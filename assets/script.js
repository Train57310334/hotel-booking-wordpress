// wp-booking-widget/assets/script.js

document.addEventListener('DOMContentLoaded', function () {
    const checkInInput = document.querySelector('input[name="checkIn"]');
    const checkOutInput = document.querySelector('input[name="checkOut"]');

    if (checkInInput && checkOutInput) {
        // Set default dates if empty
        if (!checkInInput.value) {
            const today = new Date();
            checkInInput.valueAsDate = today;
        }

        if (!checkOutInput.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            checkOutInput.valueAsDate = tomorrow;
        }

        // Logic to ensure Check-out is after Check-in
        checkInInput.addEventListener('change', function () {
            const checkInDate = new Date(this.value);
            const checkOutDate = new Date(checkOutInput.value);

            if (checkInDate >= checkOutDate) {
                const newCheckOut = new Date(checkInDate);
                newCheckOut.setDate(newCheckOut.getDate() + 1);
                checkOutInput.valueAsDate = newCheckOut;
            }
        });
    }
});
