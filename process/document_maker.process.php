<?php 
include_once("../classes/document_maker.class.php");
include_once("../classes/include/dbop.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_documentMaker = new doc_templates($dbConn, $dbConnRoot);
//echo "in document maker";
//var_dump($_REQUEST);
//var_dump($_POST);
if($_REQUEST['tempType'] == $_SESSION['RENOVATION_DOC_ID'])
{
	//echo "in if";
	$result = $obj_documentMaker->addRenovationDetails2($_SESSION['society_id'],$_SESSION['serviceRequestDetails']['unit_no'],$_REQUEST['startDate'],$_REQUEST['endDate'],$_REQUEST['renovation_text'],$_REQUEST['workType'],$_REQUEST['location'],$_REQUEST['contractorName'],$_REQUEST['contractorContact'],$_REQUEST['contractorAddress'],$_REQUEST['MaxNoOfLabourer'],$_REQUEST['labourerName'],$_SESSION['serviceRequestDetails']['srTitle'],$_SESSION['serviceRequestDetails']['priority'],$_SESSION['RENOVATION_DOC_ID'],$_SESSION['login_id'],$_FILES,$_POST['sizeOfDoc']);
}
else if($_REQUEST['tempType'] == $_SESSION['ADDRESS_PROOF_ID'])
{
	//public function addAddressProofDetails($srTitle,$srPriority,$srCategory,$loginId,$purpose,$unitId,$memberName,$stayingSince,$note,$societyId)
	
	$result = $obj_documentMaker->addAddressProofDetails($_SESSION['serviceRequestDetails']['srTitle'],$_SESSION['serviceRequestDetails']['priority'],$_SESSION['ADDRESS_PROOF_ID'],$_SESSION['login_id'],$_REQUEST['purpose'],$_SESSION['serviceRequestDetails']['unit_no'],$_REQUEST['memberName'],$_REQUEST['stayingSince'],$_REQUEST['addressProof_note'],$_SESSION['society_id']);
}
else
{
}
//echo $obj_documentMaker->actionPage;
//echo $result;
?>

<html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_documentMaker->actionPage; ?>">
	<?php

	if($validator=="Insert")
	{
		$ShowData = "Record Added Successfully";
	}
	else if($validator=="Update")
	{
		$ShowData = "Record Updated Successfully";
	}
	else if($validator=="Delete")
	{
		$ShowData = "Record Deleted Successfully";
	}
	else
	{
		foreach($_POST as $key=>$value)
		{
			echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
		}
		$ShowData = $validator;
	}
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
	//window.location.href = "<?php //echo $obj_documentMaker->actionPage;?>";
</script>

</body>
</html>
