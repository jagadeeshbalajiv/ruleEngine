<?php 
$databaseHost = "localhost";
$username = "root";
$password = "";
$databaseName = "product_rule_engine";

// Create connection
$databaseConnectionString = new mysqli($databaseHost, $username, $password, $databaseName);

// Check connection
if ($databaseConnectionString->connect_error) {
    die("Connection failed: " . $databaseConnectionString->connect_error);
} 
session_start();
$_SESSION['databaseConnectionString'] = $databaseConnectionString;

function selectResult($query){
	return executeQuery($query);
}

function insertRecords($query){
	$connection = $_SESSION['databaseConnectionString'];
	executeQuery($query);
	return $connection->insert_id;
}

function executeQuery($query){
	$connection = $_SESSION['databaseConnectionString'];
	$result = $connection->query($query);
	return $result;
}

?>