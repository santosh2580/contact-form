jQuery(document).ready(function ($) {
    $('#simple-contact-form').on('submit', function (e) {
        e.preventDefault();
        
        var formData = {
            'action': 'submit_contact_form',
            'name': $('#name').val(),
            'email': $('#email').val(),
            'message': $('#message').val(),
            'nonce': simpleContactForm.nonce
        };

        $.ajax({
            type: 'POST',
            url: simpleContactForm.ajax_url,
            data: formData,
            success: function (response) {
                $('#form-response').text(response.data.message);
                $('#simple-contact-form')[0].reset();
            },
            error: function (response) {
                $('#form-response').text(response.responseJSON.data.message);
            }
        });
    });
});
