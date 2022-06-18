<?php
    require 'connect.php'; # Connect database

    if(isset($_POST['id'])) # Check if there's a HTTP POST Request
    {
        $uid = $_POST['id']; #Set uid from POST Request
        //echo $uid . " ";
        $sql = $database -> prepare("SELECT id, name FROM id_rfid WHERE rfid_uid=?"); # Prepare SQL biar gk SQL injection
        $sql -> bind_param("s",$uid); #Bind parameter UID ke rfid_uid
        if (!$sql -> execute()){ #Execute query if fail echo 0
            echo "0";
            exit;
        }
        $result = $sql -> get_result() -> fetch_assoc(); #Fetch result then put in assoc array
        if (empty($result)){
            echo "0";
            exit;
        }
        $name = $result['name'];
        $id = $result['id'];

        echo "1," . $name;
    }
?>