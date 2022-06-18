<?php

    require 'connect.php'; #Connect database

    print_r(realpath('var/www/html/data.json'));
    
    function captureCamera(){
        $data = array(
            'capture'=> 1,
            'filepath'=>""
        );

        $data = json_encode($data);
        file_put_contents('/var/www/html/capture.json',$data);
        do{
            $data = file_get_contents('/var/www/html/capture.json');
            $data = (array)json_decode($data);
        }
        while($data['capture'] == 1);
        return $data['filepath'];
    }

    if(isset($_POST['id'])){
        $uid = $_POST['id'];

        $sql = $database -> prepare("SELECT id, name FROM id_rfid WHERE rfid_uid=?");
        $sql -> bind_param("s",$uid);
        if (!$sql -> execute()){
            echo "0";
            exit;
        }
        $result = $sql -> get_result() -> fetch_assoc();
        $name = $result['name'];
        $id = $result['id'];

        $imagePath = captureCamera();

        $sql = $database -> prepare("INSERT INTO data_id (user_id,picture) VALUES (?,?)");
        $sql -> bind_param("ss", $id,$imagePath);
        
        if(!$sql -> execute()){
            echo "0";
            exit;
        }
        echo "1," . $name;
    }
    if(isset($_POST['otp'])){
        $otp = $_POST['otp'];

        $sql = $database -> prepare("SELECT user_id,active_for FROM otp WHERE otp_code=?");
        $sql -> bind_param("s",$otp);
        if(!$sql -> execute()){
            echo "Error";
            exit;
        }
        $result = $sql -> get_result() -> fetch_assoc();
        $userid = $result['user_id'];
        $active = $result['active_for'];

        if($active < 1){
            echo "0";
            exit;
        }

        $sql = $database -> prepare("UPDATE otp SET active_for=? WHERE otp_code =?");
        $sql -> bind_param("is",$active,$otp);
        if(!$sql -> execute()){
            echo "0";
            exit;
        }
        
        $imagePath = captureCamera();

        $sql = $database -> prepare("INSERT INTO data_id (user_id,picture) VALUES (?,?)");
        $sql -> bind_param("is",$userid,$imagePath);
        if(!$sql->execute()){
            echo "0";
            exit;
        }
        echo "1";
    }
?> 