<?php
	session_start();
	$sid=$_GET['sessionid'];
	session_id($sid);
	$sid = session_id();
	var_dump($sid);
	session_start();
	var_dump('------',$sid);

	session_id($sid);
	session_start();
	$sid = session_id();
	var_dump('------',$sid);