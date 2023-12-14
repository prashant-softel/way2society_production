<?php
	session_start(); 
	include_once ("../classes/include/dbop.class.php");
	include_once ("../classes/import_daybook.class.php");
	
	$dbConn = new dbop();
	$actionPage = "../import_master_daybook.php";

	$select =$_POST['select'];
	
	
	$obj_dayBook = new dayBook($dbConn);
	
	$category=$_SESSION['Category'];
	$dayBookCategoryData = json_decode($category, true);
	if($select=='category')
	{
		
		//var_dump($dayBookCategoryData);
		$obj_dayBook->importDayBookCategoryData($dayBookCategoryData);
	}
	
	$ledger=$_SESSION['Ledger'];
	$dayBookLedgerData = json_decode($ledger, true);
	//var_dump($dayBookLedgerData);
	if($select=='ledger')
	{
		$obj_dayBook->importDayBookLedgerData($dayBookLedgerData);
	}
	$ErrorLog = $obj_dayBook->errorfile_name;

?>
 <html>
<body>
<form id="Goback" method="post" action="<?php echo $actionPage ?>">
<input type="hidden" name="mm" value="no value">
 </form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.getElementById("Goback").submit();
 </script>
 </body>
 </html>