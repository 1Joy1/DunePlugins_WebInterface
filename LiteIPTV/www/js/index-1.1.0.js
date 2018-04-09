$(document).ready(function() {
    $('#notice').css('display', 'none');
    
    $('#page_name').css('display', 'block');

    $('#mytable').css('display', 'table');
    
    if (window.location.pathname ==="/cgi-bin/plugins/liteIPTV/index" && window.location.search === "?sorted") {
        $('#pagination').prop('disabled', true);
        $('#search').prop('disabled', true);
        
        $("#table_body").sortable({
            stop: function( event, ui ) {
                var sortedIDs = $( "#table_body" ).sortable( "toArray", {attribute:"data-idkey"} );
                $.ajax({
                    url: 'link',
                    type: 'post',
                    data:  {
                        action: 'save_sorted',
                        soted_chanels: sortedIDs,
                    },        
                    success: function(resp) {
                        if(resp === null || resp.status !== 'ok') {
                            alert("Не удалось сохранить данные сортировки.");
                            $("#table_body").sortable( "cancel" );
                        }
                    },
                    error: function(resp, textStatus, errorThrown) {
                        alert("Server response error." + '\nResponseCode: ' + resp.status + 
                                                        '\ntextStatus: ' + textStatus +
                                                        '\nerrorThrown: ' + errorThrown);
                        $("#table_body").sortable( "cancel" );
                    },
                });
            }
        });        
        $(".row-tv-list").on('mousedown', function() {
            $(this).children().each(function() {
                $(this).width($(this).innerWidth());
            });
        });
        $(".row-tv-list").on('mouseup', function() {
            $(this).children().each(function() {
                $(this).removeAttr('style');
            });
        });
    } else {
        $('th').click(function(){
            var table = $(this).parents('table').eq(0),
                rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
                
            this.asc = !this.asc;
            if (!this.asc) {
                rows = rows.reverse();
            }
            for (var i = 0; i < rows.length; i++) {
                table.append(rows[i]);
            }
        })
    }
    
    $('.select-group-selector').change(function(){
        var formData = new FormData(),
            selector = $(this),
            sellsSelectorGroup = $('.select-group select[name='+this.name+']'),
            sellsGroupId = sellsSelectorGroup.parents('.select-group').siblings('.group-id');            
            
        formData.append('grID', this.value);
        formData.append('vsetvID', this.name);
        formData.append('action', 'grups_id');

        $.ajax({
            url: 'link',
            type: 'post',
            processData: false,
            contentType: false,
            data:  formData,
    
            success: function(resp) {
                if (resp === null || resp.status !== 'ok') {
                    alert("Не удалось сохранить данные.");
                    selector.val(selector.attr('data-saved-value'));
                } else {                
                    sellsSelectorGroup.val(selector.val());
                    sellsSelectorGroup.attr('data-saved-value', selector.val());
                    sellsGroupId.text(selector.val());
                    if (selector.val() !== "8888") {
                        sellsGroupId.attr("bgcolor","#008000");
                    } else {
                        sellsGroupId.attr("bgcolor","#FF0000");
                    }
                }
            },
            
            error: function(resp) {
                alert("Ошибка, Данные не сохранились.");
                selector.val(selector.attr('data-saved-value'));
            },
        });
    });

    $('.set-vsetv-id-form').submit(function(event){
        event.preventDefault();
        
        var formData = new FormData(this),
            sells_set_vsetv_id = $('.set-vsetv-id input[value='+formData.get('id_key')+']').parents('.set-vsetv-id'),
            sells_vsetv_id = sells_set_vsetv_id.siblings('.vsetv-id'),
            sells_flag_manual = sells_set_vsetv_id.siblings('.flag-manual'),
            sells_select_group = sells_set_vsetv_id.siblings('.select-group').find('select'),
            sells_group_id = sells_set_vsetv_id.siblings('.group-id'),
            sells_status_logo = sells_set_vsetv_id.siblings('.status-logo'),
            sells_img_logo = sells_set_vsetv_id.siblings('.img-logo').find('img'),
            sells_img_upload = sells_set_vsetv_id.siblings('.img-upload').find('[type=file]');
            
        //formData = {id_key:$md5, action:'save_vsetvid',vsetvID: value vsetvid}    
        $.ajax({
            url: 'link',
            type: 'post',
            processData: false,
            contentType: false,
            data:  formData,
    
            success: function(resp) {
                if (resp === null) {
                    alert("Ошибка, сервер вернул пустой ответ. Данные не сохранились.");
                    return;
                }
                if (resp.updated === 'error') {
                    alert("Ошибка! Недопустимый vsetvID");
                    return;
                }
    
                sells_vsetv_id.text(resp.chanel_id);
                sells_vsetv_id.attr('title', resp.title);
                if (resp.chanel_id == "9999") {
                    sells_vsetv_id.attr('bgcolor', '#FF0000');
                } else {
                    sells_vsetv_id.removeAttr('bgcolor');
                }
    
                if (resp.manual == "true") {
                    sells_flag_manual.text('m');
                    sells_flag_manual.attr('bgcolor', '#008000');
                } else {
                    sells_flag_manual.text('');
                    sells_flag_manual.removeAttr('bgcolor');
                }
    
                sells_status_logo.text(resp.icon_status);
    
                switch (resp.icon_status) {
                    case 'no_logo':
                        sells_status_logo.attr('bgcolor', '#FF0000');
                        break;
                    case 'in plugin':
                        sells_status_logo.removeAttr('bgcolor');
                        break;
                    case 'in DATA':
                        sells_status_logo.attr('bgcolor', '#008000');
                        break;
                }
    
                sells_img_upload.attr('name', resp.icon_path_upload);
                sells_img_logo.attr('src', resp.icon_src);
    
                sells_group_id.text(resp.group_id);
                sells_select_group.val(resp.group_id);
    
                if (resp.group_id != "8888") {
                    sells_group_id.attr("bgcolor","#008000");
                } else {
                    sells_group_id.attr("bgcolor","#FF0000");
                }
            },
            error: function(data) {
                alert("Ошибка, Данные не сохранились.");
            },
        });
    
        $(this).trigger('reset');
    });
    
    $('.select-vsetv-id-form').submit(function(event){
        event.preventDefault();        
        var formData = new FormData(this);
        //formData = {id_key:md5 (md5 от имени канала), caption:$caption (имя канала)}
        $.ajax({
            url: 'vsetv_modal',
            type: 'post',
            processData: false,
            contentType: false,
            data:  formData,
    
            success: function(resp) {
                $('.modal-title').html(formData.get('caption'));
                $('#req_content').html(resp);
                $('#set_vsetvid_modal').modal();
                $('#modal_all_list_ch').selectpicker({width:'auto'});
            },
    
            error: function(resp) {
                alert("Ошибка, Не могу загрузить форму выбора \"vsetvID\".");
            },
        });        
    });
    
    $('#set_vsetvid_modal').on('submit', '#modal_set_vsetv_id_form', function(event){
        event.preventDefault(); 
        
        if (document.getElementById('modal_hd_sd').checked && this.vsetvID.value != 0) {
            this.vsetvID.value = this.vsetvID.value + '_HD';
        }
    
        var formData = new FormData(this),
            sells_select_vsetv_id = $('.select-vsetv-id input[value='+formData.get('id_key')+']').parents('.select-vsetv-id'),
            sells_vsetv_id = sells_select_vsetv_id.siblings('.vsetv-id'),
            sells_flag_manual = sells_select_vsetv_id.siblings('.flag-manual'),
            sells_select_group = sells_select_vsetv_id.siblings('.select-group').find('select'),
            sells_group_id = sells_select_vsetv_id.siblings('.group-id'),
            sells_status_logo = sells_select_vsetv_id.siblings('.status-logo'),
            sells_img_logo = sells_select_vsetv_id.siblings('.img-logo').find('img'),
            sells_img_upload = sells_select_vsetv_id.siblings('.img-upload').find('[type=file]');
    
        //formData = {id_key:md5 (md5 от имени канала), action:'save_vsetvid', vsetvID:$id (id канала на сайте vsetv)}
        $.ajax({
            url: 'link',
            type: 'post',
            processData: false,
            contentType: false,
            data:  formData,
    
            success: function(resp) {
                if (resp === null) {
                    alert("Ошибка, сервер вернул пустой ответ. Данные не сохранились.");
                    return;
                }
                if (resp.updated === "error") {
                    alert("Ошибка! Недопустимый vsetvID");
                    return;
                }
    
                sells_vsetv_id.text(resp.chanel_id);
                sells_vsetv_id.attr('title', resp.title);
                if (resp.chanel_id == "9999") {
                    sells_vsetv_id.attr('bgcolor', '#FF0000');
                } else {
                    sells_vsetv_id.removeAttr('bgcolor');
                }
    
                if (resp.manual == "true") {
                    sells_flag_manual.text('m');
                    sells_flag_manual.attr('bgcolor', '#008000');
                } else {
                    sells_flag_manual.text('');
                    sells_flag_manual.removeAttr('bgcolor');
                }
    
                sells_status_logo.text(resp.icon_status);
    
                switch (resp.icon_status) {
                    case 'no_logo':
                        sells_status_logo.attr('bgcolor', '#FF0000');
                        break;
                    case 'in plugin':
                        sells_status_logo.removeAttr('bgcolor');
                        break;
                    case 'in DATA':
                        sells_status_logo.attr('bgcolor', '#008000');
                        break;
                }
    
                sells_img_upload.attr('name', resp.icon_path_upload);
                sells_img_logo.attr('src', resp.icon_src);
    
                sells_group_id.text(resp.group_id);
                sells_select_group.val(resp.group_id);
    
                if (resp.group_id != "8888") {
                    sells_group_id.attr("bgcolor","#008000");
                } else {
                    sells_group_id.attr("bgcolor","#FF0000");
                }
            },
            error: function(resp) {
                alert("Ошибка, Данные не сохранились.");
            },
        });
    
        $('#set_vsetvid_modal').modal('hide');
        $('#req_content').html('');
    });
    
    $('#set_vsetvid_modal').on('change', 'select', function(event){
        var ms = $('#modal_similar'),
            mfl = $('#modal_fist_letter'),
            malc = $('#modal_all_list_ch'),
            value = this.value;
    
        $('#modal_similar > [value=' + value + ']').length ? ms.val(value) : ms.val(0);
        $('#modal_fist_letter > [value=' + value + ']').length ? mfl.val(value) : mfl.val(0);
        $('#modal_all_list_ch > [value=' + value + ']').length ? malc.selectpicker('val', value) : malc.selectpicker('val', 0);
    
        $('#req_content').find('input[name=vsetvID]').val(value);
        $('#modal_fin_selected').text('Выбранный vsetvID: ' + value).css('display', 'block');
    });
    
    $('.img-upload-form').submit(function(event){
        event.preventDefault();

        var form = this,
            formData = new FormData(this),
            sells_img_upload = $('.img-upload input[value='+formData.get('id_key')+']').parents('.img-upload'),
            sells_status_logo = sells_img_upload.siblings('.status-logo'),
            sells_img_logo = sells_img_upload.siblings('.img-logo').find('img');
    
        $.ajax({
            url: 'file.php',
            type: 'post',
            processData: false,
            contentType: false,
            data:  formData,
    
            success: function(data) {
                var resp = JSON.parse(data);
                if (resp['status'] == 'error') {
                    alert('Ошибка! Файл не сохранен!');
                    return;
                }
    
                if (resp['status'] == 'none_file') {
                    alert('Файл не выбран! Выберите PNG файл!');
                    return;
                }
    
                sells_status_logo.text(resp['icon_status']);
    
                switch (resp['icon_status']) {
                    case 'no_logo':
                        sells_status_logo.attr('bgcolor', '#FF0000');
                        break;
                    case 'in plugin':
                        sells_status_logo.removeAttr('bgcolor');
                        break;
                    case 'in DATA':
                        sells_status_logo.attr('bgcolor', '#008000');
                        break;
                }
    
                sells_img_upload.attr('name', resp['icon_path_upload']);
                sells_img_logo.attr('src', resp['icon_src']);
    
                $(form).trigger('reset');
            },
    
            error: function(data) {
                alert("Ошибка, Не могу загрузить логотип");
            },
        });
    });
    
    
    createFilterPagination();

    $("#search").keyup(function(){
        $("#pagination").val(0);
        _this = this;
        $.each($("#mytable tbody tr"), function() {
            if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
               $(this).hide();
            else
               $(this).show();
        });
    });

    $("#pagination").change(function(){
        $("#search").val('');
        if ($(this).val() == 0) {
            $(".row-tv-list").show();
        } else {
            var value = $(this).val(),
                finish_row = +value * 100;
                start_row = finish_row - 100;
            $.each($("#mytable tbody tr"), function() {
                var id = $(this).attr('id').replace('rowId_', '');
                if (id > start_row && id <= finish_row) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })
        }
    });
});

