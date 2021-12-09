<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';
    require 'connect.php';
    require 'phpqrcode/qrlib.php';

    if(isset($_POST['contact'])){
        $contact = $_POST['contact'];
        $stmt = $database -> prepare("SELECT id FROM id_rfid WHERE contact=?");
        $stmt -> bind_param('s',$contact);
        if(!$stmt -> execute()){
            echo "Error ". $database -> error;
        }
        $result = $stmt -> get_result();
        $result = $result -> fetch_assoc();
        
        if($result){
            $otp = rand(100000,999999);

            $stmt = $database -> prepare("INSERT INTO otp (user_id,otp_code,active_for) VALUES (?,?,2)");
            $stmt -> bind_param('ss',$result['id'],$otp);
            if(!$stmt -> execute()){
				echo "Error " . $database -> error;
			}

            $tempdir = "/var/www/html/qr/";
            $filename = $tempdir . 'qr.png';
            QRcode::png($otp,$filename,QR_ECLEVEL_L,4);
            
            
            //Mail
            $mail = new PHPMailer();
            $mail -> isSMTP();
            $mail -> SMTPDebug = 3;
            $mail -> Debugoutput = 'html';
            $mail -> Host = 'smtp.gmail.com';
            $mail -> Port = 587;
            $mail -> SMTPSecure = 'tls';
            $mail -> SMTPAuth = true;
            $mail -> Username = "poofedt@gmail.com";
            $mail -> Password = "Superman1997PoofedT";
            $mail->setFrom("rfid@database.com", "RFID");
            $mail->addAddress($_POST['contact'], $result['name']);
            $mail->Subject = 'QR Portal';
            $mail->Body = "QR";
            $mail->AltBody = 'This is a plain-text message body';
            $mail->addAttachment("/var/www/html/qr/qr.png");

            if(!$mail->send()){
                
                echo 'Mailer Error ' . $mail->ErrorInfo;}
            else{
                echo 'Email Success';
                http_response_code(200);
            }
           
        }
    }

    ?>
