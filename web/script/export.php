<?php

require 'connect.php';

$query = "SELECT * FROM id_rfid";
$users = mysqli_query($database,$query);

//Define the filename with current date
$fileName = "itemdata-".date('d-m-Y').".xls";

//Set header information to export data in excel format
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename='.$fileName);

$heading = false;

foreach($users as $user){
    $query = "SELECT * FROM data_id WHERE user_id =" . $user['id'];
    $results = mysqli_query($database,$query);
    //$results -> fetch_assoc();
    if(!empty($results)){
        foreach($results as $result){
            if(!$heading){
                echo  "Nama\tUser ID\tWaktu Akses\tAlamat Photo\n";
                $heading = true;
            } 
            echo $user['name'] . "\t";
            echo $result['user_id'] . "\t";
            echo $result['clock_in'] . "\t";
            if(!empty($result['picture'])){
            echo "C:\\Webserver\\NGINX" . $result['picture'];}
            echo "\n";
        }
    }
}

?>