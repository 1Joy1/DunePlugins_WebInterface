$(document).ready(function() {
    $('#table_body').on('click', '.edit-buttons', function(){
        var row = $(this).parents('.row-tv-list'),
            url_list_fild = row.find('.playlists-fild'),
            save_button = '<button class="save-buttons" style="width: 9em;">Сохранить</button>';
            
        url_list_fild.prop('disabled', false);
        $(this).parent().append(save_button);
        $(this).remove(); 
    });
    
    $('#table_body').on('click', '.save-buttons', function(){
        var row = $(this).parents('.row-tv-list'),
            url_list_fild = row.find('.playlists-fild'),
			url_list_id = row.find('.playlists-id'),
            this_batton = $(this),
            edit_button = '<button class="edit-buttons" style="width: 9em;">Редактировать</button>';
            
        $.ajax({
            url: 'link',
            type: 'post',
            data:  {
                action: 'save_playlist',
                url: url_list_fild.val(),
				id: url_list_id.val(),
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(resp.error);
                } else {
                    if (url_list_fild.val() === "") {
                        row.remove();
                    } else {
                        url_list_fild.prop('disabled', true);
                        this_batton.parent().append(edit_button);
                        this_batton.remove(); 
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
    
	$('#table_body').on('click', '.phide-buttons', function(){
        var row = $(this).parents('.row-tv-list'),
            url_list_fild = row.find('.playlists-fild'),
            this_batton = $(this),
            edit_button = '<button class="popen-buttons" style="width: 9em;">Включить</button>';
            
        $.ajax({
            url: 'link',
            type: 'post',
            data:  {
                action: 'hide_playlist',
                url: url_list_fild.val(),
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(resp.error);
                } else {
                    if (url_list_fild.val() === "") {
                        row.remove();
                    } else {
                        url_list_fild.prop('disabled', true).addClass('non-activ');
                        this_batton.parent().append(edit_button);
                        this_batton.remove(); 
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
	$('#table_body').on('click', '.popen-buttons', function(){
        var row = $(this).parents('.row-tv-list'),
            url_list_fild = row.find('.playlists-fild'),
            this_batton = $(this),
            edit_button = '<button class="phide-buttons" style="width: 9em;">Отключить</button>';
            
        $.ajax({
            url: 'link',
            type: 'post',
            data:  {
                action: 'hide_playlist',
                url: url_list_fild.val(),
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(resp.error);
                } else {
                    if (url_list_fild.val() === "") {
                        row.remove();
                    } else {
                        url_list_fild.prop('disabled', true).removeClass('non-activ');;
                        this_batton.parent().append(edit_button);
                        this_batton.remove(); 
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
	
    $('#table_body').on('click', '.dell-buttons', function(){
        var row = $(this).parents('.row-tv-list'),
            this_batton = $(this),
            url_list_id = row.find('.playlists-id');
        $.ajax({
            url: 'link',
            type: 'post',
            data:  {
                action: 'dell_playlist',
                id: url_list_id.val(),
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(resp.error);
                } else {
                    row.remove();
                    $('.index-number').each(function(index){
                        $(this).text(index+1)
                    });
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert("Server response error." + '\nResponseCode: ' + resp.status + 
                                                 '\ntextStatus: ' + textStatus +
                                                 '\nerrorThrown: ' + errorThrown);
            },
        });
    });
    
    $('#add-new-playlist').on('click', '.batton-add-playlist', function(){
        var val = $('#new-playlist-fild').val();

        if(val != "") {
            $.ajax({
                url: 'link',
                type: 'post',
                data:  {
                    action: 'add_new_playlist',
                    url: val,
                },        
                success: function(resp) {
                    if(resp === null || resp.status !== 'ok') {
                        alert(resp.error);
                    } else {
                        var row = '<tr class="row-tv-list"><td class="index-number"></td><td class="playlists">'+
								'<input class="playlists-id" type="hidden" value = "' + resp.id + '">'+
                                '<input class="playlists-fild" type="text" value="'+ val +'" size="100" disabled/></td>'+
								'<td class="phide" align="center"><button class="phide-buttons" style="width: 9em;">Отключить</button></td> '+
                                '<td class="edit" align="center"><button class="edit-buttons" style="width: 9em;">Редактировать</button></td>'+
                                '<td class="dell" align="center"><button class="dell-buttons" style="width: 9em;">Удалить</button></td></tr>';
                        $('#table_body').append(row);
                        $('.index-number').each(function(index){
                            $(this).text(index+1)
                        });
                        $('#new-playlist-fild').val('');
                    }
                },
                error: function(resp, textStatus, errorThrown) {
                    alert("Server response error." + '\nResponseCode: ' + resp.status + 
                                                    '\ntextStatus: ' + textStatus +
                                                    '\nerrorThrown: ' + errorThrown);
                },
            });
        } else {
            alert("Поле для URL не может быть пустым.");
        }
    });
    
        
});