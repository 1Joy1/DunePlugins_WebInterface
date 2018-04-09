$(document).ready(function() {

    $('#fileform').trigger('reset');

    // Мы можем присоединить `fileselect` событие  ко всем input файлам в меню настроек плей листа

    $('.playlist-menu-list').on('change', ':file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });



    // Мы можем наблюдать за нашим пользовательским событием `fileselect`, таким образом.

    $(':file').on('fileselect', function(event, label) {
        $('#fileform_open_file').tooltip('destroy');
        setTimeout(function(){
            $('#fileform_open_file').tooltip({title: label, trigger: "manual"})
                                    .tooltip('show');
        }, 500);
                             
    });


    // Перехватываем отправку файла, для проверки есть ли файл в форме.
    
    $('#fileform').on('submit', function(event) {
        var form = $(this),
            formData = new FormData(this);
            

        if (!formData.get('userfile').name) {
                event.preventDefault();
                $('#fileform_open_file').tooltip('destroy');
                setTimeout(function(){
                    $('#fileform_open_file').tooltip({title: "Перед отправкой нужно выбрать файлы", trigger: "manual"})
                                            .tooltip('show');
                }, 500);
                return false;
        }
    
    });
    
    
    

    $(document).on("click.bs.dropdown.data-api", ".dropdown-not-close",
        function (e) { e.stopPropagation() }
    );
    
    $("#menu_plugin_control").on("click", "input[type=checkbox]", function(e) {
        var req = {},
            checkbox = this,
            name = this.name,
            previous_status = !this.checked;
            
        req[this.name]= +this.checked;
        
        $.ajax({
            url: 'setup.php',
            type: 'post',
            data:  req,
    
            success: function(data) {
            //Так, как с сервера при вкл. приходит - 0 а при выкл. - 1 проверяем с инверсией.
                if (data != previous_status) {
                    alert("Ошибка!\n Что-то пошло не так.\n Команда не обработалась.");
                    checkbox.checked = previous_status;
                } else if (data == 0 && name == "ad_pls") {
                    $('.item-btn-play>form').css('display', 'block');
                } else if (data == 1 && name == "ad_pls"){
                    $('.item-btn-play>form').css('display', 'none');
                }             
            },
            error: function(data) {
                alert("Ошибка ответа сервера.\n Код ошибки: "+data.status+".\n Команда не обработалась.");
                checkbox.checked = previous_status;
            },
        });
    });
});

