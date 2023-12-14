<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/servicerequest.class.php");
$m_dbConn = new dbop();
$obj_servicerequest = new servicerequest($m_dbConn);

if($_REQUEST["method"]=="edit")
{
	echo $_REQUEST["method"]."@@@";

	$select_type = $obj_servicerequest->selecting($_REQUEST['requestId']);
	
	echo json_encode($select_type);  // Returning result as json object
	
	/*foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."^";
		}
	}*/
}

if($_REQUEST["method"]=="delete")
{
	echo $_REQUEST["method"]."@@@";
	$obj_servicerequest->deleting($_REQUEST['requestId']);
	return "Data Deleted Successfully";
}

if($_REQUEST["method"]=="del_photo")
{
	echo $_REQUEST["method"]."@@@";
	$baseDir = dirname( dirname(__FILE__) );
	//echo $baseDir;
	//print_r($_REQUEST);
	$sr_id=$_REQUEST['qr'];
	$img=$_REQUEST['img'];
	  $sql2 = "select `img` FROM `service_request` WHERE request_id='$sr_id'";

	$res2 = $m_dbConn->select($sql2);
	 $image=$res2[0]['img'];
			$image_collection = explode(',', $image);
			//echo $baseDir.'/ads/'.$image_collection[0];
			for($i=0;$i<sizeof($image_collection);$i++)
			{
				if($image_collection[$i]==$img)
				{
					 unset($image_collection[$i]); 
					 break;
				}
			}
	
	$image_coll = implode(',', $image_collection);
	
     $sql3="update `service_request` set `img`= '$image_coll' where `request_id`='$sr_id'";
	$res3 = $m_dbConn->update($sql3);
	//echo $baseDir.'/ads/'.$image_collection[0];
	//if (file_exists($baseDir.'/ads/'.$image)) 
	//echo $baseDir.'\ads\\'.$img;
	if (file_exists($baseDir.'\upload\main\\'.$img)) 
	{
		
		unlink($baseDir.'\upload\main\\'.$img);

		//unlink($baseDir.'/ads/'.$image);
		echo "file deleted";
	}
	else
	{
		echo "not deleted file";
	}
}
if($_REQUEST['method'] == "checkTenantStatus")
{
	$unitId = $_REQUEST['unitId'];
	$result = $obj_servicerequest->checkTenantStatus($unitId);
	echo $result;
}
if($_REQUEST['method']=='getDetails')
{
	echo $Id=$_REQUEST['Id'];
	$res=$obj_servicerequest->getnewdetails($Id);
	foreach($res as $k=>$v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}
?>
