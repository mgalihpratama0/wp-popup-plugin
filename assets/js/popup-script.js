jQuery(document).ready(function($) {
    $('#popup').fadeIn();
    $('#popup').click(function() {
        $(this).fadeOut();
    });
});