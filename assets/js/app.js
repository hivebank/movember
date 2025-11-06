// FormFlow - Simple JavaScript utilities

$(document).ready(function() {
    console.log('FormFlow loaded');

    // Add any common JavaScript functionality here
    // For example, form validation, AJAX helpers, etc.

    // Simple notification system
    window.showNotification = function(message, type = 'success') {
        const alert = $('<div>')
            .addClass('alert')
            .addClass(type === 'error' ? 'alert-error' : 'alert-success')
            .text(message)
            .css({
                position: 'fixed',
                top: '20px',
                right: '20px',
                'z-index': 9999,
                'min-width': '300px'
            });

        $('body').append(alert);

        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    };
});
