<?php
require './script/connect.php';
session_start();

//Check if user logged in, if not redirect to login
if(empty($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true){
    header("Refresh:0,login.php");
}

$error="";
if(isset($_POST['saveid']))
{
	//Save add/edit id
	$txtname=$_POST['txtname'];
	$txtid=$_POST['txtid'];
	$txtcontact = $_POST['txtcontact'];

	//Error empty form
	if(empty($txtname) || empty($txtid)){
		echo '<script language="javascript">';
		echo 'alert("Nama dan ID tidak boleh kosong")';
		echo '</script>';
		header('Refresh:0');
	}
	else{
		//Save add id
		if($_POST["txtuid"]=="0"){
			if(!$stmt = $database -> prepare("INSERT INTO id_rfid (name,rfid_uid,contact) VALUES(?,?,?)")){
				echo "Error " . $database -> error;
			}
			$stmt -> bind_param('sss',$txtname,$txtid,$txtcontact);
			if(!$stmt -> execute()){
				echo "Error " . $database -> error;
			}
			header("Refresh:0, users.php");
		}
		else {
			//Save edit
			$stmt = $database -> prepare("UPDATE id_rfid SET name=?,rfid_uid=?,contact=? WHERE id=?");
			$stmt -> bind_param('ssi',$txtname,$txtid,$txtcontact,$_GET['uid']);
			if(!$stmt -> execute()){
				echo "Error " . $database -> error;
			}
			header('Refresh:0; users.php');
		}
	    }
}


if(isset($_GET['edit']))
{	
	//Edit id
	$updid = $_GET['uid'];
	$stmt = $database -> prepare ("SELECT * from id_rfid WHERE id = ?");
	$stmt -> bind_param("i",$updid);
	if(!$stmt -> execute()){
		echo "Error " . $database -> error;
	}
	$result = $stmt -> get_result();
	$result = $result ->fetch_assoc();
	$dbname = $result['name'];
	$dbid = $result['id'];
	$dbrfid_uid = $result['rfid_uid'];
	$dbcontact = $result['contact'];

}
if(isset($_GET['delete'])){
	//Delete id
	$stmt = $database -> prepare("DELETE FROM id_rfid WHERE id = ?");
	$stmt -> bind_param("i", $deleteid);
	$deleteid = $_GET['uid'];
	if(!$stmt -> execute()){
		echo "Error " . $database -> error;
	}
	header('Refresh:0; users.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title> Tambah User</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/bootstrap.min.js"></script>
	<script
  		src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">RFID System</a>
    </nav>
<div class="container">
	<br>
	<form action="" method="post" class="d-flex justify-content-center align-items-center">
		<table>
			<tr>
				<td> Nama </td>
				<td><input type="text" name="txtname" value ="<?php echo $dbname; ?>" size = 50> <input type="hidden" name='txtuid' value="<?php if(isset($dbid)){echo '1';} else {echo '0';} ?>" </>
			</tr>
			<tr>
				<td> Email </td>
				<td><input type="text" name="txtcontact" value ="<?php echo $dbcontact;?>" size = 50>
			</tr>
			<tr>
				<td>RFID UID</td>
				<td><input type="text" name="txtid" id="txtid" value="<?php echo $dbrfid_uid; ?>"></td>
				<td><button class="btn" name="scan" value="Scan" onclick=scan_alert()>Scan</button>
				
			</tr>
			<tr>
				<td><input type="submit" value="Save" name="saveid" class='btn btn-primary'></td>
				<td><a href="users.php" class='btn btn-primary'>Back</a>
			</tr>
	</form>
</div>
<script>
	scan_id = () =>{
		$.get('/script/scan.php').done(function(data){
			console.log(data);
			$('#txtid').val(data);
		})
	}
	scan_alert = () =>{
		alert("Silahkan Scan kartu/tag")
	}
		$('.btn').click(scan_id());
</script>
</body>
</html>
