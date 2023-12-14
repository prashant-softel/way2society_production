<?php 
	include_once("../classes/dbconst.class.php");
	include_once("../classes/include/dbop.class.php");;
	$dbConn = new dbop();
	if($_REQUEST['method'] == "getOwnerDetails")
	{
		$unitId=$_REQUEST['unitId'];
		$ownerDetails = file_get_contents(HOST_NAME."8080/".WAR_FILE_NAME."/OwnerServlet?mode=1&unitId=".$unitId."&societyId=".$_SESSION['society_id']."&token=");
		echo $ownerDetails;
		break;
	}
	if($_REQUEST['method'] == "getTenantDetails")
	{
		$unitId=$_REQUEST['unitId'];
		$tenantId = $_REQUEST['tenantId'];
		$tenantDetails = file_get_contents(HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=1&unitId=".$unitId."&tenantId=".$tenantId."&societyId=".$_SESSION['society_id']."&token=");
		echo $tenantDetails;
		break;
	}
	if($_REQUEST['method'] == "getAgreementdetails")
	{
		$tenantModuleId=$_REQUEST['tenantModuleId'];
		$agreementDetails = file_get_contents(HOST_NAME."8080/".WAR_FILE_NAME."/AgreementTermServlet?mode=2&tenantModuleId=".$tenantModuleId."&societyId=".$_SESSION['society_id']."&token=");
		echo $agreementDetails;
		break;
	}
	if($_REQUEST['method'] == "updateOwnerDetails")
	{
		$memberId=$_REQUEST['memberId'];
		$gender=$_REQUEST['gender'];
		$dob=$_REQUEST['dob'];
		$updateResponse = file_get_contents(HOST_NAME."8080/".WAR_FILE_NAME."/OwnerServlet?mode=2&societyId=".$_SESSION['society_id']."&memberId=".$memberId."&gender=".$gender."&dob=".$dob."&token=");
		echo $updateResponse;
		break;
	}
	if($_REQUEST['method'] == "addNewTenant")
	{
		$unitId = $_REQUEST['unitId'];
		$fName = $_REQUEST['fName'];
		$mName = $_REQUEST['mName'];
		$lName = $_REQUEST['lName'];
		$mContactNo = $_REQUEST['mContactNo'];
		$mEmailId = $_REQUEST['mEmaild'];
		$memCount = $_REQUEST['memCount'];
		$profession =  $_REQUEST['profession'];
		$dob =  $_REQUEST['dob'];
		$add1 = $_REQUEST['add1'];
		$add2 =  $_REQUEST['add2'];
		$city = $_REQUEST['city'];
		$pincode =  $_REQUEST['pincode'];
		$cardNo = $_REQUEST['cardNo'];
		$url = HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=2&unitId=".$unitId."&fName=".$fName."&mName=".$mName."&lName=".$lName."&memCount=".$memCount."&profession=".$profession."&dob=".getDBFormatDate($dob)."&add1=".$add1."&add2=".$add2."&city=".$city."&pincode=".$pincode."&cardNo=".$cardNo."&email=".$mEmailId."&mobileNo=".$mContactNo."&societyId=".$_SESSION['society_id']."&token=";
		$insertTenantModuleRes = file_get_contents($url);
		echo $insertTenantModuleRes;
		break;
	}
	if($_REQUEST['method'] == "addNewTenantMembers")
	{
		//echo "in addNewTenantMembers";
		$tenantModuleId = $_REQUEST['tenantModuleId'];
		$memCount = $_REQUEST['memCount'];
		if($memCount > 1)
		{
			$memberName=json_decode($_REQUEST['memberName']);
			$relation=json_decode($_REQUEST['relation']);
			$dob=json_decode($_REQUEST['dob']);
			$contactNo=json_decode($_REQUEST['contactNo']);
			$emailAdd=json_decode($_REQUEST['emailAdd']);
		}
		for($i=1;$i<$memCount;$i++)
		{
			//echo $memberName[$i];
			$memName = $memberName[$i];
			$rel = $relation[$i];
			$memDob = getDBFormatDate($dob[$i]);
			$email = $emailAdd[$i];
			$mobile = $contactNo[$i];
			$url = HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=3&tenantModuleId=".$tenantModuleId."&memberName=".$memName."&relation=".$rel."&memDob=".$memDob."&email=".$email."&mobileNo=".$mobile."&societyId=".$_SESSION['society_id']."&token=";
			$insertTenantMembers = file_get_contents($url);
		}
		echo "success";
		break;
	}
	if($_REQUEST['method'] == "updateTenantDetails")
	{
		$tenantModuleId = $_REQUEST['tenantModuleId'];
		$pFromDate = $_REQUEST['pFromDate'];
		$pToDate = $_REQUEST['pToDate'];
		$url = HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=4&tenantModuleId=".$tenantModuleId."&pFromDate=".getDBFormatDate($pFromDate)."&pToDate=".getDBFormatDate($pToDate)."&societyId=".$_SESSION['society_id']."&token=";
		$updateTenant = file_get_contents($url);
		echo $updateTenant;
		break;
	}
	if($_REQUEST['method'] == "sendDetailsToDigitalRenting")
	{
		$tenantModuleId = $_REQUEST['tenantModuleId'];
		//echo ("tenantModuleId :".$tenantModuleId);
		$propertyType = $_REQUEST['propertyType'];
		$propertyUse = $_REQUEST['propertyUse'];
		$pAddress1 = $_REQUEST['pAddress1'];
		$pAddress2 = $_REQUEST['pAddress2'];
		$pPincode = $_REQUEST['pPincode'];
		$pcity = $_REQUEST['pcity'];
		$pregion = $_REQUEST['pregion'];
		$propertyArea = $_REQUEST['propertyArea'];
		$rentType = $_REQUEST['rentType'];
		$deposit = $_REQUEST['deposit'];
		//$rentCount = $_REQUEST['j'];
		$monthlyRent =  $_REQUEST['monthlyRent'];
		$var1 = $_REQUEST['var1'];
		$var2 = $_REQUEST['var2'];
		$var3 = $_REQUEST['var3'];
		$rent1 = $_REQUEST['rent1'];
		$rent2 = $_REQUEST['rent2'];
		$rent3 = $_REQUEST['rent3'];
		$url = HOST_NAME."8080/".WAR_FILE_NAME."/AgreementTermServlet?mode=1&tenantModuleId=".$tenantModuleId."&propertyType=".$propertyType."&propertyUse=".$propertyUse."&pAddress1=".$pAddress1."&pAddress2=".$pAddress2."&pPincode=".$pPincode."&pcity=".$pcity."&pregion=".$pregion."&propertyArea=".$propertyArea."&rentType=".$rentType."&deposit=".$deposit."&monthlyRent=".$monthlyRent."&var1=".$var1."&var2=".$var2."&var3=".$var3."&rent1=".$rent1."&rent2=".$rent2."&rent3=".$rent3."&societyId=".$_SESSION['society_id']."&token=";
		$updateTenant = file_get_contents($url);
		echo $updateTenant;
		break;
	}
	if($_REQUEST['method'] == "getTenantMemberDetails")
	{
		$tenantId = $_REQUEST['tenantId'];
		$tenantDetails = file_get_contents(HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=5&tenantId=".$tenantId."&societyId=".$_SESSION['society_id']."&token=");
		echo $tenantDetails;
		break;
	}
	if($_REQUEST['method'] == "editTenant")
	{
		$tenantModuleId = $_REQUEST['tenantModuleId'];
		//echo "tenantModuleId : ".$tenantModuleId;
		$unitId = $_REQUEST['unitId'];
		$fName = $_REQUEST['fName'];
		$mName = $_REQUEST['mName'];
		$lName = $_REQUEST['lName'];
		$mContactNo = $_REQUEST['mContactNo'];
		$mEmailId = $_REQUEST['mEmaild'];
		$memCount = $_REQUEST['memCount'];
		$profession =  $_REQUEST['profession'];
		$dob =  $_REQUEST['dob'];
		$add1 = $_REQUEST['add1'];
		$add2 =  $_REQUEST['add2'];
		$city = $_REQUEST['city'];
		$pincode =  $_REQUEST['pincode'];
		$cardNo = $_REQUEST['cardNo'];
		$url = HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=6&tenantModuleId=".$tenantModuleId	."&unitId=".$unitId."&fName=".$fName."&mName=".$mName."&lName=".$lName."&memCount=".$memCount."&profession=".$profession."&dob=".getDBFormatDate($dob)."&add1=".$add1."&add2=".$add2."&city=".$city."&pincode=".$pincode."&cardNo=".$cardNo."&email=".$mEmailId."&mobileNo=".$mContactNo."&societyId=".$_SESSION['society_id']."&token=";
		$insertTenantModuleRes = file_get_contents($url);
		echo $insertTenantModuleRes;
		break;
	}
	if($_REQUEST['method'] == "editTenantMembers")
	{
		//echo "in addNewTenantMembers";
		$tenantModuleId = $_REQUEST['tenantModuleId'];
		$memCount = $_REQUEST['memCount'];
		if($memCount > 1)
		{
			$memberId = json_decode($_REQUEST['tenantMemId']);
			$memberName=json_decode($_REQUEST['memberName']);
			$relation=json_decode($_REQUEST['relation']);
			$dob=json_decode($_REQUEST['dob']);
			$contactNo=json_decode($_REQUEST['contactNo']);
			$emailAdd=json_decode($_REQUEST['emailAdd']);
		}
		for($i=1;$i<$memCount;$i++)
		{
			//echo $memberName[$i];
			$memId = $memberId[$i];
			$memName = $memberName[$i];
			$rel = $relation[$i];
			$memDob = getDBFormatDate($dob[$i]);
			$email = $emailAdd[$i];
			$mobile = $contactNo[$i];
			$url = HOST_NAME."8080/".WAR_FILE_NAME."/TenantServlet?mode=7&tenantMemberId=".$memId."&memberName=".$memName."&relation=".$rel."&memDob=".$memDob."&email=".$email."&mobileNo=".$mobile."&societyId=".$_SESSION['society_id']."&token=";
			$insertTenantMembers = file_get_contents($url);
		}
		echo "success";
		break;
	}
?>