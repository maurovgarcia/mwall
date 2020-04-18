$(function() {

    iniApp();
 
    function iniApp() {
        window.setInterval(function(){
            requestAJAX({
                message: '',
                method: 'getMessages',
                id_message: ''
            });
        }, 3000);

        $( ".form-control" ).val('');
        $( ".btn-secondary" ).hide();
    }

    // Guardar un mensaje
    $(" .btn-success ").on('click', function() {

        if($( ".form-control" ).val() == '') {
            Swal.fire({
                icon: 'warning',
                title: '¡Debes escribir un mensaje!',
                text: 'El campo donde debes escribir el mensaje se encuentra vacío.',
                confirmButtonText: 'Aceptar'
            });
        } else {
            requestAJAX({
                message: $( ".form-control" ).val(),
                method: $( ".form-control" ).prop('id') == '0' ? 'setInsertMessage' : 'setUpdateMessage',
                id_message: $( ".form-control" ).prop('id')
            });
        }
        iniApp();

    });

    //Editar un mensaje
    $(document).on('click', " .btn-primary " , function() {
        $( ".btn-secondary" ).show();
        $( ".form-control" ).attr("id", $( this ).parent().parent().prop('id'));
        $( ".form-control" ).val($( this ).parent().find('#message').text());
    });

    //Cancelar un mensaje
    $(document).on('click', " .btn-secondary " , function() {
        iniApp();
    });
    
    // Eliminar un mensaje
    $(document).on('click', " .btn-danger " , function() {
        requestAJAX({
            message: '',
            method: 'setDeleteMessage',
            id_message: $( this ).parent().parent().prop('id')
        });
    });
    
    function requestAJAX( Object ) {
        let request = $.ajax({
            method: "POST",
            url: "application/controller.php",
            data: Object,
            dataType: "json",
            beforeSend: function() {
                //cargando
            }
        });
        request.always(function() {
            //hecho
        });
        request.done(function( responseve ) {
            if(Object.method != 'getMessages') {
                Swal.fire({
                    icon: responseve.result,
                    title: responseve.title,
                    text: responseve.message,
                    confirmButtonText: 'Aceptar'
                });
            }
            callBack(responseve, Object.method);
            return responseve;
        });
        request.fail(function( jqXHR, textStatus ) {
            Swal.fire({
                icon: 'error',
                title: 'Request failed',
                text: textStatus,
                confirmButtonText: 'Aceptar'
            });
        });
    }

    function callBack(data, method) {
        switch (method) {
            case 'getMessages':
                displayMessages(data);
                break;
        
            default:
                break;
        }
    }

    function displayMessages(messages) {
        let html = '';
        if(messages.data.length == 0) {
            html = '<div class="card text-center">  \
                            <div class="card-header">  \
                            ' + messages.title +'  \
                            </div>  \
                            <div class="card-body">  \
                            <blockquote class="blockquote mb-0">  \
                                <p>' + messages.message + '</p>  \
                                <footer class="blockquote-footer">Que viva la <cite title="Source Title">libre expresión!</cite></footer>  \
                            </blockquote>  \
                            </div>  \
                        </div>';
        } else {
            html = '';
            $( messages.data ).each(function( index, value ) {
                let htmlEdite = value.is_client_message == 1 ? '<button type="button" class="btn btn-danger btn-sm">Eliminar</button> \
                <button type="button" class="btn btn-primary btn-sm">Editar</button>' : '';
                let htmlUpdateDatetime = value.date_time_update != '0000-00-00 00:00:00' ? '<small class="text-muted">Ultima edición ' + value.date_time_update + '</small>' : '';

                html += '<div class="card" id="' + value.id + '">  \
                            <div class="card-body"> \
                                <p class="card-text" id="message">' + value.message + '</p> \
                                ' + htmlEdite +'<p class="card-text"> \
                                    <small class="text-muted">Publicación ' + value.date_time_insert + '</small><br> \
                                    ' + htmlUpdateDatetime + '</p> \
                            </div> \
                        </div>';
            });
        }
        $(" .card-columns ").html(html);
    }

});