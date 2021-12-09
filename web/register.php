<?php

require './script/connect.php';
session_start();

//Check if user logged in, if not redirect to login
if(empty($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true){
    header("Refresh:0,login.php");
}

if(isset($_POST['register'])){
    
    if(empty($_POST['username']) && empty($_POST['password'])){
        echo '<script language="javascript">';
        echo 'alert("Nama dan ID tidak boleh kosong")';
        echo '</script>';
    }
    else{

        if(!strcmp($_POST['username'],$_POST['confirm'])){
            echo '<script language="javascript">';
            echo 'alert("Password tidak sama")';
            echo '</script>';
        }
        else{
        
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            //Check username if exist
            $stmt = $database -> prepare("SELECT * FROM users WHERE username = ?");
            $stmt -> bind_param('s', $username);

            $result = $stmt ->get_result();
            if(!$result){
                //Prepare SQL Query for register and execute
            
                $stmt = $database -> prepare("INSERT INTO users (username, password) VALUES (? , ?)");
                $stmt -> bind_param('ss', $username,$password);
                $stmt -> execute();
            }
            else {
                echo '<script language="javascript">';
                echo 'alert("Username sudah ada")';
                echo '</script>';
                header("Refresh:0");
            }
        }
    }

}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>
    </head>    
    <body>
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">RFID System</a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="attendance.php" class="nav-link">View Data</a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link">View Users</a>
            </li>
            <li class="nav-item">
                <a href="register.php" class="nav-link active">Register</a>
            </li>
            <li class="nav-item">
                <a href="/script/logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
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
            <div class="form-group">
                <label for="confirm">Confirm Password:</label>
                <input type="password" class="form-control" placeholder="Confirm password" name="confirm">
            </div>
            <button type="submit" class="btn btn-primary" name="register">Register</button>
            <a href="index.php" class="btn btn-primary">Home</a>
        </form>
    </div>
    </body>
</html>
