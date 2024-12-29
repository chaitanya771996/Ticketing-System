<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// default routes
Route::get('/', function () {
    // return view('welcome');
    return redirect('/login');
});

// dashboard route post login 
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'attendee') {
        // Redirect attendees to a different page
        return redirect()->route('attendee.home');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// profile validator
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// events 
Route::resource('events', EventController::class);
Route::patch('/events/{id}/status', [EventController::class, 'updateStatus']);
Route::post('events/{event}/buy-tickets', [EventController::class, 'buyTickets']);

Route::get('/home', [EventController::class, 'attendeeHome'])->name('attendee.home');
Route::get('/search', [EventController::class, 'search'])->name('events.search');

Route::get('/get-ticket-price', [TicketController::class, 'getTicketPrice']);

Route::post('/store-ticket-details', [EventController::class, 'storeTicketDetails']);

Route::get('/tickets', [TicketController::class, 'getBookings'])->name('tickets.index');
