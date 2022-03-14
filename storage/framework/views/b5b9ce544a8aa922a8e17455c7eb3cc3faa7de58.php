<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox chat-view">
                <div class="ibox-title">
                    <div class="row">
                        <small class="pull-right text-muted last-message-tms hidden-sm hidden-xs">Последнее сообщение: <span><?=$lastTms;?></span></small>
                        <button class="btn btn-warning visible-sm-inline-block visible-xs-inline-block pull-right toggle-user-list"><i class="fa fa-users" aria-hidden="true"></i> Собеседники</button>
                        <span class="active-user-name"><h4></h4></span>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="chat-discussion"><h2>Выберите собеседника</h2></div>
                        </div>
                        <div class="col-md-3 hidden-sm hidden-xs users">
                            <div class="chat-users">
                                <div class="users-list"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <textarea class="form-control message-input" name="message" placeholder="Введите текст сообщения"></textarea>
                                <span class="text-danger send-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <button id="sendMessage" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Отправить</button>
                                
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalUsers"><i class="fa fa-comments-o" aria-hidden="true"></i> Массовая рассылка</button>
                                
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group pull-right">
                                <input
                                        type="file"
                                        name="file"
                                        id="sendFile"
                                        data-buttonName="btn-primary"
                                        data-iconName="fa fa-paperclip"
                                        data-buttonText="Прикрепить файл"
                                        data-size="sm"
                                        class="btn btn-info btn-sm filestyle"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/messanger/read.php" class="btn btn-default pull-right hidden-sm hidden-xs"><i class="fa fa-eye" aria-hidden="true"></i> Сообщения пользователей</a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <span class="text-muted">Обращаем Ваше внимание, что переписка может быть просмотрена администрацией школы в целях повышения эффективности обратной связи</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="modalUsers">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Выберите адресатов сообщения</h4>
                </div>
                <div class="modal-body">
                    <div class="mailing-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="sendMessages"><i class="fas fa-paper-plane"></i> Отправить</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        $(function() {
            var lastId= 0, openGroups=[0], selectedContact=1;
            loadContacts(selectContact);
            loadMailingList();

            setInterval( function() {
                loadMessages(selectedContact);
                loadContacts();
            } , 5000);

            function loadContacts(callback) {
                var data = {
                    'userId': selectedContact,
                    'openGroups': openGroups
                };
                $.getJSON('/messenger/getContacts', data, function (response) {
                    for (groupId in response.contacts) {
                        if (openGroups.indexOf(groupId)!=-1) {
                            response.contacts[groupId].open=true;
                        }

                        for (key in response.contacts[groupId].users) {
                            if (response.contacts[groupId].users[key].id==selectedContact) {
                                response.contacts[groupId].users[key].selected=true;
                            }
                        }
                    }

                    $('.users-list').html(nunjucks.render('messenger-contacts.html', response));

                    if (callback!==undefined)
                        callback();
                });
            }

            function loadMailingList() {
                var data = {
                    'userId': selectedContact
                };

                $.getJSON('/messenger/getContacts', data, function (response) {
                    $('.mailing-list').html(nunjucks.render('messenger-contacts-mailing-list.html', response));
                });
            }

            function selectContact() {
                if (!isNaN(selectedContact)) {
                    var userBlock=$('#user-'+selectedContact);
                    $('.active-user-name').html('<div class="pull-left hidden-sm hidden-xs"><h4>Чат с пользователем:</h4></div> <div class="pull-left" style="margin-left: 20px;"><h4>' + userBlock.html() + '</h4></div>');
                    $('.chat-user-name').removeClass('bg-success');
                    userBlock.parent().addClass('bg-success');
                    loadMessages(selectedContact);
                }
            }

            function loadMessages(to) {
                if (to > 0) {
                    var data = {
                        'to': to,
                        'lastId': lastId
                    };

                    $.getJSON('/messenger/getMessages', data, function (response) {
                        if (lastId==0) $('.chat-discussion').html('');

                        if (response.viewAll) {
                            $(".message-author .viewed").removeClass('hidden');
                        }

                        if (response.lastId>lastId || lastId==0 ) {
                            $('.last-message-tms span').text(response.lastTms);
                            $('.chat-discussion').prepend(nunjucks.render('messenger-chat.html', response));
                            lastId = response.lastId;
                            var dataView = {
                                'lastId': lastId,
                                'from': selectedContact
                            };
                            $.getJSON('/messenger/viewMessages', dataView, function (response) {});

                            $(".chat-discussion .message-content:not(.link)").each( function( index ) {
                                $(this).addClass('link');
                                $(this).html($(this).html().replace(/https?:\/\/[^ ]+/g, '<a href="$&" target="_blank">$&</a>'));
                            });
                        }
                    });
                }
            }

            var files=[];
            $('#sendFile').on('change', prepareUpload);

            function prepareUpload(event) {
                files = event.target.files;
            }

            function sendMessage() {
                $(".send-error").text('');

                var text=$(".message-input").val(),
                    file = $('#sendFile').val();
                if (text=='' && file=='') {
                    $(".send-error").text('Введите текст сообщения или файл');
                }

                uploadFiles(function(files) {
                    var data = {
                        'text': $(".message-input").val(),
                        'to': [selectedContact],
                        'files': files
                    };

                    $.getJSON('/messenger/sendMessage', data, function (response) {
                        loadMessages(selectedContact);
                        $(".message-input").val('');
                        $('#sendFile').filestyle('clear');
                    });
                });
            }

            function uploadFiles(callback) {
                // Create a formdata object and add the files
                var data = new FormData();

                $.each(files, function(key, value) {
                    data.append(key, value);
                });

                $.ajax({
                    url: '/messenger/uploadFile',
                    type: 'POST',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    processData: false, // Don't process the files
                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                    success: function(data, textStatus, jqXHR)
                    {
                        if(typeof data.error === 'undefined')
                        {
                            callback(data.files);
                        }
                        else
                        {
                            // Handle errors here
                            console.log('ERRORS: ' + data.error);
                            callback([]);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        // Handle errors here
                        console.log('ERRORS: ' + textStatus);
                        // STOP LOADING SPINNER
                        callback([]);
                    }
                });
            }

            $('.users-list').on('click', '.chat-user a', function(event){
                event.preventDefault();
                lastId=0;
                $('.chat-discussion').html('');
                selectedContact=$(this).attr('data-id');
                selectContact(selectedContact);

                $(".users").toggleClass('hidden-xs');
                $(".users").toggleClass('hidden-sm');
            });


            $("#sendMessage").click(function() {
                sendMessage();
            });

            $(".message-input").keypress(function(event) {
                if ((event.ctrlKey || event.metaKey) && (event.keyCode == 13 || event.keyCode == 10)) {
                    event.preventDefault();
                    sendMessage();
                }
            });

            $("#sendMessages").click(function() {
                var receivers = [];
                $("input[name='mailto[]']:checked").each(function() {
                    receivers.push($(this).val());
                });

                $(".send-error").text('');

                var text=$(".message-input").val();
                if (text=='') {
                    $(".send-error").text('Введите текст сообщения');
                }

                uploadFiles(function(files) {
                    var data = {
                        'text': $(".message-input").val(),
                        'to': receivers,
                        'files': files
                    };

                    $.getJSON('/messenger/sendMessage', data, function (response) {
                        $(".message-input").val('');
                        swal("Сообщение отправлено", '', "success");
                    });
                });

            });

            $('.mailing-list').on('click', '.select-group', function(event){
                if ($(this).prop('checked')) {
                    $('.group-'+$(this).attr('data-group-id')).prop('checked',true);
                }
                else {
                    $('.group-'+$(this).attr('data-group-id')).prop('checked',false);
                }
            });

            $('.users-list').on('show.bs.collapse', function (e) {
                openGroups.push(e.target.id);
                $('#group-'+e.target.id).css('font-weight','bold');
            });

            $('.users-list').on('shown.bs.collapse', function (e) {
                if (e.target.id=='05-admin')
                    $('.chat-users').scrollTo($("#group-"+e.target.id),300);
            });

            $('.users-list').on('hide.bs.collapse', function (e) {
                openGroups.pop(e.target.id);
                $('#'+e.target.id).css('font-weight','normal');
            });

            $('.toggle-user-list').click(function() {
                $(".users").toggleClass('hidden-xs');
                $(".users").toggleClass('hidden-sm');
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/messenger.blade.php ENDPATH**/ ?>