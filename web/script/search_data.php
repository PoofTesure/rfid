<?php
    require 'connect.php';
    $sql = "SELECT id, name FROM id_rfid";
    $users = mysqli_query($database,$sql);
    if(isset($_GET)){
        $month = $_GET['month'];
        $year = $_GET['year'];
        $num_days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        echo "<table class='table table-striped table-responsive'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>";
        echo "<th scope='col'>Name</th>";
        for ($iter = 1; $iter <= $num_days; $iter++){
            echo '<th scope="col" style="min-width:220px;max-width:400px;">' . $iter . '</th>';
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
         //Loop through all our available users
         foreach($users as $user) {
            echo '<tr>';
            echo '<td scope="row">' . $user['name'] . '</td>';

            //Iterate through all available days for this month
            for ( $iter = 1; $iter <= $num_days; $iter++) {
            
                $stmt = $database -> prepare("SELECT clock_in, picture FROM data_id WHERE user_id = ? AND clock_in BETWEEN ? AND ?");
                $clock1 = date('Y-m-d', mktime(0, 0, 0, $month, $iter, $year));
                $clock2 = date('Y-m-d', mktime(24, 60, 60, $month, $iter, $year));
                $stmt -> bind_param('iss',$user['id'],$clock1,$clock2);
                if(!$stmt -> execute()){
                    echo "Error " . $database -> error;
                }
                $attendance = $stmt -> get_result();
                $attendance = $attendance -> fetch_all(MYSQLI_ASSOC);
                //Check if our database call actually found anything
                if(!empty($attendance)) {
                    //If we have found some data we loop through that adding it to the tables cell
                    echo '<td class="table-success">';
                    foreach($attendance as $attendance_data) {
                        echo $attendance_data['clock_in'];
                        if(!empty($attendance_data['picture'])){
                            echo "<a href='" . $attendance_data['picture'] . "'" . "> Photo</a>";}
                        echo "<br>";
                    }
                    echo '</td>';
                } else {
                    //If there was nothing in the database notify the user of this.
                    echo '<td class="table-secondary">Tidak ada data</td>';
                }
            }
            echo '</tr>';
        }
    }
?>