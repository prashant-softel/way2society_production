<?php	
echo "1"; 
include_once("../classes/import_reverse_charges.class.php"); 
echo "2";
include_once("../classes/include/dbop.class.php");
echo "3";
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
echo "4";
		$obj_rcImport = new import_reverse_charges($dbConnRoot,$dbConn);
echo "5";
		$validator = $obj_rcImport->CSV_RC_Import();
echo "6";
		$actionPage = $obj_rcImport->actionPage;
		$ErrorLog = $obj_rcImport->errorLog;
		echo $validator;
		
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $actionPage ?>">
<?php 
foreach($_POST as $key => $value)
{
	echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
}
$ShowData = $validator;
?>
<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">

</form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.Goback.submit();
</script>
</body>
</html>