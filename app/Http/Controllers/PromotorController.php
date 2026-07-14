<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class PromotorController extends Controller
{
    public function dashboard()
    {
        $promotor = Auth::user()->promotor;
        if (!$promotor) {
            // Handle if a user has role promotor but no promotor record exists yet
            abort(403, 'Profil promotor Anda belum dibuat.');
        }

        $events = Event::where('promotor_id', $promotor->id)->latest()->get();
        
        $ticketsSold = \App\Models\Ticket::whereHas('ticketCategory.event', function ($q) use ($promotor) {
            $q->where('promotor_id', $promotor->id);
        })->count();

        $totalSales = \Illuminate\Support\Facades\DB::table('tickets')
            ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->join('events', 'ticket_categories.event_id', '=', 'events.id')
            ->where('events.promotor_id', $promotor->id)
            ->sum('ticket_categories.price');

        return view('promotor.dashboard', compact('events', 'promotor', 'ticketsSold', 'totalSales'));
    }

    public function createEvent()
    {
        $promotor = Auth::user()->promotor;
        if ($promotor->status_promotor !== 'active') {
            return redirect()->route('promotor.dashboard')->with('error', 'Akun Anda masih dalam status Pending. Anda tidak dapat membuat event sampai disetujui oleh Admin.');
        }
        return view('promotor.events.create');
    }

    public function storeEvent(Request $request)
    {
        $promotor = Auth::user()->promotor;
        if ($promotor->status_promotor !== 'active') {
            return redirect()->route('promotor.dashboard')->with('error', 'Akun Anda masih dalam status Pending.');
        }

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_category' => 'required|string|in:Musik,Seminar,Teater,Olahraga,Lainnya',
            'city' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'maps_url' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'event_date' => 'required|date',
            'sale_start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'max_tickets_per_nik' => 'required|integer|min:1',
            'banner_image' => 'nullable|image|max:2048',
            'seatmap_image' => 'nullable|image|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('events', 'public');
        }
        
        $seatmapPath = null;
        if ($request->hasFile('seatmap_image')) {
            $seatmapPath = $request->file('seatmap_image')->store('events/seatmaps', 'public');
        }

        $event = Event::create([
            'promotor_id' => $promotor->id,
            'event_name' => $validated['event_name'],
            'event_category' => $validated['event_category'],
            'city' => $validated['city'],
            'location' => $validated['location'],
            'maps_url' => $validated['maps_url'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'event_date' => $validated['event_date'],
            'sale_start_date' => $validated['sale_start_date'],
            'description' => $validated['description'],
            'max_tickets_per_nik' => $validated['max_tickets_per_nik'],
            'banner_image_path' => $imagePath,
            'seatmap_image_path' => $seatmapPath,
            'status_event' => 'active'
        ]);

        return redirect()->route('promotor.events.tickets', $event->id)->with('success', 'Event berhasil dibuat, silakan tambahkan kategori tiket!');
    }

    public function editEvent($id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        return view('promotor.events.edit', compact('event'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_category' => 'required|string|in:Musik,Seminar,Teater,Olahraga,Lainnya',
            'city' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'maps_url' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'event_date' => 'required|date',
            'sale_start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'max_tickets_per_nik' => 'required|integer|min:1',
            'banner_image' => 'nullable|image|max:2048',
            'seatmap_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('events', 'public');
            $event->banner_image_path = $imagePath;
        }

        if ($request->hasFile('seatmap_image')) {
            $seatmapPath = $request->file('seatmap_image')->store('events/seatmaps', 'public');
            $event->seatmap_image_path = $seatmapPath;
        }

        $event->update([
            'event_name' => $validated['event_name'],
            'event_category' => $validated['event_category'],
            'city' => $validated['city'],
            'location' => $validated['location'],
            'maps_url' => $validated['maps_url'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'event_date' => $validated['event_date'],
            'sale_start_date' => $validated['sale_start_date'],
            'description' => $validated['description'],
            'max_tickets_per_nik' => $validated['max_tickets_per_nik'],
        ]);

        return redirect()->route('promotor.events.index')->with('success', 'Event berhasil diperbarui!');
    }

    public function manageTickets($id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        $ticketCategories = $event->ticketCategories;
        return view('promotor.events.tickets', compact('event', 'ticketCategories'));
    }

    public function storeTicket(Request $request, $id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'total_quota' => 'required|integer|min:1'
        ]);

        TicketCategory::create([
            'event_id' => $event->id,
            'category_name' => $validated['category_name'],
            'price' => $validated['price'],
            'total_quota' => $validated['total_quota'],
            'remaining_quota' => $validated['total_quota']
        ]);

        return redirect()->back()->with('success', 'Kategori tiket berhasil ditambahkan!');
    }

    public function updateTicket(Request $request, $id, $ticket_id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        $ticketCategory = TicketCategory::where('event_id', $event->id)->findOrFail($ticket_id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'total_quota' => 'required|integer|min:1'
        ]);

        $soldTickets = $ticketCategory->total_quota - $ticketCategory->remaining_quota;
        if ($validated['total_quota'] < $soldTickets) {
            return redirect()->back()->with('error', "Kuota total tidak boleh kurang dari tiket yang sudah terjual ($soldTickets tiket).");
        }

        $ticketCategory->update([
            'category_name' => $validated['category_name'],
            'price' => $validated['price'],
            'total_quota' => $validated['total_quota'],
            'remaining_quota' => $validated['total_quota'] - $soldTickets
        ]);

        return redirect()->back()->with('success', 'Kategori tiket berhasil diperbarui!');
    }

    public function destroyTicket($id, $ticket_id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        $ticketCategory = TicketCategory::where('event_id', $event->id)->findOrFail($ticket_id);

        $soldTickets = $ticketCategory->total_quota - $ticketCategory->remaining_quota;
        if ($soldTickets > 0) {
            return redirect()->back()->with('error', 'Tiket ini tidak dapat dihapus karena sudah ada yang terjual.');
        }

        $ticketCategory->delete();
        return redirect()->back()->with('success', 'Kategori tiket berhasil dihapus.');
    }

    public function events()
    {
        $promotor = Auth::user()->promotor;
        $events = Event::where('promotor_id', $promotor->id)->latest()->get();
        return view('promotor.events.index', compact('events'));
    }

    public function eventCheckins($id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        
        $tickets = \App\Models\Ticket::whereHas('ticketCategory', function ($q) use ($id) {
            $q->where('event_id', $id);
        })->where('checkin_status', 'checked_in')->latest('checked_in_at')->get();
        
        return view('promotor.events.checkins', compact('event', 'tickets'));
    }

    public function transactions()
    {
        $promotorId = Auth::user()->promotor->id;
        
        // Ambil transaksi yang tiketnya dimiliki oleh event milik promotor ini
        $transactions = Transaction::whereHas('tickets.ticketCategory.event', function ($q) use ($promotorId) {
            $q->where('promotor_id', $promotorId);
        })
        ->with(['user', 'tickets.ticketCategory.event'])
        ->latest()
        ->get();

        return view('promotor.transactions', compact('transactions'));
    }

    public function report()
    {
        $promotorId = Auth::user()->promotor->id;
        
        $events = Event::where('promotor_id', $promotorId)->get();
        
        $reportData = $events->map(function($event) {
            $totalTicketsSold = \App\Models\Ticket::whereHas('ticketCategory', function($q) use($event) {
                $q->where('event_id', $event->id);
            })->whereHas('transaction', function($q) {
                $q->where('payment_status', 'paid');
            })->count();
            
            $totalRevenue = \App\Models\Ticket::whereHas('ticketCategory', function($q) use($event) {
                $q->where('event_id', $event->id);
            })->whereHas('transaction', function($q) {
                $q->where('payment_status', 'paid');
            })->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
              ->sum('ticket_categories.price');
            
            return (object) [
                'event_name' => $event->event_name,
                'event_date' => $event->event_date,
                'total_tickets_sold' => $totalTicketsSold,
                'total_revenue' => $totalRevenue,
            ];
        });

        return view('promotor.report', compact('reportData'));
    }

    public function payouts()
    {
        $promotor = Auth::user()->promotor;
        
        $totalSales = \Illuminate\Support\Facades\DB::table('tickets')
            ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->join('events', 'ticket_categories.event_id', '=', 'events.id')
            ->where('events.promotor_id', $promotor->id)
            ->sum('ticket_categories.price');
            
        $netSales = $totalSales * 0.95; // 95% is for promotor
        
        $payouts = \App\Models\Payout::where('promotor_id', $promotor->id)->latest()->get();
        $totalWithdrawn = $payouts->whereIn('status', ['pending', 'completed'])->sum('amount');
        
        $availableBalance = $netSales - $totalWithdrawn;
        
        return view('promotor.payouts', compact('netSales', 'totalWithdrawn', 'availableBalance', 'payouts'));
    }

    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
        ]);

        $promotor = Auth::user()->promotor;
        
        $totalSales = \Illuminate\Support\Facades\DB::table('tickets')
            ->join('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->join('events', 'ticket_categories.event_id', '=', 'events.id')
            ->where('events.promotor_id', $promotor->id)
            ->sum('ticket_categories.price');
            
        $netSales = $totalSales * 0.95;
        $totalWithdrawn = \App\Models\Payout::where('promotor_id', $promotor->id)
                            ->whereIn('status', ['pending', 'completed'])
                            ->sum('amount');
        
        $availableBalance = $netSales - $totalWithdrawn;

        if ($request->amount > $availableBalance) {
            return redirect()->back()->with('error', 'Nominal penarikan melebihi saldo yang tersedia.');
        }

        \App\Models\Payout::create([
            'promotor_id' => $promotor->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Permintaan tarik dana berhasil diajukan! Admin akan memprosesnya dalam 1-3 hari kerja.');
    }

    public function settings()
    {
        $promotor = Auth::user()->promotor;
        return view('promotor.settings', compact('promotor'));
    }

    public function destroyEvent($id)
    {
        $event = Event::where('promotor_id', Auth::user()->promotor->id)->findOrFail($id);
        
        if ($event->banner_image_path) {
            Storage::disk('public')->delete($event->banner_image_path);
        }
        
        if ($event->seatmap_image_path) {
            Storage::disk('public')->delete($event->seatmap_image_path);
        }
        
        $event->delete();
        
        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }

    public function scanner()
    {
        return view('promotor.scanner');
    }

    public function verifyScanner(Request $request)
    {
        $request->validate([
            'qr_payload' => 'required|string'
        ]);

        $payload = $request->qr_payload;
        $ticket = \App\Models\Ticket::with('ticketCategory.event.promotor')->where('qr_code_payload', $payload)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak ditemukan atau QR Code tidak valid.'
            ]);
        }

        $promotor = Auth::user()->promotor;
        if ($ticket->ticketCategory->event->promotor_id !== $promotor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: Tiket ini bukan untuk acara Anda.'
            ]);
        }

        if ($ticket->checkin_status === 'checked_in') {
            return response()->json([
                'status' => 'warning',
                'message' => 'Tiket ini SUDAH DIGUNAKAN sebelumnya pada ' . date('d M Y H:i', strtotime($ticket->checked_in_at)) . '.',
                'ticket_info' => [
                    'name' => $ticket->name_holder,
                    'event' => $ticket->ticketCategory->event->event_name,
                    'category' => $ticket->ticketCategory->name
                ]
            ]);
        }

        $ticket->checkin_status = 'checked_in';
        $ticket->checked_in_at = now();
        $ticket->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in BERHASIL! Silakan masuk.',
            'ticket_info' => [
                'name' => $ticket->name_holder,
                'event' => $ticket->ticketCategory->event->event_name,
                'category' => $ticket->ticketCategory->name
            ]
        ]);
    }
}
