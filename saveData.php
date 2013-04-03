<?
        require "./init.php";
	$data = file_get_contents('datastore.txt');
	$IP = $_SERVER['REMOTE_ADDR'];
	$rows = explode("\n", $data);
        // How many pairs the user has done this round
	$completed = 1;
        // How many pairs have been done by this IP address
        $completed_by_IP = 1;
	foreach($rows as $r) {
	    if(preg_match("/" . $_POST['confirmation'] . "/", $r)) {
		$completed += 1;
	    }
	    if(preg_match("/" . $IP . "/", $r)) {
		$completed_by_IP += 1;
	    }
	}
	$row = "";
	foreach($_POST as $p) {
		$row .= "{$p}\t";
	}
	$row .= "{$IP}\t";
	$row .= "{$_SERVER['REQUEST_TIME']}\n";
	
	if($completed <= $job_size && $completed_by_IP < $ip_limit) {
	    echo "Success:";
	    echo "Completed {$completed} of {$job_size}";
	    $data = $row;
	    $result = file_put_contents('datastore.txt', $data, FILE_APPEND);
	    var_dump($result);
	}
	else {
	    echo "You have already finished your {$job_size} allotted problems.";    
	}
