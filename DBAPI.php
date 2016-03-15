<?php

//if(isset($_POST['action']) && !empty($_POST['action'])) {
//	$action = $_POST['action'];
//	switch($action) {
//		case 'table_content': getTableContents($_POST['table']); break;
//		case 'authenticate': authenticateUser($_POST['server'],$_POST['user'],$_POST['pass']); break;
//	}
//
//}

function authenticateUser($server, $user, $pass) {	
	$dbConn = new mysqli($server, $user, $pass);
	if($dbConn->connect_error) {
		$val = $dbConn->connect_error;
	}
	else {
		$val = 0;
	}
	$dbConn->close();
	$result = array();
	$result['error'] = $val;
        
	return $result;
}

function getDatabases($server, $user, $pass) {
    $dbConn = new mysqli($server, $user, $pass);
	if($dbConn->connect_error) {
		$val = $dbConn->connect_error;
	}
	else {
		$val = 0;
	}
        
        $query = "SHOW DATABASES";
        
        $sqlResult = $dbConn->query($query);
        
        
	$dbConn->close();
	$result = array();
	$result['error'] = $val;
        $result['dbs'] = array();
        $i = 0;
        while( $row = $sqlResult->fetch_array()) {
            $result['dbs'][$i] = $row[0];
            $i++;
        }
        
	return $result;
}

function getTables($server, $user, $pass, $dbName) {
    $dbConn = new mysqli($server, $user, $pass);
	if($dbConn->connect_error) {
		$val = $dbConn->connect_error;
	}
	else {
		$val = 0;
	}
        
        $query = "SHOW TABLES IN " . $dbName;
        
        $sqlResult = $dbConn->query($query);
        
        
	$dbConn->close();
	$result = array();
	$result['error'] = $val;
        $result['tables'] = array();
        $i = 0;
        while( $row = $sqlResult->fetch_array()) {
            $result['tables'][$i] = $row[0];
            $i++;
        }
        
	return $result;
}