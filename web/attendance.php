<?php
    require './script/connect.php';
    session_start();
    if(empty($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true){
        header("Refresh:0,login.php");
    }

    $sql = "SELECT clock_in FROM data_id ORDER BY clock_in ASC limit 1";
    $start = $database -> query($sql) -> fetch_assoc();
    $start = $start['clock_in'];
    $start = explode("-",$start);
    $sql = "SELECT clock_in FROM data_id ORDER BY clock_in DESC limit 1";
    $end = $database -> query($sql) -> fetch_assoc();
    $end = $end['clock_in'];
    $end = explode("-",$end);

?>

<!DOCTYPE html>
<html lang=id>
    <head>
        <title>RFID System</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script
  		src="https://code.jquery.com/jquery-3.5.1.min.js"
  		integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  		crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">RFID System</a>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="attendance.php" class="nav-link active">View Data</a>
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
            <div class="row">
                <div class=col>
                    <h2>Data</h2>
                </div>
                <div class=col>
                    <a href="/script/export.php" class="btn btn-primary float-right">Export ke Excel</a>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <select id="bulan" class="form-select" aria-label="Bulan">
                        <option selected disabled>Bulan</option>
                        <?php for ($m=1; $m<=12;$m++){
                            $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                            echo "<option value='$m'>$month</option>";
                            }
                            ?>
                    </select>
                    <select id="tahun" class="form-select" aria-label="Tahun">
                        <option selected>Tahun</option>
                        <?php 
                            $start = $start[0];
                            $end = $end[0];
                            for($start; $start <= $end; $start++){
                            echo "<option value='$start'>$start</option>";
                         }
                        ?>
                        </select>
                    <button class="btn search" name="search" value="Search" onclick=search_data()>Cari</button>
                    <script>
                        search_data = () =>{
                            var month = document.getElementById('bulan').value;
                            var year = document.getElementById('tahun').value;
                            var get_data = "month=";
                            get_data = get_data.concat(month,"&year=",year);
                            $.get("/script/search_data.php",get_data).done(function (data){
                                $('#attendance').html(data);
                            }
                            )
                        }
                    </script>
                </div>
            </div>
            <div class='row' id='attendance'>
            </div>
        </div>
    </body>
