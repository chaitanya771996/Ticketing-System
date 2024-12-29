<!-- resources/views/events/view.blade.php -->
@include("header_section")
<br>
<div class="app-content">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <div class="card-title">View Events</div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Title</th>
                                {{-- <th>Description</th> --}}
                                <th>Date</th>
                                <th>Location</th>
                                <th>Event Available</th>
                                <th>Event Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="eventTable">
                            <!-- Rows will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEventForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="event_id" id="event_id">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Event Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_date" class="form-label">Date</label>
                        <input type="datetime-local" name="date" id="edit_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_location" class="form-label">Location</label>
                        <input type="text" name="location" id="edit_location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ticket_availability" class="form-label">Tickets Available</label>
                        <input type="number" name="ticket_availability" id="edit_ticket_availability" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include("footer_section")

<script>
    $(document).ready(function () {
        // Load events
        function loadEvents() {
            $.ajax({
                url: '/events',
                method: 'GET',
                success: function (response) {
                    let eventRows = '';
                    response.events.forEach((event, index) => {
                        eventRows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${event.title}</td>
                                
                                <td>${event.date}</td>
                                <td>${event.location}</td>
                                <td>${event.ticket_availability}</td>
                                <td>${event.status}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editEventBtn" data-id="${event.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm cancelEventBtn" data-id="${event.id}">Cancel</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#eventTable').html(eventRows);
                }
            });
        }

        loadEvents();

        // Open edit modal and populate fields
        $(document).on('click', '.editEventBtn', function (e) {
            e.preventDefault();
            const eventId = $(this).data('id');
            $.ajax({
                url: `/events/${eventId}/edit`,
                method: 'GET',
                success: function (response) {
                    const eventDate = new Date(response.event.date).toISOString().slice(0, 16);
                    $('#event_id').val(response.event.id);
                    $('#edit_title').val(response.event.title);
                    $('#edit_description').val(response.event.description);
                    $('#edit_date').val(eventDate);
                    $('#edit_location').val(response.event.location);
                    $('#edit_ticket_availability').val(response.event.ticket_availability);
                    $('#editEventModal').modal('show');
                }
            });
        });

        // Update event
        $('#editEventForm').on('submit', function (e) {
            e.preventDefault();
            const eventId = $('#event_id').val();
            const formData = $(this).serialize();
            $.ajax({
                url: `/events/${eventId}`,
                method: 'PUT',
                data: formData,
                success: function (response) {
                    $('#editEventModal').modal('hide');
                    loadEvents();
                    showAlert('Event updated successfully!', 'success');
                }
            });
        });

        // Cancel event
        $(document).on('click', '.cancelEventBtn', function (e) {
            e.preventDefault();

            const eventId = $(this).data('id');
            if (confirm('Are you sure you want to cancel this event?')) {
                $.ajax({
                    url: `/events/${eventId}/status`,
                    method: 'PATCH',
                    data: {
                        status: 'canceled'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        loadEvents(); // Reload the event list
                        showAlert(response.message, 'danger');
                    },
                    error: function (xhr) {
                        showAlert('Failed to update event status. Please try again.', 'danger');
                    }
                });
            }
        });


        // Show alert
        function showAlert(message, type) {
            $('#alertMessage').text(message).removeClass('d-none alert-danger alert-success').addClass(`alert-${type}`);
            setTimeout(() => $('#alertMessage').addClass('d-none'), 3000);
        }
    });
</script>