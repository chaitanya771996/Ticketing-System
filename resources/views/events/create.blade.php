<!-- resources/views/events/create.blade.php -->
@include("header_section")
<br>
<div class="app-content">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="card-title">Create Event</div>
                </div>
                <div class="card-body">
                    <form id="createEventForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Event Title</label>
                            <input type="text" id="title" name="title" placeholder="Event Title" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Event Description</label>
                            <textarea id="description" name="description" placeholder="Event Description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Event Date</label>
                            <input type="datetime-local" id="date" name="date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="location" class="form-label">Event Location</label>
                            <input type="text" id="location" name="location" placeholder="Event Location" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="ticket_availability" class="form-label">Tickets Available</label>
                            <input type="number" id="ticket_availability" name="ticket_availability" placeholder="Tickets Available" class="form-control">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include("footer_section")
<script>
    $('#createEventForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous error messages
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid');

        $.ajax({
            url: '/events',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Event created successfully!');
                // Optionally reset the form
                $('#createEventForm')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    for (var field in errors) {
                        // Display the error message
                        var errorMessage = errors[field][0];
                        var inputField = $('[name="' + field + '"]');
                        inputField.addClass('is-invalid');
                        inputField.after('<div class="text-danger">' + errorMessage + '</div>');
                    }
                } else {
                    alert('An error occurred. Please try again later.');
                }
            }
        });
    });
</script>
