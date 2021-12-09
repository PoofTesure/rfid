<?php
require './script/connect.php';
session_start();

//Check if user logged in, if not redirect to login
if(empty($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true){
    header("Refresh:0,login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>RFID System</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>
    </head>
    <body>

    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">RFID System</a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="attendance.php" class="nav-link">View Data</a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link">View Users</a>
            </li>
            <li class="nav-item">
                <a href="register.php" class="nav-link">Register</a>
            </li>
            <li class="nav-item">
                <a href="/script/logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container">
        <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
            <h1 class="mb-3">Welcome <?php echo $_SESSION['user'];?>,</h1>
            <p class="lead">
                To your RFID System Dashboard.
            </p>
            <div class="row mx-n2">
                <div class="col-md px-2">
                    <a href="users.php" class="btn btn-lg btn-outline-secondary w-100 mb-3">Users</a>
                </div>
                <div class="col-md px-2">
                    <a href="attendance.php" class="btn btn-lg btn-outline-secondary w-100 mb-3" >Data</a>
                </div>
            </div>
        </div>
    </div>
</html>
