<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Ticket;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            $transaction = Transaction::where('invoice_number', $request->order_id)->first();
            
            if ($transaction) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaction->update(['payment_status' => 'paid']);
                } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
                    $transaction->update(['payment_status' => 'expired']);
                    
                    // Kembalikan kuota tiket dan hapus tiket yang pending
                    $tickets = Ticket::where('transaction_id', $transaction->id)->get();
                    foreach ($tickets as $ticket) {
                        $ticket->ticketCategory->increment('remaining_quota', 1);
                        $ticket->delete();
                    }
                }
            }
        }

        return response()->json(['message' => 'Success']);
    }
}
