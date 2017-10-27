$(document).ready(function() {
    /** Automatically add the CSRF token to all request headers */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /**
     * SCROLL TO THIS ON CLICK
     * If clicked elements data-target attribute points to some elements id
     * starting with a # it will that element to the top of the page if possible
     * <p id="scrollToThisElem" class="scrollTo" data-target="#scrollToThisElem">
     */
    $('.scrollTo').on('click', function() {
        var elem = $(this);
        elem = elem[0].dataset.target;
        setTimeout(function (){
            $('html, body').animate({
                scrollTop: $(elem).offset().top
            }, 1000);
        }, 1);
    });

    /**
     * SWITCH FORM ACTION ATTR ON CLICK
     * Changes the forms action attr depending on which button was pushed
     * button determined by value attr
     */
    $('.navButton').on('click', function() {
        var elem = $(this);
        if (elem[0].value == 'next_step') {
            var book    = elem[0].dataset.book
            var chapter = elem[0].dataset.chapter
        }
        if (elem[0].value == 'next_book') {
            var book    = elem[0].dataset.book
            var chapter = elem[0].dataset.chapter
        }
        document.chapter_nav_buttons.action = '/ksiega/' + book + '/rozdzial/' + chapter;
    });

    /**
     * SEND QUESTION - AJAX
     */
    $('#faq_form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/chapter/send_question',
            data: $('#faq_form').serialize(),
            success: function(msg) {
                $("#faq_form").collapse('hide');
                $("#ajaxResponse").text(msg);
                $("#ajaxResponse").collapse('show');
            }
        });
    });

    /**
     * Hide the "Thank You" message, after reopening the FAQ form, after a question was send
     */
    $('#faq_form_question').on('click', function() {
        $("#ajaxResponse").collapse('hide');
    });

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