<?php
    $url = "localhost/script/checkid.php";
    $data = [
        'id' => 123
    ];

    $field_string = http_build_query($data);
    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$field_string);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
?>