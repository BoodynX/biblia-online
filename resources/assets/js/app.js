require('./bootstrap');

/** We don't want the buttons to stay focused after they are clicked */
$('.btn').mouseup(function(){
    $(this).blur();
})