<?php 
include_once("../classes/lien.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/doc.class.php");
$dbConn = new dbop(); 
$obj_lien = new lien($dbConn);
$obj_doc=new document($dbConn);
if(isset($_REQUEST['method']))	
{
	echo $_REQUEST["method"]."@@@";
	if($_REQUEST["method"]=="editLienDetails")
	{
		$select_type = $obj_lien->getLienByLienId($_REQUEST['lienId']);
		foreach($select_type as $k => $v)
		{
			echo json_encode($v);
		}
	}
	if($_REQUEST["method"]=="editDocumentDetails")
	{
		//$select_type = $obj_lien->getDocumentsByUnitId($_REQUEST['unit_id'], DOC_TYPE_LIEN_ID);
		$select_type = $obj_lien->getDocumentsByLienID($_REQUEST['lien_ID'], DOC_TYPE_LIEN_ID);
		foreach($select_type as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
	if($_REQUEST["method"]=="deleteLien")
	{
		$lienId=$_REQUEST['lienId'];
		$res=$obj_lien->deleteLien($lienId);
		echo $res;
		//return "Data Deleted Successfully";
	}
}
?>