<?php
	error_reporting(E_ERROR | E_PARSE);
	$c = new mysqli("localhost", "native_160821016", "ubaya", "native_160821016");

	if ($c->connect_errno) {
		echo json_encode(array('result' => 'ERROR', 'msg' => 'Failed to connect DB'));
		die();
	}

	if (!isset($_POST['username'])) {
		echo json_encode(array('result' => 'ERROR'));
		die();
	}

	$username = $_POST['username'];

	$sql = "SELECT C.idcerbung, C.title, C.username, C.url, max(date_format(substr(P.tanggal, 1, 10), '%d/%m/%Y')) as tanggal
			FROM cerbung as C 
			INNER JOIN user_follows_cerbung as F ON C.idcerbung = F.idcerbung
			INNER JOIN paragraf as P ON C.idcerbung = P.idcerbung 
			WHERE F.username=?
			GROUP BY C.idcerbung, C.title, C.username, C.url";
	$stmt = $c->prepare($sql);
	$stmt->bind_param("s", $username);
	$stmt->execute();

	$result = $stmt->get_result();
	
	$array = array();

	while ($obj = $result->fetch_object()) {
	    $array[] = $obj;
	}

	if (empty($array)) {
    	echo json_encode(array('result' => 'ERROR', 'msg' => 'No data found'));
	} else {
    	echo json_encode(array('result' => 'OK', 'data' => $array));
	}
?>