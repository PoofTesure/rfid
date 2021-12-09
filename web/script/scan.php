<?php
	if(isset($_GET)){
		read();
		#echo "Post Received";
	}
	
	function read(){
		$data = array(
			'readMode'=> 1,
			'ID'=>" ");
		$data = json_encode($data);
		file_put_contents('readMode.json',$data);
		do{
			$data = file_get_contents('readMode.json');
			$data = (array)json_decode($data);
		}
		while($data['readMode'] == 1);
		sleep(1);
		$send_data = (array)json_decode(file_get_contents('readMode.json'));
		print_r($send_data['ID']);
	}
?>