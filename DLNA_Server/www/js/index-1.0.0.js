$(document).ready(function() {
    
    getSettings();
    
    $("#manag-fold-tabl").on("click", '.folder-dell', function(e) {
        e.preventDefault();
        
        if (!confirm(lang.translate("Are you sure you want to delete the folder from the server indexing"))) {
            return;
        }
        var folder = $(this).parents(".folder");
            path_folder = folder.find('.path-folder').text();
        
        $.ajax({
            url: 'folder',
            type: 'post',
            data:  {
                command: 'dell_folder', 
                value: path_folder,
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error!") + '\n' +
                          lang.translate("The directory is not deleted."));
                } else {
                    folder.remove();
                    NotifyAlert('Folder removed from indexing.', 'After adding / removing folders, do not forget to start the scan.');
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("Folder not deleted."));
            },
        });
    });
    
    
    $("#rescan-db").on("click", function(e) {
        e.preventDefault();        
        $.ajax({
            url: 'control',
            type: 'post',
            data:  {
                command: 'rescan_db', 
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error, not start the scan!") + '\n' +
                          lang.translate("Server Response:") + '\n' + resp.error);
                } else {
                    NotifyAlert('Scaning started.', 'Do not shutdown the server until scanning is complete.');
                    getSettings();
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("Scanning is failed."));
            },
        });
    });

    
    $("#modal-for-folder").on("hidden.bs.modal", function (e) {
        setTimeout(function() { $("#btn-call-modal").blur(); }, 100);
    });
    
    
    $("#modal-for-folder").on("show.bs.modal", function (e) {
        var fild_path = $("#fild-dune-resurs-path");
        
        fild_path.text("/");
        
        $(".tools-block").empty();
        
        $.ajax({
            url: 'folder',
            type: 'post',
            data:  {
                command: 'get_folders_from_this_path', 
                value: '',
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error, The resources from the DuneHD are not received."));
                } else {
                    var elements = '';
                    $.each(resp.resurses, function( index, value ) {
                        elements = elements +
                                  '<a class="block dune-resurs" data-dune-resurs-path="' + 
                                  value.resurs_path +'" href="#"><img src="' + 
                                  plugin_path + 'img/' + value.resurs_icon + '.png"><span>' + 
                                  value.resurs_name + '</span></a>';
                    });
                    $(".tools-block").append(elements);
                    addFoldersHandler();
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("The resources from the DuneHD are not received."));
            },
        });
         
    });


    $(".plugin-control").on("click", "input[type=checkbox]", function(e) {
        var checkbox = this,
            name = this.name;
            
        if (name == "off_on") {
            var value = checkbox.checked ? 'true' : 'false';
            sendPrefs('set_off_on', value);
        }
        
        if (name == "autostart") {
            var value = checkbox.checked ? 'yes' : 'no';
            sendPrefs('set_autostart', value);
        }
        
        if (name == "autosearch_new_file") {
            var value = checkbox.checked ? 'yes' : 'no';
            sendPrefs('set_inotify', value);
        }
        
        if (name == "dlna_standart") {
            var value = checkbox.checked ? 'yes' : 'no';
            sendPrefs('set_dlna_standart', value);
        }
        
        if (name == "loging") {
            var value = checkbox.checked ? 'on' : 'off';
            sendPrefs('set_loging', value);
        }
        
    });

    
    $("#add-folder-button").on("click", function(e) {
        e.preventDefault();
        var path = $("#fild-dune-resurs-path").text();
        if (path == "" || path == "/") {
            alert(lang.translate("Select folder"));
            return;
        }
        $.ajax({
            url: 'folder',
            type: 'post',
            data:  {
                command: 'add_folder', 
                value: path,
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error, Folder not added."));
                } else {
                    var html_content = '<tr class="folder"><th scope="row" style="width: 100px;"><img class="folder-ico" src="' + 
                                        plugin_path +'img/folder_b.png"></th><td>' + resp.resurs.name + '</td><td class="path-folder">' + 
                                        resp.resurs.path + '</td><td><a class="folder-dell block float-right" href="#"><img class="minus-ico" src="' + 
                                        plugin_path +'img/minus-b.png"></a></td></tr>';
                    if(resp.added === true) {
                        $("#manag-fold-tabl").append(html_content);
                        NotifyAlert('The folder is added, for indexing.', 'After adding / removing folders, do not forget to start the scan.');
                    } else {
                        alert(lang.translate("This folder has already been added for indexing."));
                    }
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("Folder not added."));
            },
        });
    });

    
    function addFoldersHandler() {
        $(".dune-resurs").on("click", function(e) {
            e.preventDefault();
            var fild_path = $("#fild-dune-resurs-path");
                
            fild_path.text($(this).data('duneResursPath'));
            
            $.ajax({
                url: 'folder',
                type: 'post',
                data:  {
                    command: 'get_folders_from_this_path', 
                    value: fild_path.text(),
                },        
                success: function(resp) {
                    if(resp === null || resp.status !== 'ok') {
                        alert(lang.translate("Error, The resources from the DuneHD are not received."));
                    } else {
                        $(".tools-block").empty();
                        if (fild_path.text() == "") {
                            var elements = "";
                            fild_path.text("/");
                        } else {
                            var elements = '<a class="block dune-resurs" data-dune-resurs-path="' + 
                                      fild_path.text().substr(0, fild_path.text().lastIndexOf('/')) +
                                      '" href="#"><img src="' + plugin_path + 'img/back.png"><span>...</span></a>';
                        }              
                        $.each(resp.resurses, function( index, value ) {
                            elements = elements +
                                      '<a class="block dune-resurs" data-dune-resurs-path="' + 
                                      value.resurs_path +'" href="#"><img src="' + 
                                      plugin_path + 'img/' + value.resurs_icon + '.png"><span>' + 
                                      value.resurs_name + '</span></a>';
                        });
                        $(".tools-block").append(elements);
                        addFoldersHandler();
                    }
                },
                error: function(resp, textStatus, errorThrown) {
                    alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                     '\ntextStatus: ' + textStatus +
                                                                     '\nerrorThrown: ' + errorThrown +
                                                                     '\n' + lang.translate("The resources from the DuneHD are not received."));
                },
            });
        });
    }
    
    
    function sendPrefs(command, value) {
        $.ajax({
            url: 'control',
            type: 'post',
            data:  {
                command: command, 
                value: value,
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error!") + '\n' +
                          lang.translate("New settings not set.") + '\n' + resp.error);
                }
                getSettings();
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("New settings not set."));
            },
        });
    }

    
    function getSettings() {
        $.ajax({
            url: 'control',
            type: 'get',
            data:  {
                command: 'get_settings', 
            },        
            success: function(resp) {
                if(resp === null || resp.status !== 'ok') {
                    alert(lang.translate("Error!") + '\n' +
                          lang.translate("Server is not returned settings."));
                } else {
                    updatePage(resp);
                }
            },
            error: function(resp, textStatus, errorThrown) {
                alert(lang.translate("Server response error.") + '\nResponseCode: ' + resp.status + 
                                                                 '\ntextStatus: ' + textStatus +
                                                                 '\nerrorThrown: ' + errorThrown +
                                                                 '\n' + lang.translate("Server is not returned settings."));
            },
        });
    }

    
    function updatePage(property) {        
        if (property.server_on) {
            $('#off-on-control').prop('checked', true).parents('.list-group-item').removeClass('text-danger').addClass('text-success');
            if (property.dlna_web_status) {
                if (property.dlna_web_status.scan) {
                    $('#dlna-web-status-scan').removeClass('text-success').addClass('text-danger').text(lang.translate("Scanning...")).removeClass('invisible');
                    setTimeout(getSettings, 5000);
                } else {
                    $('#dlna-web-status-scan').removeClass('text-danger').addClass('text-success').text(lang.translate("Scanning is complete.")).removeClass('invisible');
                }
                $('#dlna-web-status-video').removeClass('d-none').text(property.dlna_web_status.video);
                $('#dlna-web-status-audio').removeClass('d-none').text(property.dlna_web_status.audio);
                $('#dlna-web-status-picture').removeClass('d-none').text(property.dlna_web_status.picture);
                
            }
        } else {
            $('#off-on-control').prop('checked', false).parents('.list-group-item').removeClass('text-success').addClass('text-danger');
            $('#dlna-web-status-scan').addClass('invisible');
            $('#dlna-web-status-video').addClass('d-none');
            $('#dlna-web-status-audio').addClass('d-none');
            $('#dlna-web-status-picture').addClass('d-none');
        }
        if (property.autostart) {
            if (property.autostart === 'yes') {
                $('#autostart-control').prop('checked', true);
            } else {
                $('#autostart-control').prop('checked', false);
            }
        }
        if (property.current_settings.inotify) {
            if (property.current_settings.inotify === 'yes') {
                $('#autosearch-new-file-control').prop('checked', true);
            } else {
                $('#autosearch-new-file-control').prop('checked', false);
            }
        }
        if (property.current_settings.strict_dlna) {
            if (property.current_settings.strict_dlna === 'yes') {
                $('#dlna-standart-control').prop('checked', true);
            } else {
                $('#dlna-standart-control').prop('checked', false);
            }
        }
        if (property.current_settings.log_level) {
            if (property.current_settings.log_level === 'on') {
                $('#loging-control').prop('checked', true);
            } else {
                $('#loging-control').prop('checked', false);
            }
        }
    }
    
    
    function NotifyAlert(head, text) {
        var alertBlock = $('<div class="alert alert-dismissible alert-secondary fade show" id="myAlert">' +
                        '<button type="button" class="close" data-dismiss="alert" onfocus="this.blur();">&times;</button>' +
                        '<h5 lang="en">' + head + '</h5><h6 lang="en">' + text + '</h6>' +
                    '</div>');

        $("#notifies_alert").hide().append(alertBlock).slideDown(600);

        setTimeout(function() {
            $("#myAlert").alert("close");
        }, 5000);
    }
    

});