(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            // Check if the specific form is being submitted
            if (form.id === 'booking-form') {
                console.log('Booking form is being submitted...');
                // Your custom validation logic
                const validateBooking = checkBookingValidity(); // Assume this function returns a boolean

                if (!validateBooking) {
                    event.preventDefault(); // Prevent form submission
                    event.stopPropagation(); // Stop event propagation
                    form.classList.add('was-validated'); // Optionally show validation feedback
                    return; // Exit the function
                } else {
                    const paymentMethod = $("input[name='payment_method']:checked").val();

                    if (paymentMethod === 'Cash') {
                        // If payment method is Cash, submit the form directly
                        $('#booking-form')[0].submit(); // Or use AJAX to submit the form data if needed
                    }
                }
            }

            // Default Bootstrap validation check
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });

})();


function checkBookingValidity() {
    let date = $('#day');
    let start_time = $('#start_time');
    let end_time = $('#end_time');
    let stripeOption = $('#opt2'); // Reference to Stripe payment method option
    let cardElement = $("#card-element"); // Reference to the card element
    let isValid = true;
    let errors = {
        date: '',
        start_time: '',
        end_time: '',
        combined: '',
        card: '' // New field for card validation
    };

    // Clear any previous error messages
    $('.error').remove();
    $('#err_msg').html(''); // Clear the error message container

    // Validate date
    if (!date.val()) {
        errors.date = 'Date is required.';
        isValid = false;
    } else {
        const selectedDate = new Date(date.val());
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time for comparison

        if (isNaN(selectedDate.getTime())) {
            errors.date = 'Invalid date format.';
            isValid = false;
        } else if (selectedDate < today) {
            errors.date = 'Date must be today or in the future.';
            isValid = false;
        }
    }

    // Validate start_time
    if (!start_time.val()) {
        errors.start_time = 'Start time is required.';
        isValid = false;
    }

    // Validate end_time
    if (!end_time.val()) {
        errors.end_time = 'End time is required.';
        isValid = false;
    }

    // If both start_time and end_time are provided, validate their order and time in the future
    if (start_time.val() && end_time.val()) {
        const selectedDateTime = new Date(date.val());
        const currentTime = new Date();

        const startTimeParts = start_time.val().split(':');
        const endTimeParts = end_time.val().split(':');

        const startDateTime = new Date(selectedDateTime.setHours(startTimeParts[0], startTimeParts[1]));
        const endDateTime = new Date(selectedDateTime.setHours(endTimeParts[0], endTimeParts[1]));

        if (startDateTime >= endDateTime) {
            errors.combined = 'Start time must be before end time.';
            isValid = false;
        }

        if (startDateTime < currentTime || endDateTime < currentTime) {
            errors.combined = 'Times must be in the future.';
            isValid = false;
        }
    }

    // Validate card details if Stripe is selected
    if (stripeOption.is(':checked')) {
        if (!cardElement || cardElement.hasClass("StripeElement--empty")) { // Check if card details are empty or invalid
            errors.card = 'Card details are required.';
            isValid = false;
        }
        console.log('Card Element:', cardElement, cardElement.hasClass("StripeElement--empty"));
    }

    let err_msg = '';

    // Display error messages for individual fields
    if (errors.date) {
        err_msg += `<p class="text-danger mb-2" style="font-size: 14px;">${errors.date}</p>`;
    }
    if (errors.start_time) {
        err_msg += `<p class="text-danger mb-2" style="font-size: 14px;">${errors.start_time}</p>`;
    }
    if (errors.end_time) {
        err_msg += `<p class="text-danger mb-2" style="font-size: 14px;">${errors.end_time}</p>`;
    }
    if (errors.combined) {
        err_msg += `<p class="text-danger mb-2" style="font-size: 14px;">${errors.combined}</p>`;
    }
    if (errors.card) {
        err_msg += `<p class="text-danger mb-2" style="font-size: 14px;">${errors.card}</p>`;
    }

    $('#err_msg').html(err_msg);

    // Log for debugging
    console.log('Validation Errors:', errors);

    return isValid;
}


