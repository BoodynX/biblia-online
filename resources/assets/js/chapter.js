$(document).ready(function() {
    /**
     * SWITCH FORM ACTION ATTR ON CLICK
     * Changes the forms action attr depending on which button was pushed
     * button determined by value attr
     */
    $('.navButton').on('click', function() {
        var elem = $(this);
        if (elem[0].value == 'the_end') {
            document.chapter_nav_buttons.action = '/last';
        } else {
            if (elem[0].value == 'next_step') {
                var book    = elem[0].dataset.book
                var chapter = elem[0].dataset.chapter
            }
            if (elem[0].value == 'next_book') {
                var book    = elem[0].dataset.book
                var chapter = elem[0].dataset.chapter
            }
            document.chapter_nav_buttons.action = '/ksiega/' + book + '/rozdzial/' + chapter;
        }
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
});