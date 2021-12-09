<?php
    require "connect.php";
    
    $sql = "SELECT * FROM otp;";
    $results = mysqli_query($database,$sql);
    #print_r($results);
    foreach ($results as $result){
        #print_r ($result);
        $date1 = new DateTime();
        $sqldate = $result['requested_at'];
        $date2 = new DateTime($sqldate);
        $interval = $date1 ->diff($date2);
        $interval = $interval -> d;
        if ($interval >= 1){
            $sql = $database -> prepare("DELETE FROM otp WHERE id=?");
            $sql -> bind_param("i",$result['id']);
            if(!$sql->execute()){
                echo "Error " . $database -> error;
			}
        }
    }

?>