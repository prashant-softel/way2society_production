
<?php include_once("../classes/chequeleafbook.class.php");
include_once("../classes/include/dbop.class.php");
$m_dbConn = new dbop();
$obj_chequeleafbook = new chequeleafbook($m_dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_chequeleafbook->selecting();

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
	echo $obj_chequeleafbook->deleting();
}

?>