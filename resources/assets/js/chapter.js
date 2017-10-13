/**
 * SCROLL TO THIS ON CLICK
 * If clicked elements data-target attribute points to some elements id
 * starting with a # it will that element to the top of the page if possible
 * <p id="scrollToThisElem" class="scrollTo" data-target="#scrollToThisElem">
 */

$( ".scrollTo" ).on( "click", function() {
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
$( ".navButton" ).on( "click", function() {
    var elem = $(this);
    if (elem[0].value == 'next_step') {
        var book    = elem[0].dataset.book
        var chapter = elem[0].dataset.chapter
    }
    if (elem[0].value == 'next_book') {
        var book    = elem[0].dataset.book
        var chapter = elem[0].dataset.chapter
    }
    document.chapter_nav_buttons.action ='/ksiega/'+book+'/rozdzial/'+chapter;
});