<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotor;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPromotors = Promotor::count();
        $pendingPromotorsCount = Promotor::where('status_promotor', 'pending')->count();
        $totalUsers = User::count();
        $totalRevenue = DB::table('ticket_categories')
                            ->join('tickets', 'ticket_categories.id', '=', 'tickets.ticket_category_id')
                            ->sum('ticket_categories.price'); // Dummy revenue calculation

        $pendingPromotors = Promotor::where('status_promotor', 'pending')->latest()->get();

        return view('admin.dashboard', compact(
            'totalPromotors', 
            'pendingPromotorsCount', 
            'totalUsers', 
            'totalRevenue', 
            'pendingPromotors'
        ));
    }

    public function approvePromotor($id)
    {
        $promotor = Promotor::findOrFail($id);
        $promotor->update(['status_promotor' => 'active']);

        return redirect()->back()->with('success', 'Promotor ' . $promotor->company_name . ' berhasil disetujui!');
    }

    public function users()
    {
        $users = User::where('role', 'buyer')->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users_create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => 'buyer',
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users_edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users')->with('success', "Akun pengguna berhasil $status.");
    }

    public function allPromotors()
    {
        $promotors = Promotor::latest()->get();
        return view('admin.promotors', compact('promotors'));
    }

    public function togglePromotor($id)
    {
        $promotor = Promotor::findOrFail($id);
        if ($promotor->status_promotor == 'active') {
            $promotor->status_promotor = 'suspended';
        } else {
            $promotor->status_promotor = 'active';
        }
        $promotor->save();

        $status = $promotor->status_promotor == 'active' ? 'diaktifkan' : 'dinonaktifkan / disuspend';
        return redirect()->route('admin.promotors')->with('success', "Status Promotor berhasil $status.");
    }

    public function editPromotor($id)
    {
        $promotor = Promotor::with('user')->findOrFail($id);
        return view('admin.promotors_edit', compact('promotor'));
    }

    public function updatePromotor(Request $request, $id)
    {
        $promotor = Promotor::findOrFail($id);
        $user = $promotor->user;
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $promotor->company_name = $request->company_name;
        $promotor->save();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.promotors')->with('success', 'Data Promotor berhasil diperbarui.');
    }

    public function destroyPromotor($id)
    {
        $promotor = Promotor::findOrFail($id);
        $promotor->delete();
        return redirect()->route('admin.promotors')->with('success', 'Data Promotor berhasil dihapus.');
    }

    public function events()
    {
        $events = Event::with('promotor')->latest()->get();
        return view('admin.events', compact('events'));
    }

    public function createEvent()
    {
        $promotors = Promotor::where('status_promotor', 'active')->get();
        return view('admin.events_create', compact('promotors'));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'promotor_id' => 'required|exists:promotors,id',
            'event_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'max_tickets_per_nik' => 'required|integer|min:1',
            'status_event' => 'required|in:draft,active,completed',
            'description' => 'nullable|string',
        ]);

        Event::create($request->all());

        return redirect()->route('admin.events')->with('success', 'Event berhasil ditambahkan.');
    }

    public function editEvent($id)
    {
        $event = Event::findOrFail($id);
        $promotors = Promotor::where('status_promotor', 'active')->get();
        return view('admin.events_edit', compact('event', 'promotors'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        $request->validate([
            'promotor_id' => 'required|exists:promotors,id',
            'event_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'max_tickets_per_nik' => 'required|integer|min:1',
            'status_event' => 'required|in:draft,active,completed',
            'description' => 'nullable|string',
        ]);

        $event->update($request->all());

        return redirect()->route('admin.events')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroyEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.events')->with('success', 'Event berhasil dihapus.');
    }

    public function toggleEventStatus($id)
    {
        $event = Event::findOrFail($id);
        $event->status_event = $event->status_event === 'active' ? 'draft' : 'active';
        $event->save();

        $status = $event->status_event === 'active' ? 'diaktifkan' : 'dinonaktifkan (draft)';
        return redirect()->route('admin.events')->with('success', "Event berhasil $status.");
    }

    public function reports()
    {
        $totalTickets = \App\Models\Ticket::count();
        $totalUsers = User::count();
        $totalPromotors = Promotor::count();
        
        $transactions = \App\Models\Transaction::where('payment_status', 'paid')->get();
        $grossRevenue = $transactions->sum('total_price');
        $platformFee = $grossRevenue * 0.05;

        return view('admin.reports', compact(
            'totalTickets', 'totalUsers', 'totalPromotors', 'grossRevenue', 'platformFee'
        ));
    }

    public function finance()
    {
        $transactions = \App\Models\Transaction::with(['user', 'tickets.ticketCategory.event.promotor'])->latest()->get();
        $totalRevenue = $transactions->sum('total_price');
        
        return view('admin.finance', compact('totalRevenue', 'transactions'));
    }

    public function payouts()
    {
        $pendingPayouts = \App\Models\Payout::with('promotor')->where('status', 'pending')->latest()->get();
        $historyPayouts = \App\Models\Payout::with('promotor')->whereIn('status', ['completed', 'rejected'])->latest()->get();

        return view('admin.payouts', compact('pendingPayouts', 'historyPayouts'));
    }

    public function approvePayout(Request $request, $id)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $payout = \App\Models\Payout::findOrFail($id);
        
        $path = $request->file('proof_image')->store('payouts/proofs', 'public');

        $payout->update([
            'status' => 'completed',
            'proof_image_path' => $path
        ]);

        return redirect()->back()->with('success', 'Pencairan dana berhasil disetujui dan bukti telah diunggah.');
    }

    public function rejectPayout($id)
    {
        $payout = \App\Models\Payout::findOrFail($id);
        $payout->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Pencairan dana ditolak.');
    }
}
