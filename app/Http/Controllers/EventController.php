<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\RazorpayHelper;

class EventController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            $events = Event::where('user_id', auth()->id())->get();
            return response()->json(['events' => $events]);
        }
        
        return view('events.view');
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'location' => 'required',
            'ticket_availability' => 'required|integer',
        ]);

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'ticket_availability' => $request->ticket_availability,
        ]);

        return response()->json(['event' => $event]);
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        // return view('events.edit', compact('event'));
        return response()->json(['event' => $event]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'location' => 'required',
            'ticket_availability' => 'required|integer',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

        return response()->json(['event' => $event]);
    }

    public function updateStatus(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $event->update([
            'status' => $request->status, // Expect 'active' or 'canceled'
        ]);

        return response()->json(['message' => 'Event status updated successfully!']);
    }


    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return response()->json(['message' => 'Event canceled successfully!']);
    }

    // public function cancel($id)
    // {
    //     $event = Event::findOrFail($id);
    //     $event->delete();

    //     return response()->json(['message' => 'Event canceled']);
    // }

    // public function search(Request $request)
    // {
    //     $events = Event::where('title', 'like', "%{$request->keyword}%")
    //         ->where('location', 'like', "%{$request->location}%")
    //         ->whereDate('date', '>=', $request->date)
    //         ->get();
        
    //     return view('events.index', compact('events'));
    // }

    public function buyTickets(Request $request, RazorpayHelper $razorpayHelper)
    {
        $event = Event::findOrFail($request->event_id);
        $order = $razorpayHelper->createOrder($event->ticket_availability);

        return response()->json(['order' => $order]);
    }

    public function attendeeHome(Request $request) {
        $events = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->paginate(9); // Paginate with 9 items per page

        return view('attendee_home', compact('events'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $events = Event::where('title', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->orWhere('location', 'like', "%$query%")
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->paginate(9);

        return view('attendee_home', compact('events', 'query'));
    }

    public function storeTicketDetails(Request $request)
    {
        // Get the data from the frontend
        $paymentId = $request->input('payment_id') ?? 'dummy_12345';
        $totalPrice = $request->input('total_price');
        $quantity = $request->input('quantity');
        $eventType = $request->input('event_type');
        $eventParts = explode('_', $eventType);
        $eventNumber = $eventParts[1];  // This will give you '2'
    
        // Optionally, you can convert it to an integer if needed
        $eventNumber = (int) $eventNumber;
        // Retrieve event and ticket details
        $event = Event::where('id', $eventNumber)->first();
        
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        // Check if there are enough tickets available
        if ($event->ticket_availability < $quantity) {
            return response()->json(['error' => 'Not enough tickets available'], 400);
        }

        // Deduct ticket availability based on the quantity booked
        $event->ticket_availability -= $quantity;
        $event->user_id = auth()->id();  // Assuming you're storing the user_id of the logged-in user
        $event->save(); // Save the event with updated availability

        // Store booking details in Tickets table
        $ticket = new Ticket();
        $ticket->event_id = $event->id;
        $ticket->user_id = auth()->id(); // Assuming the user is logged in
        $ticket->quantity = $quantity;
        $ticket->total_price = $totalPrice;
        $ticket->payment_id = $paymentId;
        $ticket->save();

        // Return success response
        return response()->json(['success' => 'Booking successful'], 200);
    }

}

