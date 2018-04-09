$(document).ready(function() {
    
    $('.upfotter').on('click', '.tapperform', function(event){
        event.preventDefault();
        var footer = $(this).parents('.footer'),
            tapper = $(this),
            a,
            b;
        
        if (footer.css('height') == '10px') {
            footer.css('height','160px');
            a = 'glyphicon-chevron-up';
            b = 'glyphicon-chevron-down';
        } else {
            footer.css('height','10px');
            a = 'glyphicon-chevron-down';
            b = 'glyphicon-chevron-up';
        }
        tapper.children('.glyphicon').removeClass(a).addClass(b);
        

    });
});