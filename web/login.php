<?php

require './script/connect.php';
session_start();


if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)){
        echo '<script language="javascript">';
        echo 'alert("Nama dan ID tidak boleh kosong")';
        echo '</script>';
    }

    else{
    //Prepare SQL statement

    $stmt = $database -> prepare("SELECT * FROM users WHERE username=? ");
    $stmt -> bind_param('s', $username);
    $stmt -> execute();
    
    $result = $stmt ->get_result();
    $result = $result -> fetch_assoc();
    
    if(!$result){
        echo '<script language="javascript">';
		echo 'alert("Username atau Password salah")';
		echo '</script>';
    } else{
        if(password_verify($password, $result['password'])){
            //Create session when user logged in
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = $result['username'];
            header("Refresh:0,index.php");
        }
        else{
            echo '<script language="javascript">';
		    echo 'alert("Username atau Password salah")';
		    echo '</script>';
        }
    }
}
    
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>
    </head>    
    <body>
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">RFID System</a>
    </nav>
    <div class="container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" placeholder="Enter username" name="username">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password">
            </div>
            <button type="submit" class="btn btn-primary" name="login">Submit</button>
        </form>
    </div>
    </body>
</html>
