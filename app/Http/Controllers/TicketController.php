<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function getTicketPrice(Request $request)
    {
        // Sample ticket prices based on event type and ticket type
        $eventTicketPrices = [
            $request->event_type => [
                'early_bird' => 100, 
                'regular' => 150, 
                'vip' => 200
            ]
        ];

        // Get ticket type, quantity, and event type from the request
        $ticketType = $request->ticket_type;
        $quantity = (int) $request->quantity;
        $eventType = $request->event_type;

        // Validate eventType and ticketType
        if (!isset($eventTicketPrices[$eventType])) {
            return response()->json(['error' => 'Invalid event type'], 400);
        }

        if (!isset($eventTicketPrices[$eventType][$ticketType])) {
            return response()->json(['error' => 'Invalid ticket type for this event'], 400);
        }

        // Calculate the price
        $price = $eventTicketPrices[$eventType][$ticketType];
        $total = $price * $quantity;

        // Return the updated price and total
        return response()->json([
            'price' => number_format($price, 2),
            'total' => number_format($total, 2)
        ]);
    }
    
    // Function to get all booked tickets for a specific event
    public function getBookings()
    {
        if (auth()->user()->role === 'attendee') {
            $userId = auth()->user()->id;
            $bookings = Ticket::where('user_id', $userId)->get();
        } else {
            $bookings = Ticket::get();
        }
        // Return the bookings as a view or JSON response
        return view('bookings', compact('bookings'));
    }
}