function updateBookingForm() {

    checkBookingValidity();
    let diffInHours = '-';
    let total = '-';
    // Validate booking form
        let date = $('#day').val();
        let start_time = $('#start_time').val();
        let end_time = $('#end_time').val();
        let rate = parseFloat($('#rate').val());

        // Parse times to calculate difference
        const startTimeParts = start_time.split(':');
        const endTimeParts = end_time.split(':');

        const startDateTime = new Date(date);
        startDateTime.setHours(startTimeParts[0], startTimeParts[1], 0);

        const endDateTime = new Date(date);
        endDateTime.setHours(endTimeParts[0], endTimeParts[1], 0);

        // Calculate time difference in hours
        const diffInMs = endDateTime - startDateTime;
        diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours

        // Calculate total based on rate and difference in hours
        total = diffInHours * rate;
        total = total.toFixed(2);

        rate = (rate>0) ? rate : "-";
        total = (total>0) ? total : "-";
        diffInHours = (diffInHours>0) ? diffInHours : "-";

        $('#rate').val(rate);
        $('#total').val(total);
        $('#duration').val(diffInHours);



    // Log the hours difference and total cost
    $('.hrs_duration').html(diffInHours);
    $('.total').html(total);

    console.log('Hours difference:', diffInHours);
    console.log('Total cost:', total);
}

$(document).ready(function () {
    function checkAvailabilityFields() {
        let full_time = $('#full_time');

        let at_least_one_day = false;
        $('#days-availability .card').each(function () {
            let card = $(this);
            let selects = card.find('.form-select');

            if (selects.length === 2) { // Ensure there are exactly two select elements per card
                let startTimeSelect = $(selects[0]);
                let endTimeSelect = $(selects[1]);

                // Get values from the select elements
                let startTime = startTimeSelect.val();
                let endTime = endTimeSelect.val();

                // Remove any existing error messages
                card.find('.error-message').remove();

                // Check if both selects are non-empty
                if (startTime && endTime) {
                    // Convert time strings to Date objects for comparison
                    let startTimeDate = new Date(`1970-01-01T${startTime}`);
                    let endTimeDate = new Date(`1970-01-01T${endTime}`);

                    // Check if both times are different and start time is before end time
                    if (startTime === endTime) {
                        $('<div class="error-message text-danger">Start and end times cannot be the same.</div>').insertAfter(endTimeSelect);
                    } else if (startTimeDate >= endTimeDate) {
                        $('<div class="error-message text-danger">Start time must be before end time.</div>').insertAfter(endTimeSelect);
                    } else {
                        // If all conditions are satisfied, ensure there are no error messages
                        card.find('.error-message').remove();
                    }
                }
            }

            // Original condition: Ensure all selects in the card are non-empty
            let allSelected = true;
            selects.each(function () {
                if (!$(this).val()) {
                    allSelected = false;
                    return false; // Exit .each() if any select is empty
                }
            });

            if (allSelected) { // If all selects in the card are non-empty, set at_least_one_day flag
                at_least_one_day = true;
            }
        });


        console.log(at_least_one_day, full_time.prop('checked'));
        if (at_least_one_day || full_time.prop('checked')) {

            full_time.prop('required', false);
            $('#days-availability .form-select').each(function () {
                $(this).prop('required', false);
            })
        } else {
            full_time.prop('required', true);
            $('#days-availability .form-select').each(function () {
                $(this).prop('required', true);
            })
        }
    }
    if ($('#owner-form').length > 0) {
        $('#full_time').change(function () {
            checkAvailabilityFields();
        });
        $('#days-availability .form-select').change(function () {
            checkAvailabilityFields();
        });
        checkAvailabilityFields();
    }
})