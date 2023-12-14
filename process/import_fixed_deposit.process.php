<?php	
	session_start();

include_once("../classes/import_fixed_deposit.class.php"); 
include_once("../classes/include/dbop.class.php");
require_once ("../classes/CsvOperations.class.php");

		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$csv = new CsvOperations();
		$checkBoxIndexes = array();

		$fileData = $_SESSION['file_data'];
		// var_dump($_POST);
		
		if (isset($_POST["submit"]) || isset($_POST["validate"]))
		{
			$tempFileName = $_POST["tmpName"];
			$name = $_POST["name"];
			$error = $_POST["error"];
			if(isset($_POST["submit"]))
			{
				$bvalidate = false;
			}else
			{
				$bvalidate = true;
			}
			
			// var_dump($bvalidate);

			if ($error == 0)
			{
				$data = $_POST["data"];

				$checkBoxIndexes = explode(',', $data);
				//DB Connection	
				
				
				$obj_member_import=new fd_import($dbConnRoot, $dbConn);
				//Uploading Data
				// print_r($fileData[3]);
				// die();
				$validator = $obj_member_import->UploadData($fileName, $fileData, $bvalidate);
				// print_r($validate);
				// die();

				$actionPage = $obj_member_import->actionPage;
				$ErrorLog = $obj_member_import->errorLog;
				
			}
			else
			{   //Error Related To File
				switch ($error)
	            {
	                case 1:
	                       echo '<p> The file is bigger than this PHP installation allows</p>';
	                       // $result = '<p> The file is bigger than this PHP installation allows</p>';
	                       break;
	                case 2:
	                       echo '<p> The file is bigger than this form allows</p>';
	                       // $result = '<p> The file is bigger than this form allows</p>';
	                       break;
	                case 3:
	                       echo '<p> Only part of the file was uploaded</p>';
	                       // $result = '<p> Only part of the file was uploaded</p>';
	                       break;
	                case 4:
	                       echo '<p> No file was uploaded</p>';
	                       // $result = '<p> No file was uploaded</p>';
	                   break;
	            }
			}
		}
		// echo $validator;
?>


<html>
<body>
<form id="Goback" method="post" action="<?php echo $actionPage; ?>">
<?php 
foreach($_POST as $key=>$value)
{
	"<input type=\"text\" name=\"$key\" value=\"$value\" />";
}
$ShowData = $validator;
?>
<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
<input type="hidden" name="Isvalidate" value="1">

</form>
<script>
	window.open("<?php echo $ErrorLog; ?>");
	<?php if($bvalidate !== true)
	{ ?>
		document.getElementById("Goback").submit();
	<?php } ?>
	
</script>
</body>
</html>
<?php echo '@@@'.$ErrorLog; ?>

