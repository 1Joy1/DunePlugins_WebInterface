$(document).ready(function() {

    getSaveContent();


    getStatus();


    $("#screenshot-but").on("click", function(e) {
		document.getElementById("screen-sound").play();
        e.preventDefault();
        $.ajax({
            url: 'do',
            type: 'get',
            data:  {
                action: 'screenshot',
            },
            success: function(resp) {
                if(resp.status === 'error') {
                    alert("Ошибка!\n" + resp.error);
                } else if(resp.status === 'ok') {
                    getSaveContent();
                    NotifyAlert("Скриншот сохранен", "Путь к файлу: " + resp.screenPath);
                } else {
                    alert("Неопознанная ошибка!");
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    });




    $("#rec-stop-but").on("click", function(e) {
        e.preventDefault();

        var this_button = $(this);

        if (this_button.hasClass("rec")) {

            sendCommandRecStop();

        } else {

            sendCommandRecStart();

        }
    });




    $('#save_content').on('click', '.del-el', function(e) {
        e.preventDefault();
        var this_el = $(this);
        var href = this_el.siblings('a').attr('href');

        $.ajax({
            url: 'do',
            type: 'post',
            data:  {
                action: 'del_el',
                href: href,
            },
            success: function(resp) {
                if(resp.status === 'ok') {
                    this_el.parent('div').remove();
                } else {
                    alert("Неопознанная ошибка!");
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    })



    function NotifyAlert(head, text) {
        var alertBlock = $('<div class="alert alert-dismissible alert-info fade show" id="myAlert">' +
                        '<button type="button" class="close" data-dismiss="alert" onfocus="this.blur();">&times;</button>' +
                        '<h5 style="word-wrap: break-word; overflow-wrap: break-word;">' + head +
                        '</h5><h6 style="word-wrap: break-word; overflow-wrap: break-word;">' + text + '</h6>' +
                    '</div>');

        $("#notifies_alert").hide().append(alertBlock).slideDown(600);

        setTimeout(function() {
            $("#myAlert").alert("close");
        }, 5000);
    }




    function sendCommandRecStart() {
        $.ajax({
            url: 'do',
            type: 'get',
            data:  {
                action: 'rec',
            },
            success: function(resp) {
                if(resp.status === 'ok') {
                    $("#rec-stop-but").addClass("rec").text("Остановить запись");
                    NotifyAlert("Запись началась", "Длительность записи ограничена 3-мя минутами.");
                    getStatus();
                } else {
                    alert("Неопознанная ошибка! Запись не началась!");
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    }




    function sendCommandRecStop() {
        $.ajax({
            url: 'do',
            type: 'get',
            data:  {
                action: 'stop_rec',
            },
            success: function(resp) {
                if(resp.status === 'ok') {
                    $("#rec-stop-but").removeClass("rec").text("Записать видео");
                    NotifyAlert("Видео сохраненно", "Путь к файлу:" + resp.screenPath);
                    getSaveContent();
                } else {
                    alert("Неопознанная ошибка! Запись не остановлена!");
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    }




    function getStatus() {
        $.ajax({
            url: 'do',
            type: 'get',
            data:  {
                action: 'status_rec',
            },
            success: function(resp) {
                if(resp.status === 'rec') {
                    $("#rec-stop-but").addClass("rec").text("Остановить запись (" + timeFormater(resp.tts) + ")");
                    setTimeout(getStatus, 1000);
                } else if(resp.status === 'stop') {
                    if ($("#rec-stop-but").hasClass("rec")) {
                        NotifyAlert("Запись закончена", "Путь к файлу: " + resp.screenPath);
                        $("#rec-stop-but").removeClass("rec").text("Записать видео");
                        getSaveContent();
                    }
                } else {
                    alert("Неопознанная ошибка при получении статуса!");
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    }




    function getSaveContent() {
        $.ajax({
            url: 'do',
            type: 'get',
            data:  {
                action: 'get_save_content',
            },
            success: function(resp) {
                if(!resp.screen_collection) {
                    alert("Ошибка! Невозможно получить сохраненные скриншоты. Отсутствует эллемент 'screen_collection'.");
                } else if (Object.keys(resp.screen_collection).length > 0) {
                    updatePage(resp.screen_collection);
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Ошибка ответа сервера. Невозможно получить сохраненные скриншоты." +
                    "\nResponseCode: " + resp.status +
                    "\ntextStatus: " + textStatus +
                    "\nerrorThrown: " + errorThrown);
            },
        });
    }




    function updatePage(screen_collection) {
        var new_html = '';
        var ellement = '';

        for (var key in screen_collection) {
            if (!detectmob()) {
                ellement = '<div class="col-lg-3 col-md-4 col-sm-6 col-6 text-center"><a href="' + screen_collection[key].src +
                           '" download><img class="screenshots" src="' + (screen_collection[key].type === 'img' ? screen_collection[key].src : screen_collection[key].fake_img) +
                           '"></a><button type="button" class="btn btn-danger btn-sm del-el">Удалить</button></div>';
            } else {
                ellement = '<div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center"><a href="' + screen_collection[key].src +
                           '" download>' + key + '</a></div>';
            }
            new_html = new_html + ellement;
        }

        $('#save_content').html(new_html);
    }




    function detectmob() {
         if( navigator.userAgent.search(/Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone|Mobile/i) !== -1) {
            return true;
          } else {
            return false;
          }
    }




    function timeFormater(sec) {

        return Math.floor(sec / 60) + ':' + ((sec % 60 < 10) ? "0" + (sec % 60) : sec % 60);
    }
});