function isPolishHoliday(date) {
    // Array of Polish holidays (dates are in 'YYYY-MM-DD' format)
    var polishHolidays = [
        '2023-01-01', '2023-01-06', '2023-04-17', '2023-05-01', '2023-05-03', '2023-06-15',
        '2023-08-15', '2023-11-01', '2023-11-11', '2023-12-25', '2023-12-26',
        // Add more holidays for other years as needed
    ];

    // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
    var dayOfWeek = date.getDay();

    // Format the date as 'YYYY-MM-DD'
    var formattedDate = date.toISOString().slice(0, 10);

    // Check if the date is a weekend day (Saturday or Sunday)
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        return true; // It's a weekend
    }

    // Check if the date is a Polish holiday
    if (polishHolidays.includes(formattedDate)) {
        return true; // It's a holiday
    }

    return false; // It's a regular business day
}

jQuery(document).ready(function($) {
    // Calculate the estimated delivery date
    function calculateDeliveryDate() {
        var currentDate = new Date();
        var deliveryDate = new Date();

        // Set the cutoff time (2:00 PM)
        var cutoffTime = new Date(currentDate);
        cutoffTime.setHours(14, 0, 0, 0);

        // Check if the current time is past the cutoff time
        if (currentDate >= cutoffTime) {
            // It's past the cutoff time, so add 2 business days
            var businessDaysToAdd = 2;
            while (businessDaysToAdd > 0) {
                deliveryDate.setDate(deliveryDate.getDate() + 1);
                if (!isPolishHoliday(deliveryDate)) {
                    businessDaysToAdd--;
                }
            }
        } else {
            // It's before the cutoff time, so add 1 business day
            var businessDaysToAdd = 1;
            while (businessDaysToAdd > 0) {
                deliveryDate.setDate(deliveryDate.getDate() + 1);
                if (!isPolishHoliday(deliveryDate)) {
                    businessDaysToAdd--;
                }
            }
        }

        return deliveryDate;
    }

    // Get the estimated delivery date
    var estimatedDeliveryDate = calculateDeliveryDate();

});
