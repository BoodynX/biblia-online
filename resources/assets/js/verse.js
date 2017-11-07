$(document).ready(function() {
    /**
     * ADD VERSE TO FAVS - AJAX
     */
    $('.add_to_fav').on('click', function() {
        var verse_id         = this.value;
        var operation        = this.name;
        var new_operation    = (operation == 'add') ? 'rem' : 'add';
        var error_msg_field  = '#add_to_fav_error_' + verse_id;
        var fav_button       = '#fav_button_' + verse_id;
        var fav_button_label = '#fav_button_label_' + verse_id;
        var fav_button_ico   = '#fav_button_ico_' + verse_id;
        $.ajax({
            type: 'POST',
            url: '/verse/store_fav',
            data: { verse_id : verse_id , operation : operation },
            success: function(msg) {
                $(error_msg_field).collapse('hide');
                $(fav_button).attr('name', new_operation);
                $(fav_button).blur();
                $(fav_button_label).text(msg);
                $(fav_button_ico).toggleClass('fav_on fav_off');
            },
            error: function () {
                $(error_msg_field).text('Wystąpił błąd podczas zapisu! Spróbuj później.');
                $(error_msg_field).collapse('show');
                $(fav_button_label).text('Błąd');
            }
        });
    });
});