<?php
	session_start(); 
	include_once ("../classes/import_daybook.class.php");
	include_once ("../classes/include/dbop.class.php");

	$dbConn = new dbop();
	$actionPage = "../import_daybook.php";
	$dbConnRoot = new dbop(true);
	
	$demo=$_SESSION['DayBook'];
	$xmlData = json_decode($demo, true);

	$obj_dayBook = new dayBook($dbConn);
	$obj_dayBook->dayBookProcess($xmlData);
	$obj_dayBook->errorfile_name;


?>
<html>
	<body>
        <form id="Goback" method="post" action="<?php echo $actionPage ?>">
	        <input type="hidden" name="mm" value="no value">
        </form>
        <script>
			window.open("<?php echo $obj_dayBook->errorfile_name ?>");
			document.getElementById("Goback").submit();
 		</script>
	 </body>
</html>