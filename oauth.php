<?php
	require_once 'config.php';
	session_start();
	
	$auth_url = LOGIN_URI . "/services/oauth2/authorize?response_type=code&client_id=" . CLIENT_ID . "&redirect_uri=" . urlencode(REDIRECT_URI);
	
	$_SESSION['name']= htmlspecialchars($_POST['name']);
	$_SESSION['ticketnum']= htmlspecialchars($_POST['ticketnum']);
	$_SESSION['phone']=htmlspecialchars($_POST['phone']);

	header('Location: ' . $auth_url);
?>
