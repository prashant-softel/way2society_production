<?php include_once("../classes/gallery_group.class.php");	
	include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_gallery=new gallery_group($dbConn, $dbConnRoot);
		
		
if(isset($_REQUEST["method"]) && $_REQUEST["method"] == 'fetchsocietylist')	
{	

		//echo 'test';
	$query = "select society_id,society_name from society where  `client_id` = '" . $_SESSION['society_client_id'] . "' AND status='Y' order by society_name ASC";
	$select_type = $obj_gallery->combobox11($query, 'society_id[]', 0, $_REQUEST['group_id']); 
}
else
{

	echo $_REQUEST["method"]."@@@";
	
	if($_REQUEST["method"]=="edit")
	{
		$select_type = $obj_gallery->selecting();
	
		foreach($select_type as $k => $v)
		{
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}
	}
	
	if($_REQUEST["method"]=="delete")
	{
		$obj_gallery->deleting();
		return "Data Deleted Successfully";
	}
}
?>