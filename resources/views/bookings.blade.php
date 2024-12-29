@include('header_section')

<div class="app-content-header">

    <div class="container-fluid">

    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0"></h3></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Booking</li>
        </ol>
        </div>
    </div>
    </div>
</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
            
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Booking Summary</h3>
                    </div>
                    <div class="card-body">
                        @if(count($bookings) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        {{-- <th>Ticket Type</th> --}}
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Payment Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->user->name }}</td>
                                            {{-- <td>{{ $booking->ticket_type }}</td> --}}
                                            <td>{{ $booking->quantity }}</td>
                                            <td>₹{{ $booking->total_price }}</td>
                                            <td>{{ ($booking->payment_id) ? 'Success' : 'Failed' }}</td>
                                            <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No bookings available for this event.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer_section')