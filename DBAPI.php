<?php
/**
 * Name: DBAPI.php
 * Author: Jason Duffey
 *         Ohio Northern University
 * Date: 3/2016
 * 
 * API for accessing an arbitrary database
 */

//Execute the function specified in the AJAX call by the 'method' data
require 'DBCreds.php';

echo $_POST['method']();

function sanitize($str, $quotes = ENT_NOQUOTES) {
//sanitize - sanitizes user input to prevent SQL Injection
//Parameters - str - the string to be sanitized
//             quotes - the special flags for the sanitizing function used
//Return - the sanitized string    
    $str = htmlspecialchars($str, $quotes);
    return $str;
}

function authenticateUser() {
//authenticateUser - authenticates a user on a DB Server, checks to see if they
//                   have an account on the SQL server
//                   needs a 'server', 'user', 'pass' to be posted to the page
//                   these correspond to the server to connect to and the 
//                   username and password to authenticate 
    
//Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password);
    
    //Check for errors, if 0 then successfully connected
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }

    $dbConn->close();

    //Put the result in to an array and return as JSON
    $result = array();
    $result['error'] = $val;
    
    return json_encode($result);
}

function getDatabases() {
//getDatabases - get the databases the specified user is allowed to view
//                   needs a 'server', 'user', 'pass' to be posted to the page
//                   these correspond to the server to connect to and the 
//                   username and password to authenticate
    
//Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    
    //create a new connection to the server
    $dbConn = new mysqli($server, $username, $password);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }

    //Execute the query to get the databases
    $query = "SHOW DATABASES";
    $sqlResult = $dbConn->query($query);  
    $dbConn->close();

    //Put the results into an array and return as JSON
    $result = array();
    $result['error'] = $val;
    $result['dbs'] = array();
    $i = 0;
    while( $row = $sqlResult->fetch_array()) {
        $result['dbs'][$i] = $row[0];
        $i++;
    }

    return json_encode($result);
}

function getTables() {
//getTables - get the tables the specified database
//                   needs a 'server', 'user', 'pass', 'db' to be posted to the page
//                   these correspond to the server to connect to and the 
//                   username and password to authenticate as well as the DB to
//                   get the tables of    

    //Check if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    if(isset($_POST['db'])) {
        $database = sanitize($_POST['db']);
    }
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }

    //Execute the query to get the tables
    $query = "SHOW TABLES IN " . $database;      
    $sqlResult = $dbConn->query($query);
    $dbConn->close();

    //Put the results into an array and return as JSON
    $result = array();
    $result['error'] = $val;
    $result['tables'] = array();
    $i = 0;
    while( $row = $sqlResult->fetch_array()) {
        $result['tables'][$i] = $row[0];
        $i++;
    } 
    
    return json_encode($result);
}

function getSchema() {
//getSchema - get the schema of the specified table in a DB
//                   needs a 'server', 'user', 'pass', 'db' , 'table' to be posted to the page
//                   these correspond to the server to connect to and the 
//                   username and password to authenticate as well as the DB and
//                   table to get the schema of    
    
    //Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    if(isset($_POST['db'])) {
        $database = sanitize($_POST['db']);
    }
    if(isset($_POST['table'])) {
        $table = sanitize($_POST['table']);
    }
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password, $database);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }
    
    //Execute the query to get the schema
    $query = "DESCRIBE " . $table;  
    $sqlResult = $dbConn->query($query);
    
    //Get the primary key columns
    $sql = "SHOW INDEX FROM ". $table . " WHERE key_name = 'PRIMARY';";
    $indexResult = $dbConn->query($sql);
    
    //Get the foreign keys for the table
    $foreignSQL = "SELECT column_name, referenced_table_name, referenced_column_name FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA = '" . $database . "' and REFERENCED_TABLE_SCHEMA IS NOT NULL and table_name = '" . $table . "'";
    $foreignResult = $dbConn->query($foreignSQL);
    $dbConn->close();
    
    //Put the results into an array and return as JSON
    $result = array();
    $result['error'] = $val;
    $i = 0;
    while( $row = $sqlResult->fetch_array()) {      
        $result['field'][$i] = $row[0];    //0 corresponds to the field name
        $result['type'][$i] = $row[1];     //1 corresponds to the data type
        $result['null'][$i] = $row[2];     //2 corresponds to if null is allowed
        $result['key'][$i] = $row[3];      //3 corresponds to if it is part of a key
        $result['default'][$i] = $row[4];  //4 corresponds to the default value if there is one
        $result['extra'][$i] = $row[5];    //5 corresponds to any extra information if there is any
        $i++;
    }
    
    //Parse the primary key into the result
    $i = 0;
    while($row = $indexResult->fetch_array()) {
        $result['primary'][$i] = $row[4];
        $i++;
    }
    
    //Parse the foreign keys into the result
    $j = 0;
    while($row = $foreignResult->fetch_array()) {
        $result['keyColumn'][$j] = $row[0]; //0 corresponds to the column_name
        $result['referencedtable'][$j] = $row[1]; //1 corresponds to the referenced_table_name
        $result['referencedColumn'][$j] = $row[2]; //2 corresponds to the referenced_column_name
        $j++;
    }
    
    return json_encode($result);
}

