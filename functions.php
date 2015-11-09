<?php

	require_once("./User.class.php");
	require_once("../config_global.php");
    $database = "if15_skmw";
	
	session_start();
	
	$mysqli = new mysqli($servername, $server_username, $server_password, $database);

	$User = new User($mysqli); //siit läheb user.class.php $mysqli'sse
	
	
	

?>