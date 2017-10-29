require('./bootstrap');

$(document).ready(function() {

    /**
     *  We don't want the buttons to stay focused after they are clicked
     */
    $('.btn').mouseup(function(){
        $(this).blur();
    })

    /**
     * SCROLL TO TARGET ON CLICK
     * If clicked elements data-target attribute points to some elements id
     * starting with a # it will that element to the top of the page if possible
     * <p id="scrollToThisElem" class="scroll_target_to_top" data-target="#scrollToThisElem">
     */
    $('.scroll_target_to_top').on('click', function() {
        var elem = $(this);
        elem = elem[0].dataset.target;
        setTimeout(function (){
            $('html, body').animate({
                scrollTop: $(elem).offset().top
            }, 1000);
        }, 1);
    });

    /**
     * SCROLL TO THIS ON CLICK
     * If clicked elements data-target attribute points to some elements id
     * starting with a # it will that element to the top of the page if possible
     * <p id="scrollToThisElem" class="scroll_target_to_top" data-target="#scrollToThisElem">
     */
    $('.scroll_to_top').on('click', function() {
        var elem = $(this);
        if ($(this).hasClass('collapsed')) {
            setTimeout(function (){
                $('html, body').animate({
                    scrollTop: $(elem).offset().top - 200
                }, 1000);
            }, 1);
        }
    });

});