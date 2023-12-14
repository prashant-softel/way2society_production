<?php
	session_start();
	include_once("../classes/import_creditnote.class.php"); 
	include_once ("../classes/include/dbop.class.php");
	require_once ("../classes/CsvOperations.class.php");
	
	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);
	$validator = "";
	$csv = new CsvOperations();

	if (isset($_POST["submit"]))
	{
		$tempFileName = $_POST["tmpName"];
		$name = $_POST["name"];
		$error = $_POST["error"];

		if ($error == 0)
		{		
			$data = $_POST["data"];
			$checkBoxIndexes = array();
			$fileData = $_SESSION['file_data'];
			$NoteType = $_POST['NoteType'];
			$checkBoxIndexes = explode(',', $data);
			
			$obj_dImport = new creditnote_import($dbConnRoot,$dbConn);
			$validator = $obj_dImport->UploadData($fileName, $fileData, $NoteType);
			
			$actionPage = $obj_dImport->actionPage;
			$ErrorLog = $obj_dImport->errorLog;
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