<?php
    require 'connect.php';

    if(isset($_POST['id'])){
        $uid = $_POST['id'];
        //echo $uid . " ";
        $sql = $database -> prepare("SELECT id, name FROM id_rfid WHERE rfid_uid=?");
        $sql -> bind_param("s",$uid);
        if (!$sql -> execute()){
            echo "0";
            exit;
        }
        $result = $sql -> get_result() -> fetch_assoc();
        if (empty($result)){
            echo "0";
            exit;
        }
        $name = $result['name'];
        $id = $result['id'];

        echo "1," . $name;
    }
?>