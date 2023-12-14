<?php
if(!isset($_SESSION)){ session_start(); }
include_once("../classes/sendMsg.class.php");
include_once("../classes/dbconst.class.php");
$obj_notify = new notification($m_dbConn,$m_dbConnRoot);

if(isset($_REQUEST['smsSet'])=='smsSet')
{
	$id=$_REQUEST['Id'];
   echo $sendMsg=$obj_notify->sendMsg_id($id);
  
}

?>