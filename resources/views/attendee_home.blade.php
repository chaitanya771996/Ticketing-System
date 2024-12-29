@include('header_section')

<div class="app-content-header">

    <div class="container-fluid">

    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0"></h3></div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Events</li>
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
            <h3 class="card-title">Events</h3>
            </div>
            <div class="card-body">
                <div class="container mt-4">
                    <form action="{{ route('events.search') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Search events" value="{{ request('query') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                
                    <div class="row">
                        @foreach ($events as $event)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="card-text">{{ $event->description }}</p>
                                        <p class="card-text">
                                            <strong>Date:</strong> {{ $event->date }}<br>
                                            <strong>Location:</strong> {{ $event->location }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Tickets Available:</strong> {{ $event->ticket_availability }}
                                        </p>
                                        <a href="#" class="btn btn-success book-now" id="bookNowButton" data-event-id="event_{{$event->id}}" data-event-name="Event {{$event->id}}">Book Now</a>
                                        <!-- Bootstrap Modal for Ticket Booking -->
                                        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="ticketModalLabel">Select Ticket Type</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Ticket Types (Early Bird, Regular, VIP) -->
                                                        <div class="mb-3">
                                                            <label for="ticketType" class="form-label">Ticket Type</label>
                                                            <select id="ticketType" class="form-select">
                                                                <option value="early_bird">Early Bird</option>
                                                                <option value="regular">Regular</option>
                                                                <option value="vip">VIP</option>
                                                            </select>
                                                        </div>

                                                        <!-- Price Display -->
                                                        <div id="ticketPrice" class="mb-3">Price: ₹0.00</div>

                                                        <!-- Quantity Input -->
                                                        <div class="mb-3">
                                                            <label for="quantity" class="form-label">Quantity</label>
                                                            <input type="number" id="quantity" class="form-control" value="1" min="1">
                                                        </div>

                                                        <!-- Total Price Display -->
                                                        <div id="totalPrice" class="mb-3">Total: ₹0.00</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" id="confirmBooking" class="btn btn-primary">Confirm Booking</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                
                    <div class="d-flex justify-content-center">
                        {{ $events->links() }}
                    </div>    
                </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>

<!-- Booking Confirmation Popup -->
<div class="modal fade" id="bookingConfirmationPopup" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmationModalLabel">Booking Confirmed!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Your booking has been successfully confirmed! Thank you for choosing us.</p>
          <p>We have received your payment, and your tickets are now confirmed.</p>
          <p>You will receive a confirmation email shortly.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <a href="/booking-details" class="btn btn-primary">View Booking Details</a>
        </div>
      </div>
    </div>
  </div>
  


@include('footer_section')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    // Open Modal on Button Click
    $('.book-now').click(function() {
        const eventId = $(this).data('event-id');  // Get event ID from button
        const eventName = $(this).data('event-name');  // Get event name or other details
        
        // Show the modal
        const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
        ticketModal.show();

        // Store the event ID (for Razorpay and booking logic)
        window.selectedEventType = eventId;

        // Optionally update modal title with the event name
        $('#ticketModalLabel').text(`Select Ticket Type for ${eventName}`);

        // Reset modal data on open
        resetModalData();
    });

    // Reset modal data when it is opened
    function resetModalData() {
        $('#ticketType').val('early_bird');
        $('#ticketPrice').text('Price: ₹0.00');
        $('#totalPrice').text('Total: ₹0.00');
        $('#quantity').val(1);
    }

    // Ticket Price Mapping (Initial Setup)
    const ticketPrices = {
        'early_bird': 100,  // Example prices
        'regular': 150,
        'vip': 200
    };

    // AJAX call to update price and total
    function updatePrice() {
        const ticketType = $('#ticketType').val();
        const quantity = parseInt($('#quantity').val());
        const eventType = window.selectedEventType; // Use the selected event type

        // Send AJAX request
        $.ajax({
            url: '/get-ticket-price', // Your Laravel endpoint
            method: 'GET',
            data: {
                ticket_type: ticketType,
                quantity: quantity,
                event_type: eventType  // Include the event type in the request
            },
            success: function(response) {

                let formattedTotal = response.total.replace(/,/g, '');  // Remove commas
                formattedTotal = parseFloat(formattedTotal);  // Convert to float

                // Optionally, remove decimals (if you want an integer value)
                formattedTotal = Math.round(formattedTotal);  // Round to nearest integer if needed

                // Update the UI with the new price and total
                $('#ticketPrice').text('Price: ₹' + response.price);
                $('#totalPrice').text('Total: ₹' + formattedTotal); 
            }
        });
    }

    // Event listeners for changing ticket type or quantity
    $('#ticketType').change(updatePrice);
    $('#quantity').on('input', updatePrice);

    $('#confirmBooking').click(function() {
        const ticketType = $('#ticketType').val();
        const quantity = parseInt($('#quantity').val());
        const eventType = window.selectedEventType;
        const totalPrice = parseFloat($('#totalPrice').text().replace('Total: ₹', '').trim());

        console.log(ticketType, quantity, eventType, totalPrice);

        // Ensure total price is correctly formatted (in paise, Razorpay expects an integer)
        const amountInPaise = Math.round(totalPrice * 100);

        // Prepare Razorpay options
        // const options = {
        //     "key": "YOUR_RAZORPAY_KEY", // Replace with your Razorpay key
        //     "amount": amountInPaise, // Amount in paise (multiply by 100)
        //     "currency": "INR", // Currency, change if needed
        //     "name": "Your Event Name",  // Event name
        //     "description": `Booking for ${ticketType}`,
        //     "image": "YOUR_LOGO_URL",  // Optionally, add your logo
        //     "handler": function(response) {
        //         // After successful payment, store ticket details and update availability
        //         storeBookingDetails(response.razorpay_payment_id, totalPrice, quantity, eventType);
        //     },
        //     "prefill": {
        //         "name": "{{ auth()->user()->name }}",  // Prefill the user's name
        //         "email": "user@example.com",  // Prefill the user's email
        //         "contact": "1234567890"  // User's contact if available
        //     },
        //     "theme": {
        //         "color": "#F37254" // Optional theme color
        //     }
        // };

        // // Trigger Razorpay payment
        // const rzp1 = new Razorpay(options);
        // rzp1.open();
        var razorpay_payment_id = null;
        storeBookingDetails(razorpay_payment_id, totalPrice, quantity, eventType);
    });

    // Function to store booking details after successful payment
    function storeBookingDetails(paymentId, totalPrice, quantity, eventType) {
        $.ajax({
            url: '/store-ticket-details', // Your Laravel endpoint to store ticket details
            method: 'POST',
            data: {
                payment_id: paymentId,
                total_price: totalPrice,
                quantity: quantity,
                event_type: eventType,
                _token: "{{ csrf_token() }}" // CSRF token for security
            },
            success: function(response) {
                $('#ticketModal').modal('hide');  // Replace 'yourModalId' with the actual modal ID
            
                window.location.reload();
            },
            error: function(error) {
                // Error handling
                alert("Error in storing booking details.");
            }
        });
    }

</script>