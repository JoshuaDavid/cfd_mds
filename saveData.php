<?
	$job_size = 50;
	$data = file_get_contents('datastore.txt');
	$IP = $_SERVER['REMOTE_ADDR'];
	$rows = explode("\n", $data);
	$completed = 1;
	foreach($rows as $r) {
	    if(preg_match("/" . $IP . "/", $r)) {
		$completed += 1;
	    }
	}
	$row = "";
	foreach($_POST as $p) {
		$row .= "{$p}\t";
	}
	$row .= "{$_SERVER['REMOTE_ADDR']}\t";
	$row .= "{$_SERVER['REQUEST_TIME']}\n";
	
	if($completed < $job_size) {
	    echo "Success:";
	    echo "Completed {$completed} of {$job_size}";
	    $data = $row;
	    $result = file_put_contents('datastore.txt', $data, FILE_APPEND);
	    var_dump($result);
	}
	else {
	    echo "You have already finished your {$job_size} allotted problems.";    
	}