function selectAll() {
    //Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    if(isset($_POST['db'])) {
        $database = sanitize($_POST['db']);
    }
    if(isset($_POST['table'])) {
        $table = sanitize($_POST['table']);
    }
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password, $database);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }
    
    //Execute the query to get the table data
    $query = "SELECT * FROM " . $table;  
    $sqlResult = $dbConn->query($query);
    $dbConn->close();
    
    //get the column headers for the table
    $headers = json_decode(getSchema());
    
    //Put the results into an array and return as JSON
    $result = array();
    $result['error'] = $val;
    $i = 0;
    
    //put the table headers into the result
    $result['columns'] = array();
    foreach ( $headers->field as $field) {
        array_push($result['columns'],$field);
    }
    
    //loop through each result in the table
    while($row = $sqlResult->fetch_assoc()) {
        //loop through each field in each row
        foreach ( $headers->field as $field) {
            $result[$field][$i] = $row[$field];
        }
        $i++;
    }
    
    return json_encode($result);
    
}

function connect() {
    $dbConn = new mysqli(getServer(), getUsername(), getPassword());
    
    return $dbConn;
}

function selectRows() {
    //Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    if(isset($_POST['db'])) {
        $database = sanitize($_POST['db']);
    }
    if(isset($_POST['selectData'])) {
       $table = $_POST['selectData']['table'];
       $columns = $_POST['selectData']['columns'];
       $where = $_POST['selectData']['where'];
    };
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password, $database);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }
    
    $query = "SELECT ";
    $queryCol = implode(", ", $columns);
    $query .= $queryCol . " FROM " . $table . " WHERE " . $where . ";";
    
    $queryResult = $dbConn->query($query);
    $dbConn->close();
    
    $result = array();
    $result['error'] = $val;
    $result['SQL'] = $query;
    $i = 0;
    
    //loop through each result in the table
    while($row = $queryResult->fetch_assoc()) {
        //loop through each field in each row
        foreach ( $columns as $field) {
            $result[$field][$i] = $row[$field];
        }
        $i++;
    }
    
    $result['columns'] = $columns;
    
    return json_encode($result);
}

function insert() {
    //Check to see if the proper variables were POSTed
    if(isset($_POST['server'])) {
        $server = sanitize($_POST['server']);
    }
    if(isset($_POST['user'])) {
        $username = sanitize($_POST['user']);
    }
    if(isset($_POST['pass'])) {
        $password = sanitize($_POST['pass']);
    }
    if(isset($_POST['db'])) {
        $database = sanitize($_POST['db']);
    }
    if(isset($_POST['tableData'])) {
       $table = $_POST['tableData']['table'];
       $columns = $_POST['tableData']['columns'];
    }
    
    //Create a new connection to the server
    $dbConn = new mysqli($server, $username, $password, $database);
    if($dbConn->connect_error) {
            $val = $dbConn->connect_error;
    }
    else {
            $val = 0;
    }
    
    
    $query = "INSERT INTO " . $table . " (";
    $queryCol = implode(", ",$columns);
    $query .= $queryCol . ") VALUES ('";
    $queryData = implode("', '", $_POST['data']);
    $query .= $queryData . "');";
    
    $sqlResult = $dbConn->query($query);
    $dbConn->close();
    
    //Put the results into an array and return as JSON
    $result = array();
    $result['error'] = $val;
    
    $result['SQL'] = $query;
    return json_encode($result);
}

//SELECT * FROM `KEY_COLUMN_USAGE` WHERE CONSTRAINT_SCHEMA = 'nascardb' and REFERENCED_TABLE_SCHEMA IS NOT NULL;
//        table_name
//        column_name
//        referenced_table_name
//        referenced_column_name
//
//SHOW INDEX FROM `table`
//       key_name = 'PRIMARY'