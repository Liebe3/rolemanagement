<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = match(Auth::user()->role) {
            'admin' => Ticket::all(),
            'support' => Ticket::where('assigned_to', Auth::id())->get(),
            default => Ticket::where('user_id', Auth::id())->get(),
        };
        
        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'priority' => 'required|in:low,medium,high'
        ]);

        $ticket = Auth::user()->tickets()->create($validated);

        // Send notification
        // event(new TicketCreated($ticket));

        return redirect()->route('tickets.index');
    }
}