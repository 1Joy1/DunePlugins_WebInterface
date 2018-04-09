$(document).ready(function() {
    $('#notice').css('display', 'none');
    
    $('#page_name').css('display', 'block');

    $('#mytable').css('display', 'table');

    createFilterPagination();
    
    $('.row-tv-list').on('click', '.arhivs-buttons-edit', function() {
        var edit_button = $(this),
            save_button = '<button class="arhivs-buttons-save" style="width: 9em;">Сохранить</button>',
            row = $(this).parents('.row-tv-list');
            
        row.removeClass('set');    
        row.find('.arhivs-url-fild').prop('disabled', false).removeClass('set');
        row.find('.arhivs-coll-day-fild').prop('disabled', false);
        row.find('.arhivs-ua-fild').prop('disabled', false).removeClass('set');
        edit_button.parent().append(save_button);
        edit_button.remove();            
    });
    
    
    $('.row-tv-list').on('click', '.arhivs-buttons-save', function() {
        var save_button = $(this),
            edit_button = '<button class="arhivs-buttons-edit" style="width: 9em;">Редактировать</button>',
            row = $(this).parents('.row-tv-list'),
            url_fild = row.find('.arhivs-url-fild'),
            day_fild = row.find('.arhivs-coll-day-fild'),
            ua_fild = row.find('.arhivs-ua-fild'),
            chanel_name_fild = row.find('.chanel-name');
            
        $.ajax({
            url: 'link',
            type: 'post',
            data:  {
                action: 'save_arhive',
                idkey: row.data('idkey'),
                url: url_fild.val(),
                day: day_fild.val(),
                ua: ua_fild.val(),
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert("Не удалось сохранить данные.");
                } else {
                    if (url_fild.val() !== "") {
                        row.addClass('set');
                        url_fild.prop('disabled', true).addClass('set');
                        day_fild.prop('disabled', true);
                        ua_fild.prop('disabled', true).addClass('set');
                        save_button.parent().append(edit_button);
                        save_button.remove();
                        chanel_name_fild.addClass('font-bold');
                    } else {
                        chanel_name_fild.removeClass('font-bold');
                        day_fild.val(1);
                        ua_fild.val('');
                    }
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Server response error." + '\nResponseCode: ' + resp.status + 
                                                 '\ntextStatus: ' + textStatus +
                                                 '\nerrorThrown: ' + errorThrown);
            },
        });
    });

    
    $('#clear-all-archive').on('click', function() {
        if (confirm("Вы уверены, что хотите очистить весь список каналов от ссылок на архивы???\nВостановление будет невозможным!")) {
            $.ajax({
                url: 'link',
                type: 'post',
                data:  {
                    action: 'clear_arhive',
                },        
                success: function(resp) {
                    if(resp === null || resp.status !== 'ok') {
                        alert("Не удалось сохранить данные.");
                    } else {
                        var save_button = '<button class="arhivs-buttons-save" style="width: 9em;">Сохранить</button>';                        
                        $('.arhivs-url-fild').val('').prop('disabled', false).removeClass('set');
                        $('.arhivs-coll-day-fild').val(1).prop('disabled', false);
                        $('.arhivs-ua-fild').val('').prop('disabled', false).removeClass('set');
                        $('.arhivs-buttons-edit').parent().append(save_button);
                        $('.arhivs-buttons-edit').remove();
                        $('.chanel-name').removeClass('font-bold');
                        $('.row-tv-list').removeClass('set');
                    }
                },
                error: function(resp, textStatus, errorThrown) {
                    alert("Server response error." + '\nResponseCode: ' + resp.status + 
                                                    '\ntextStatus: ' + textStatus +
                                                    '\nerrorThrown: ' + errorThrown);
                },
            });
        }
    });
    
    
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
    
    
});


function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), 
            valB = getCellValue(b, index);
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
    }
}

function getCellValue(row, index){
    return $(row).children('td').eq(index).text();
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
