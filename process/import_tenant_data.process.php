<?php	
	session_start();

include_once("../classes/import_tenant_data.class.php"); 
include_once("../classes/include/dbop.class.php");
require_once ("../classes/CsvOperations.class.php");

		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$ErrorLog='';
		$actionPage="";
		$csv = new CsvOperations();
		$checkBoxIndexes = array();

		$fileData = $_SESSION['file_data'];
		//var_dump($_POST);
		
		if (isset($_POST["submit"]))
		{
			$tempFileName = $_POST["tmpName"];
			//$name = $_POST["name"];
			$error = $_POST["error"];

			if(isset($_POST["submit"]))
			{
				$bvalidate = false;
			}else
			{
				$bvalidate = true;
			}
			
		 //var_dump($bvalidate);

			if ($error == 0)
			{
				$data = $_POST["data"];

				$checkBoxIndexes = explode(',', $data);
			
				$obj_member_import = new import_tenantdata($dbConnRoot, $dbConn);
			
				$validator = $obj_member_import->UploadData($fileName, $fileData, $bvalidate);
				

				$actionPage = $obj_member_import->actionPage;
				$ErrorLog = $obj_member_import->errorLog;

			
				echo $validator;	
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
<form id="Goback" method="post" action="<?php echo $actionPage ?>">
<input type="hidden" name="mm" value="no value">
 </form>
<script>
	window.open("<?php echo $ErrorLog ?>");
	document.getElementById("Goback").submit();
 </script>
 </body>
 </html>

