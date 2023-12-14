<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/include/check_session.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);

echo $_REQUEST["method"] . "@@@";

if ($_REQUEST['method'] == 'Get_client') {

	$query = "SELECT distinct id,client_name  from client where id='" . $_REQUEST['Id'] . "'";
	$result = $dbConnRoot->SELECT($query);
	$Table = '';

	if ($result[0]['id'] == 0) {
		$sql = "SELECT db.dbname, db.locked, soc.society_id, soc.society_code, soc.society_name, soc.status, soc.timestamp, clnt.client_name,soc.Last_use_society_timestamp FROM dbname as db LEFT JOIN society as soc ON db.dbname = soc.dbname LEFT JOIN client as clnt ON soc.client_id = clnt.id";
		$res = $dbConnRoot->select($sql);
		$Table = $res;
	} else {
		$sql = "SELECT db.dbname, db.locked, soc.society_id, soc.society_code, soc.society_name, soc.status, soc.timestamp, clnt.client_name,soc.Last_use_society_timestamp FROM dbname as db LEFT JOIN society as soc ON db.dbname = soc.dbname LEFT JOIN client as clnt ON soc.client_id = clnt.id where  clnt.id='" . $result[0]['id'] . "' ORDER BY  soc.society_id ASC ";
		$res = $dbConnRoot->select($sql);
		$Table = $res;
	}
	echo json_encode($Table);
}
