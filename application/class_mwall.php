<?php
    class Mwall {

        private $mysqli;
        public $result;
        public $data = array();

        function __construct() {
            $this->mysqli = new mysqli('localhost', 'root', '', 'mwall');
            if ($this->mysqli->connect_errno) {
                $this->result = array('error', 'connetion', 'Fallo al conectar a MySQL: (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error, '');
            }
        }

        function getMessages() {
            $dataFinally = array();
            $query = $this->mysqli->prepare('SELECT id, client_ip, message, date_time_insert, date_time_update, 0 AS is_client_message FROM messages  ORDER BY date_time_insert, date_time_update ASC');
            $query->execute();
            $resultSet = $query->get_result();
            $data = $resultSet->fetch_all(MYSQLI_ASSOC);
            
            foreach ($data as $key => $value) {
                $dataFinally[$value['id']] = $value;
            }
            $this->result = $query->affected_rows;
            $this->data = $dataFinally;
        }

        function setInsertMessage($message) {
            $query = $this->mysqli->prepare('INSERT INTO messages (client_ip, message) VALUES(?, ?)');
            $query->bind_param('ss', $_SERVER['REMOTE_ADDR'], $message);
            $query->execute();
            setcookie('mwall-messages[' . $query->insert_id . ']', $query->insert_id, time() + (10 * 365 * 24 * 60 * 60));
            $this->result = $query->affected_rows;
        }

        function setUpdateMessage($id, $message) {
            $query = $this->mysqli->prepare('UPDATE messages SET message = ?, client_ip = ?, date_time_update = NOW() WHERE id = ?');
            $query->bind_param('sss', $message, $_SERVER['REMOTE_ADDR'], $id);
            $query->execute();
            $this->result = $query->affected_rows;
        }

        function setDeleteMessage($id) {
            $query = $this->mysqli->prepare('DELETE FROM messages WHERE id = ?');
            $query->bind_param('s', $id);
            $query->execute();
            setcookie('mwall-messages[' . $id . ']', '', time() + (10 * 365 * 24 * 60 * 60)); 
            $this->result = $query->affected_rows;
        }

        function __destruct() {
            $this->mysqli->close();
        }
    }

?>