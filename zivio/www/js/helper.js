$(document).ready(function() {
    $(".plugin-control").on("click", ".btn", function(event){
        event.preventDefault();
        var command,
            reason,
            plugin_name = $(this).parents(".plugin-card").find(".plugin-name").text();


        if ($(this).hasClass("btn-danger")) {
            command = "Стоп";
            reason = "error";
            $(this).addClass('disabled').siblings('.btn-success').removeClass('disabled');

            NotifyAlert(reason, "Произошла ошибка. Плагин <b>" + plugin_name + "</b> - команда \"" + command + "\" не выполненна.");

        }

        if ($(this).hasClass("btn-success")) {
            command = "Старт";
            reason = "success";
            $(this).addClass('disabled').siblings('.btn-danger').removeClass('disabled');
            NotifyAlert(reason, "Плагин <b>" + plugin_name + "</b> - команда \"" + command + "\" обработатна успешно.");

        }
    });
});


function NotifyAlert(reason, text) {
    if (reason === "error" || reason === 'success') {
        var header;

        if (reason === "error") {
            reason = "danger";
            header = "Упcс!!! Что-то пошло не так!!!";
        } else {
            header = "Успешно!!!"
        }



        var alertBlock = $("<div class='alert alert-"+reason+" alert-dismissible fade in' id='myAlert' role='alert'>" +
                            "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                            "<span aria-hidden='true'>×</span></button>" +
                            "<h4>"+header+"</h4>" +
                            "<h5>" + text + "</h5></div>"
                            );

        $("#notifies_alert").hide().append(alertBlock).slideDown(600);

        setTimeout(function() {
            $("#myAlert").alert("close");
        }, 8000);
    }

}