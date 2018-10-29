$(document).ready(function() {


    $('.upfotter').on('click', '.tapperform', function(event){
        event.preventDefault();

        var footer = $(this).parents('.footer'),
            tapper = $(this),
            a,
            b;

        if (!footer.hasClass('open')) {
            footer.css('height','160px');
            a = 'glyphicon-chevron-up';
            b = 'glyphicon-chevron-down';
            footer.addClass('open');
        } else {
            footer.css('height','10px');
            a = 'glyphicon-chevron-down';
            b = 'glyphicon-chevron-up';
            footer.removeClass('open');
        }
        tapper.children('.glyphicon').removeClass(a).addClass(b);
    });



    $('.button-dune-controll').on('click', '.ir-code', function(event) {
        event.preventDefault();

        var $brand = $('.navbar-brand').find('a'),
            ir_code = $(this).data('ircode'),
            dune_ip = $(this).data('ipaddr');


        $.ajax({
            url: "http://" + dune_ip + "/cgi-bin/do",
            type: 'get',
            data: { 'cmd': 'ir_code', 'ir_code': ir_code },

            success: function(data) {
                $brand.css('color', 'red');
                setTimeout(function() {
                    $brand.css('color', '');
                }, 200);
            },
        });
    });



    $('.item').on('click', 'a', function(event) {
        event.preventDefault();

        var $brand = $('.navbar-brand').find('a'),
            stream_url = $(this).data('streamurl');


        $.ajax({
            url: "link",
            type: 'get',
            data: { 'n1': stream_url },

            success: function(data) {
                $brand.css('color', 'red');
                setTimeout(function() {
                    $brand.css('color', '');
                }, 200);
            },
        });


    })
});