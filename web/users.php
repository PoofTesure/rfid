<?php
require './script/connect.php';
session_start();

//Check if user logged in, if not redirect to login
if(empty($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true){
    header("Refresh:0,login.php");
}

//Grab all the users from our database
$sql = "SELECT name, rfid_uid,id,contact from id_rfid";
$users = mysqli_query($database,$sql);
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
        <a class="navbar-brand" href="index.php">RFID System</a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="attendance.php" class="nav-link">View Data</a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link active">View Users</a>
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
        <div class="row">
		<div class="col">
            <h2>Users</h2></div>
		<div class="col">
		    <a class="btn btn-primary float-right align-bottom" href="add_users.php" role="button">Tambah User</a></div>
        </div>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">RFID UID</th>
                    <th scope='col'>Contact</th>
		            <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
		        $index = 0;
                //Loop through and list all the information of each user including their RFID UID
                foreach($users as $user) {
		            $index+=1;
                    echo '<tr>';
                    echo '<td scope="row">' . $index . '</td>';
                    echo '<td>' . $user['name'] . '</td>';
                    echo '<td>' . $user['rfid_uid'] . '</td>';
                    echo '<td>' . $user['contact'] . '</td>';
		            echo "<td><a href='add_users.php?edit=1&uid=$user[id]'>Edit</a> | <a href='add_users.php?delete=1&uid=$user[id]'>Delete</a></td>";
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</html>