function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), 
            valB = getCellValue(b, index);
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
    }
}

function getCellValue(row, index){
    return $(row).children('td').eq(index).text().replace("_HD","");
}

function vsetvIDupd(){
    var formData = new FormData();
    formData.append('action', 'vsetvIDupd');
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/cgi-bin/plugins/liteIPTV/link");
    xhr.send(formData);
    alert ('vsetvID обновлены!\n Для применения страница перезагрузится!');
    location.reload();
}


function cutset(){
    var formData = new FormData();
    var g2=document.getElementById('cutstr').value;
    if (g2 == '') {
        alert ('Err!');
    } else {
        formData.append('cutstr', g2);
        formData.append('action', 'save_cutname');
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/cgi-bin/plugins/liteIPTV/link");
        xhr.send(formData);
        alert ('Добавлено в Удалить из названия канала:' + g2);
        location.reload();
    }
}

function createFilterPagination(){
    var count_row = $(".row-tv-list").size(),
        remain = count_row % 100,
        count_select_option = Math.ceil(count_row / 100),
        opt_text;

    for (var i=1; i<count_select_option+1; i++) {
        if (remain && i == count_select_option) {
            opt_text = (i*100-99) + '-' + (i*100-100+remain);
        } else {
            opt_text = i*100-99 + '-' + i*100;
        }
        $("#pagination").append(
            $('<option>', { value: i,
                            text: opt_text }));
    }
}
