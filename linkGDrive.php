<?php 
//phpinfo();
error_reporting(7);
// echo "loaded functions1";
require_once("functions.php");
//echo "loaded functions";
header('Content-Type: text/html; charset=utf-8');

$authUrl = getAuthorizationUrl("", "");
//echo "auth url:".$authUrl;
//die();
header("Location: ".$authUrl);
