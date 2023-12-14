<?php
	include_once("classes/include/dbop.class.php");
	include_once ("dbconst.class.php");
	include_once("classes/api_utility.class.php");
    include_once("classes/document_maker.class.php");
	include_once("classes/servicerequest.class.php");
	
	if (isset($_SERVER['HTTP_ORIGIN']))
	{
       	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
	//var_dump($_SESSION);
	//$m_dbConnRoot = new dbop(true);
	//var_dump($m_dbConnRoot);
    //http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
    $postdata = file_get_contents("php://input");
	//var_dump($postdata);
  	if (isset($postdata)) 
	{
		//echo "in if";
        $request = json_decode($postdata);
		//var_dump($request);
        $method = $request->method;
		$societyId = $request->societyId;
		//echo "SocietyID :".$societyId;
		$unitId = $request->unitId;
		$role = $request->role;
		$obj_api_utility = new api_utility($societyId,$unitId,$role);
		//var_dump($obj_api_utility);
		$conObj = $obj_api_utility->getDBConnectionObject();
		$dbConn = $conObj[0];
		$dbConnRoot = $conObj[1];
		//var_dump($conObj);
		$obj_templates = new doc_templates($dbConn,$dbConnRoot);
		$obj_serviceRequest = new servicerequest($dbConn);
		//var_dump($obj_templates);
		$response = array();
		//echo $method;
		if($method == "getWorkTypeList")
		{
			$workTypeList = $obj_templates->getListOfWork();
			if(sizeof($workTypeList) > 0)
			{
				$response['success'] = "1";
				$response['response']['WorkList'] = $workTypeList;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['WorkList'] = "error";
			}
			//var_dump($response);
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
			//var_dump($workTypeList);	
		}
		else if($method == "submitRenovationDetails")
		{
			//echo "in if 2";
			$unitId = $request->unitId;
			$startDate = $request->startDate;
			$endDate = $request->endDate;
			$workDetails = $request->workDetails;
			$workType = $request->workType;
			$location = $request->location;
			$contractorName = $request->contractorName;
			$contractorContact = $request->contractorContact;
			$contractorAddress = $request->contractorAddress;
			$maxLabourer = $request->maxLabourer;
			$labourerName = $request->labourerName;
			$srTitle = $request->srTitle;
			$srPriority = $request->srPriority;
			$loginId = $request->loginId;
			$srCategory = $request->srCategory;
			$drawingFiles = array();
			$sizeOfDoc = 0;
			$result = $obj_templates->addRenovationDetails2($societyId,$unitId,$startDate,$endDate,$workDetails,$workType,$location,$contractorName,$contractorContact,$contractorAddress,$maxLabourer,$labourerName,$srTitle,$srPriority,$srCategory,$loginId,$drawingFiles,$sizeOfDoc);
			$response = array();
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['renovationId'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['renovationId'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "getRenovationNOC")
		{
			$renovationId = $request->renovationId;
			$result = $obj_templates->getFinalTemplate($renovationId);
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['template'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['template'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "getMemberDetails")
		{
			$unitId = $request->unitId;
			$result = $obj_templates->getpassDetail2($unitId);
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['member_details'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['member_details'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "getRequestId")
		{
			$result = $obj_serviceRequest->getRenovationId();
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['details'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['details'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if ($method == "getMemberAddress")
		{
			$result = $obj_serviceRequest->getMemberAddress($unitId,$societyId);
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['memberAddress'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['memberAddress'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "submitAddressProofDetails")
		{
			$srTitle = $request->srTitle;
			$srPriority = $request->srPriority;
			$loginId = $request->loginId;
			$srCategory = $request->srCategory;
			$purpose = $request->purpose;
			$stayingSince = $request->stayingSince;
			$memberName = $request->memberName;
			$note = $request->note;
			//public function addAddressProofDetails($srTitle,$srPriority,$srCategory,$loginId,$purpose,$unitId,$memberName,$stayingSince,$note,$societyId)
	
			$result = $obj_templates->addAddressProofDetails($srTitle,$srPriority,$srCategory,$loginId,$purpose,$unitId,$memberName,$stayingSince,$note,$societyId);
			//echo $result;
			if($result != "")
			{
				$response['success'] = "1";
				$response['response']['serviceRequestId'] = $result;
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
				$response['response']['serviceRequestId'] = "error";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "uploadImage")
		{
			$renovationId = $request->renovationId;
			$files = $request->drawingFile;
			$unitId = $request->unitId;
			$result = $obj_templates->uploadFile($renovationId,$files,$unitId);
			//echo $result;
			if($result > 0)
			{
				$response['success'] = "1";
				//echo $workTypeList;
			}
			else
			{
				$response['success'] = "0";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "getRenovationDetails")
		{
			$type = $request->type;
			$loginId = $request->loginId;
			//echo "Type : ".$type;
			$result = $obj_api_utility->getRenovationRequest($type,$loginId,$societyId);
			$response['success'] = "1";
			$response['response']['RenovationRequest'] = $result;
			$response['response']['length'] = sizeof($result);
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "approveRequest")
		{
			$requestType = $request->requestType;
			$loginId = $request->loginId;
			$action = $request->action;
			$requestId = $request->requestId;
			$serviceRequestId = $request->serviceRequestId;
			$obj_serviceRequest->getRenovationId();
			//var_dump("In api.php : ",$request);
			$result = $obj_api_utility->approveRequest($requestType,$requestId,$action,$loginId,$serviceRequestId);
			echo "result : ".$result;
			if($result > 0)
			{
				$response['success'] = "1";
			}
			else
			{
				$response['success'] = "0";
			}
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
		else if($method == "getAddressProofRequest")
		{
			//getAddressProofRequests	
			$type = $request->type;
			$loginId = $request->loginId;
			//echo "Type : ".$type;
			$result = $obj_api_utility->getAddressProofRequests($type,$loginId,$societyId);
			$response['success'] = "1";
			$response['response']['AddressProofRequest'] = $result;
			$response['response']['length'] = sizeof($result);
			$jsonResponse = json_encode($response);
			echo $jsonResponse;
		}
    }
    else {
        echo "Not called properly with username parameter!";
    }
?>