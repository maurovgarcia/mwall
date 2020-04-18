<?php

// $_POST['message'] = 'Prueba de mensaje YO - Editado';
// $_POST['method'] = 'getMessages';
/*
getMessages
setInsertMessage
setUpdateMessage
setDeleteMessage
*/
// $_POST['id_message'] = '3';

    // try {

        require 'class_mwall.php';

        if(empty($_POST)) {
            $result = array('error', 'No se recibieron parametros', 'No se recibieron parametros para ejecutar el proceso, intenta nuevamente.', '');
        } else {

            $messages_published_client  = isset($_COOKIE['mwall-messages']) ? $_COOKIE['mwall-messages'] : false;
            $parameters                 = $_POST;
    
            $Mwall = new Mwall();
            if(is_array($Mwall->result)) {
                $result = $Mwall->result;
            } else {
    
                switch ($parameters['method']) {
                    case 'getMessages':
                        $Mwall->getMessages();
                        if($Mwall->result > 0) {
                            $messages = $Mwall->data;
        
                            if($messages_published_client) {
                                foreach ($messages_published_client as $key => $value) {
                                    if(array_key_exists($value, $messages)) {
                                        $messages[$value]['is_client_message'] = 1;
                                    }
                                }
                            }
                            $messages = array_values($messages);
                            $result = array('success', 'Mensajes disponibles', '', $messages);
                        } else {
                            $result = array('success', '¡Sé tú el primero que publique un mensaje!', 'No hay mensajes para mostrar.', '');
                        }
                        break;
        
                    case 'setInsertMessage':
                        $Mwall->setInsertMessage($parameters['message']);
        
                        if($Mwall->result == 1) {
                            $result = array('success', '¡Mensaje publicado!', 'Gracias por publicar tu mensaje, puedes agregar otro si quieres, o editar el que ya publicaste.', '');
                        } else {
                            $result = array('error', '¡Error al publicar tu mensaje!', 'Se ha presentado un error al publicar tu mensaje, intenta nuevamente.', '');
                        }
        
                        break;
        
                    case 'setUpdateMessage':
                        $Mwall->setUpdateMessage($parameters['id_message'], $parameters['message']);
        
                        if($Mwall->result == 1) {
                            $result = array('success', '¡Mensaje actualizado!', 'Gracias por actualizar tu mensaje, puedes agregar otro si quieres.', '');
                        } else {
                            $result = array('error', '¡Error al actualizar tu mensaje!', 'Se ha presentado un error al actualizar tu mensaje, intenta nuevamente.', '');
                        }
    
                        break;
        
                    case 'setDeleteMessage':
                        $Mwall->setDeleteMessage($parameters['id_message']);
        
                        if($Mwall->result == 1) {
                            $result = array('success', '¡Mensaje eliminado!', 'Lamentamos que hayas borrado tu mensaje, sin embargo, puedes agregar otro si quieres.', '');
                        } else {
                            $result = array('error', '¡Error al eliminar tu mensaje!', 'Se ha presentado un error al eliminar tu mensaje, intenta nuevamente.', '');
                        }
                        break;
                    
                    default:
                        $result = array('error', 'Error', 'Método ' . $parameters['method'] . ' no especificado.', '');
                        break;
                }
    
            }

        }

    // } catch (Exception $e) {
    //     $result = array('error', 'Error', $e);
    // } finally {
        $result = array(
            'result'    => $result[0],
            'title'     => $result[1],
            'message'   => $result[2],
            'data'      => $result[3]
        );
        echo json_encode($result);

        // echo '<pre>';
        // var_export($result);
        // echo '</pre>';
    // }

?>