<?php
    if (!function_exists('imap_open')){
        echo "IMAP is not configured";
        exit;
    }
    else{
        $host = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $connection = imap_open($host,"poofedt@gmail.com","Superman1997PoofedT") or die ("Can't connect to gmail " . imap_last_error());
        #while($connection){
            $date = date("d-M-Y");
            $emails = imap_search($connection, strtoupper("UNANSWERED ON " . $date));
            #echo strtoupper("UNSEEN ON " . $date);
            if ($emails){
                echo "Email present \r\n";
                $url = "/qr.php";
                foreach($emails as $email){
                    $header = imap_headerinfo($connection, $email);
                    $fromemail = $header -> sender;
                    $mailbox = $fromemail[0] -> mailbox;
                    $email_host = $fromemail[0] -> host;

                    $fromemail = $mailbox . "@" . $email_host;
                    
                    $url = "localhost/script/qr.php";
                    $data = [
                        'contact' => $fromemail
                    ];

                    $field_string = http_build_query($data);
                    
                    $ch = curl_init();

                    curl_setopt($ch,CURLOPT_URL,$url);
                    curl_setopt($ch,CURLOPT_POST,true);
                    curl_setopt($ch,CURLOPT_POSTFIELDS,$field_string);

                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

                    $result = curl_exec($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if($httpcode == 200){
                        $status = imap_setflag_full($connection, $email, "\\Seen \\Answered");
                        echo $status;
                    }
                    
                    
                }
            }
        #}
    }
?>