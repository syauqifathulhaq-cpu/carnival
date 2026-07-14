<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PembeliController extends Controller
{
    public function home()
    {
        $events = Event::where('status_event', 'active')
                       ->whereHas('promotor', function ($q) {
                           $q->where('status_promotor', 'active');
                       })
                       ->latest()->get();
        return view('pembeli.home', compact('events'));
    }

    public function explore(Request $request)
    {
        $query = Event::where('status_event', 'active')
                      ->whereHas('promotor', function ($q) {
                          $q->where('status_promotor', 'active');
                      });
                      
        if ($request->has('q') && $request->q != '') {
            $query->where('event_name', 'like', '%' . $request->q . '%')
                  ->orWhere('city', 'like', '%' . $request->q . '%');
        }
        
        $events = $query->latest()->get();
        return view('pembeli.explore', compact('events'));
    }

    public function eventDetail($id)
    {
        $event = Event::where('status_event', 'active')
                      ->whereHas('promotor', function ($q) {
                          $q->where('status_promotor', 'active');
                      })
                      ->with('ticketCategories')
                      ->findOrFail($id);
        
        return view('pembeli.event-detail', compact('event'));
    }

    public function checkout(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        // The user selected quantities for each category, passed via query params or session.
        // For simplicity, we'll expect it via URL like ?qty[category_id]=2
        $quantities = $request->input('qty', []);
        
        // Filter out zero or empty quantities
        $quantities = array_filter($quantities, function($val) {
            return $val > 0;
        });

        if (empty($quantities)) {
            return redirect()->route('pembeli.event', $id)->with('error', 'Silakan pilih minimal 1 tiket.');
        }

        $categories = TicketCategory::whereIn('id', array_keys($quantities))->get();
        
        return view('pembeli.checkout', compact('event', 'categories', 'quantities'));
    }

    public function processCheckout(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();
        
        // Validasi input
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*.category_id' => 'required|exists:ticket_categories,id',
            'tickets.*.name' => 'required|string|min:3|max:255',
            'tickets.*.nik' => 'required|string|size:16|regex:/^[0-9]+$/',
            'tickets.*.phone' => 'required|string|min:10|max:20',
        ], [
            'tickets.*.name.min' => 'Nama pada tiket minimal 3 karakter.',
            'tickets.*.nik.size' => 'NIK harus tepat 16 digit.',
            'tickets.*.nik.regex' => 'NIK hanya boleh berisi angka.',
            'tickets.*.phone.min' => 'Nomor HP tidak valid.',
        ]);

        $ticketsData = $request->input('tickets');
        
        // Cek kuota
        $categoryCounts = [];
        foreach ($ticketsData as $ticket) {
            $catId = $ticket['category_id'];
            if (!isset($categoryCounts[$catId])) {
                $categoryCounts[$catId] = 0;
            }
            $categoryCounts[$catId]++;
        }

        $totalPrice = 0;
        $categoryModels = [];

        foreach ($categoryCounts as $catId => $qty) {
            $category = TicketCategory::find($catId);
            if ($category->remaining_quota < $qty) {
                return redirect()->back()->with('error', 'Maaf, kuota untuk kategori ' . $category->category_name . ' tidak mencukupi.');
            }
            $totalPrice += ($category->price * $qty);
            $categoryModels[$catId] = $category;
        }

        // Cek total tiket per NIK untuk event ini (menggabungkan pesanan saat ini dan pesanan sebelumnya)
        $nikCounts = [];
        foreach ($ticketsData as $ticket) {
            $nik = $ticket['nik'];
            if (!isset($nikCounts[$nik])) {
                $nikCounts[$nik] = 0;
            }
            $nikCounts[$nik]++;
        }

        foreach ($nikCounts as $nik => $count) {
            // Hitung tiket yang sudah dibeli sebelumnya dengan NIK ini untuk event yang sama
            $existingTicketsCount = Ticket::where('nik_holder', $nik)
                ->whereHas('ticketCategory', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })->count();

            if (($existingTicketsCount + $count) > $event->max_tickets_per_nik) {
                return redirect()->back()->with('error', "NIK {$nik} telah melebihi batas maksimum pembelian ({$event->max_tickets_per_nik} tiket) untuk event ini.");
            }
        }

        // Buat Transaksi
        $invoiceNumber = 'INV-' . date('YmdHis') . '-' . strtoupper(Str::random(5));
        
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'invoice_number' => $invoiceNumber,
            'total_price' => $totalPrice,
            'payment_status' => 'pending', 
            'transaction_date' => now(),
        ]);

        // Buat Tiket & Kurangi Kuota
        foreach ($ticketsData as $ticket) {
            $cat = $categoryModels[$ticket['category_id']];
            
            Ticket::create([
                'transaction_id' => $transaction->id,
                'ticket_category_id' => $cat->id,
                'nik_holder' => $ticket['nik'],
                'name_holder' => $ticket['name'],
                'phone_holder' => $ticket['phone'],
                'qr_code_payload' => $transaction->invoice_number . '-TKT-' . strtoupper(Str::random(8)),
                'checkin_status' => 'not_used',
            ]);

            $cat->decrement('remaining_quota', 1);
        }

        // Set konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $params = array(
            'transaction_details' => array(
                'order_id' => $transaction->invoice_number,
                'gross_amount' => $transaction->total_price,
            ),
            'customer_details' => array(
                'first_name' => explode(' ', $user->name)[0],
                'email' => $user->email,
            ),
        );

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            
            return redirect()->route('pembeli.payment', $transaction->id);
        } catch (\Exception $e) {
            // Jika gagal mendapatkan snap token
            return redirect()->route('pembeli.tickets')->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function payment($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);
        
        // Cek status real-time ke Midtrans jika masih pending
        if ($transaction->payment_status == 'pending') {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            try {
                $status = \Midtrans\Transaction::status($transaction->invoice_number);
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $transaction->update(['payment_status' => 'paid']);
                } elseif ($status->transaction_status == 'expire' || $status->transaction_status == 'cancel' || $status->transaction_status == 'deny') {
                    $transaction->update(['payment_status' => 'expired']);
                }
            } catch (\Exception $e) {
                // Abaikan jika tidak ditemukan
            }
        }

        // Jika sudah dibayar, langsung ke tiket
        if ($transaction->payment_status == 'paid') {
            return redirect()->route('pembeli.tickets')->with('success', 'Pembayaran berhasil!');
        }

        // Jika snap_token kosong (mungkin karena error sebelumnya), generate ulang
        if (empty($transaction->snap_token)) {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = array(
                'transaction_details' => array(
                    'order_id' => $transaction->invoice_number,
                    'gross_amount' => $transaction->total_price,
                ),
                'customer_details' => array(
                    'first_name' => explode(' ', Auth::user()->name)[0],
                    'email' => Auth::user()->email,
                ),
            );

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return redirect()->route('pembeli.tickets')->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }
        }
        
        return view('pembeli.payment', compact('transaction'));
    }

    public function tickets()
    {
        $user = Auth::user();
        $allTransactions = Transaction::with(['tickets.ticketCategory.event'])
                            ->where('user_id', $user->id)
                            ->latest()
                            ->get();
                            
        // Cek status transaksi pending (Fallback jika Webhook tidak jalan di localhost)
        foreach ($allTransactions as $trx) {
            if ($trx->payment_status == 'pending') {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                try {
                    $status = \Midtrans\Transaction::status($trx->invoice_number);
                    if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                        $trx->update(['payment_status' => 'paid']);
                    } elseif ($status->transaction_status == 'expire' || $status->transaction_status == 'cancel' || $status->transaction_status == 'deny') {
                        $trx->update(['payment_status' => 'expired']);
                        $tickets = Ticket::where('transaction_id', $trx->id)->get();
                        foreach ($tickets as $ticket) {
                            $ticket->ticketCategory->increment('remaining_quota', 1);
                            $ticket->delete();
                        }
                        // Refresh relation so isEmpty() check works accurately
                        $trx->load('tickets');
                    }
                } catch (\Exception $e) {
                    // Abaikan
                }
            }
        }

        // Active = has tickets AND event is today or future
        $transactions = $allTransactions->filter(function($trx) {
            if ($trx->tickets->isEmpty()) return false;
            $eventDate = $trx->tickets->first()->ticketCategory->event->event_date;
            return \Carbon\Carbon::parse($eventDate)->startOfDay()->gte(now()->startOfDay());
        });

        return view('pembeli.tickets', compact('transactions'));
    }

    public function history()
    {
        $user = Auth::user();
        $allTransactions = Transaction::with(['tickets.ticketCategory.event'])
                            ->where('user_id', $user->id)
                            ->latest()
                            ->get();
                            
        // Cek status transaksi pending
        foreach ($allTransactions as $trx) {
            if ($trx->payment_status == 'pending') {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                try {
                    $status = \Midtrans\Transaction::status($trx->invoice_number);
                    if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                        $trx->update(['payment_status' => 'paid']);
                    } elseif ($status->transaction_status == 'expire' || $status->transaction_status == 'cancel' || $status->transaction_status == 'deny') {
                        $trx->update(['payment_status' => 'expired']);
                        $tickets = Ticket::where('transaction_id', $trx->id)->get();
                        foreach ($tickets as $ticket) {
                            $ticket->ticketCategory->increment('remaining_quota', 1);
                            $ticket->delete();
                        }
                        $trx->load('tickets');
                    }
                } catch (\Exception $e) {
                    // Abaikan
                }
            }
        }

        // History = expired (no tickets) OR event is past
        $transactions = $allTransactions->filter(function($trx) {
            if ($trx->tickets->isEmpty()) return true;
            $eventDate = $trx->tickets->first()->ticketCategory->event->event_date;
            return \Carbon\Carbon::parse($eventDate)->startOfDay()->lt(now()->startOfDay());
        });

        return view('pembeli.history', compact('transactions'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('pembeli.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->back()->with('success', 'Pengaturan profil berhasil diperbarui.');
    }
}
