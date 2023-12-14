<?php	include_once "../classes/import_society.class.php" ;
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$validator = "";
		$result="";
		
		$filename =$_POST['name'];
        $tmpName = $_POST['tmpName'];
		$validate=$_POST['Validate'];
	
		$checkBoxIndexes = array();
		$fileData=$_SESSION['file_data'];
		
		try
		{
			
			$tempFileName = $_POST["tmpName"];
			$name = $_POST["name"];
			$error = $_POST["error"];
			$data = $_POST["data"];
			$checkBoxIndexes = explode(',', $data);
			$obj_import=new import($dbConnRoot,$dbConn);
			$dbConnRoot->begin_transaction();
			$dbConn->begin_transaction();
			
			
			
			if($validate=='')
			{
				echo "<br>Validate Value Should be Null : ".$validate;
				$validator=$obj_import->ImportData($fileData);
				$result=$obj_import->UploadData($tmpName,$checkBoxIndexes,$fileData,$validate);
			}
			else
			{
				echo "<br>Validate Value : ".$validate;
				$result=$obj_import->UploadData($tmpName,$checkBoxIndexes,$fileData,$validate);
			}
			
			$ErrorLog=$obj_import->ErrorLogFile;
			$dbConnRoot->commit();
			$dbConn->commit();
		}
		
		catch(Exception $exp)
		{
			$dbConnRoot->rollback();
			$dbConn->rollback();
			
			$validator = $exp;
			$result=$exp;
		}
		
?>


<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_import->actionPage; ?>">

	<?php
	
	//echo  "ErrorLog File Name:".$ErrorLog;
	$ShowData = "";
	$ShowData1 = ""; 
	if($validator=="Exist")
	{
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$validator="Society Already Exist...";
		$ShowData=$validator;
		
		
	}
	else
	{
		$ShowData=$validator;
		//$ShowData1=$result;
		//echo $ShowData;	
	}

	if($result=="Exist")
	{
		foreach($fileData as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}	
	}
	else
	{
		$ShowData1=$result;	
	}
	?>
<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="ShowData1" value="<?php echo $ShowData1; ?>">
<input type="hidden" name="mm">
<input type="hidden" name="Import" value="Import">
</form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.Goback.submit();
</script>
</body>
</html>

