<?php
if(!isset($_SESSION)){ session_start(); }
include_once ("dbconst.class.php"); 
include_once("include/dbop.class.php");
include_once("latestcount.class.php");

include_once( "include/fetch_data.php");

include_once('../swift/swift_required.php');
include_once("../ImageManipulator.php");
include_once("utility.class.php");
include_once("android.class.php");
include_once("email.class.php");

class servicerequest
{
	//public $actionPage = "../addnotice.php";
	public $m_dbConn;
	public $m_dbConnRoot;	
	public $objFetchData;
	public $m_objUtility;
	
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;		
		$this->m_dbConnRoot = $dbConnRoot;		
		$this->objFetchData = new FetchData($dbConn);
		$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);	
		$this->m_objUtility = new utility($dbConn,$this->m_dbConnRoot);
	}		
	
	public function startProcess()
	{
		$errorExists=0;
	if($_POST['insert']=='Submit' || $_POST['insert'] == "Next" && $errorExists==0)
	{
		date_default_timezone_set('Asia/Kolkata');	
		$image_list=array(); 
		for($i=0; $i<count($_FILES['img']['name']); $i++)
			{
				//print_r($_FILES);
				$file_type=$_FILES['img']['type'][$i];
				$file_size=$_FILES['img']['size'][$i];
				$file_tmp=$_FILES['img']['tmp_name'][$i];
				list($txt, $ext) = explode(".", $file);
				$randon_name = $file.".".$ext;
				$kaboom = explode(".", $_FILES['img']['name'][$i]); // Split file name into an array using the dot
				 $fileExt = end($kaboom);
				 $random_name= rand();
				//echo $random_name;
			
				if($_FILES["img"]['name'][$i]<>'')
				{
				if ($_FILES["img"]["size"][$i] > 10240*1024) 
				{
					 $error="Sorry, your file is too large.";
					 $this->table .= "Sorry, your file is too large.";
				}
				else if (($_FILES["img"]["type"][$i] == "image/gif") || 
						($_FILES["img"]["type"][$i] == "image/jpeg") || 
						($_FILES["img"]["type"][$i]== "image/png") || 
						($_FILES["img"]["type"][$i] == "image/pjpeg")) 
				{
					//echo "2";
			
					if ($_FILES["img"]["type"][$i] == "image/jpeg")
					{ //echo"jpeg type";
						$url =$random_name.'.'.$fileExt;
					}
					else if($_FILES["img"]["type"][$i] == "image/png")
					{//echo"png type";
						$url =$random_name.'.'.$fileExt;
					}
					else if ($_FILES["img"]["type"][$i] == "image/gif")
					{
						$url =$random_name.'.'.$fileExt;
					}
					echo $random_name.'.'.$fileExt;
		 $manipulator = new ImageManipulator($_FILES['img']['tmp_name'][$i]);
		 
       $newImage = $manipulator->resample(1024, 683);
	
        $manipulator->save('../upload/main/' . $random_name.'.'.$fileExt);
		
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
			//}
		 $image_collection = implode(',', $image_list);
		//  echo $image_collection;
		//echo "in startprocess".$_SESSION['society_id']."<br />";	
		//echo $_POST['reportedby'];
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$request_no = $obj_LatestCount->getLatestRequestNo($_SESSION['society_id']);
		
		if($_POST['category'] == $_SESSION['RENOVATION_DOC_ID'])
		{
			$details = "This is Renovation Request.";
		}
		else if($_POST['category'] == $_SESSION['ADDRESS_PROOF_ID'])
		{
			$details = "This is Address Proof Request.";
		}
		else
		{
			$details = $_POST['details'];
		}
		//$request_no = $request_no + 1;
		// change to  $_POST['unit_no'] to $_POST['unit_no2']; in insert statment
		  $sql = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby`, `dateofrequest`, `email`, `phone`, `priority`, `category`, `summery`,`img`, `details`, `status`, `unit_id`) VALUES ('".$request_no."', '".$_SESSION['society_id']."', '".$_POST['reported_by']."', '".getDBFormatDate(date('d-m-Y'))."', '".$_POST['email']."', '".$_POST['phone']."', '".$_POST['priority']."', '".$_POST['category']."', '".$_POST['summery']."','$image_collection', '".$details."', 'Raised', '".$_POST['unit_no2']."')";					
		//echo "query:".$sql;  	
		$result = $this->m_dbConn->insert($sql);
		if($_POST['category'] == $_SESSION['RENOVATION_DOC_ID'] || $_POST['category'] == $_SESSION['ADDRESS_PROOF_ID'])
		{
			$_SESSION['renovation_service_request_id'] = $result;
		}
		$sqlSR = $this->GetCategoryDetails( $_POST['category']);
		$EmailIDOfCategory = ""; 
		if(isset($sqlSR) && sizeof($sqlSR) > 0)
		{
			$EmailIDOfCategory = $sqlSR[0]['email'];
			$CCEmailIDOfCategory = $sqlSR[0]['email_cc'];
		}
		//echo $EmailIDOfCategory;
		$this->sendEmail($request_no, $_POST['reportedby'], 'Raised', $_POST['details'], $_POST['email'], $EmailIDOfCategory, $CCEmailIDOfCategory,$_POST['unit_no2']);
		
		$this->ServiceRequestMobileNotification($request_no, $_POST['category'], $_POST['priority'], $_POST['summery'], $EmailIDOfCategory, $CCEmailIDOfCategory,$sqlSR[0]['unitID'],$sqlSR[0]['co_unitID'], $_POST['unit_no'], true);
		
		$this->ServiceRequestSMS($_POST['summery'],$sqlSR[0]['unitID'],$sqlSR[0]['co_unitID'],$_POST['unit_no']);
		header("Location: ../servicerequest.php?type=open");
	}
	}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$id=$_REQUEST['request_id'];
			//echo $id;
			$image_list=array(); 
			$select = "select `img` FROM `service_request` WHERE request_id='".$id."'";
			$res2 =$this->m_dbConn->select($select);
				//print_r($res2);
				 $image=$res2[0]['img'];
				if($image <> "")
				{
					$image_list = explode(',', $image);
				}
		for($i=0; $i<count($_FILES['img']['name']); $i++)
			{
				//print_r($_FILES);
				$file_type=$_FILES['img']['type'][$i];
				$file_size=$_FILES['img']['size'][$i];
				$file_tmp=$_FILES['img']['tmp_name'][$i];
				list($txt, $ext) = explode(".", $file);
				$randon_name = $file.".".$ext;
				$kaboom = explode(".", $_FILES['img']['name'][$i]); // Split file name into an array using the dot
				 $fileExt = end($kaboom);
				 $random_name= rand();
				//echo $random_name;
			
				if($_FILES["img"]['name'][$i]<>'')
				{
				if ($_FILES["img"]["size"][$i] > 10240*1024) 
				{
					 $error="Sorry, your file is too large.";
					 $this->table .= "Sorry, your file is too large.";
				}
				else if (($_FILES["img"]["type"][$i] == "image/gif") || 
						($_FILES["img"]["type"][$i] == "image/jpeg") || 
						($_FILES["img"]["type"][$i]== "image/png") || 
						($_FILES["img"]["type"][$i] == "image/pjpeg")) 
				{
					//echo "2";
			
					if ($_FILES["img"]["type"][$i] == "image/jpeg")
					{ //echo"jpeg type";
						$url =$random_name.'.'.$fileExt;
					}
					else if($_FILES["img"]["type"][$i] == "image/png")
					{//echo"png type";
						$url =$random_name.'.'.$fileExt;
					}
					else if ($_FILES["img"]["type"][$i] == "image/gif")
					{
						$url =$random_name.'.'.$fileExt;
					}
					//echo $random_name.'.'.$fileExt;
		 $manipulator = new ImageManipulator($_FILES['img']['tmp_name'][$i]);
		 
       $newImage = $manipulator->resample(1024, 683);
	
        $manipulator->save('../upload/main/' . $random_name.'.'.$fileExt);
		
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
	}
		 $image_collection = implode(',', $image_list);
		//print_r($_REQUEST);
		
		 $up_query="update `service_request` set `email`='".$_POST['email']."',`phone`='".$_POST['phone']."',`priority`='".$_POST['priority']."',`category`='".$_POST['category']."',`summery`='".$_POST['summery']."', `details`='".$_POST['details']."' ,`img`='$image_collection' where  `request_id`='".$id."' and `society_id`=".$_SESSION['society_id']." ";
			//die();
			$data = $this->m_dbConn->update($up_query);
		
			//$this->ServiceRequestSMS($_POST['phone'], $request_no, $_POST['summery'], $_POST['category'], $_POST['unit_no'],$EmailIDOfCategory, $CCEmailIDOfCategory);
			//echo $data;
			//die();
			
		$sqlSR = $this->GetCategoryDetails($_POST['category']);
		$EmailIDOfCategory = ""; 
		if(isset($sqlSR) && sizeof($sqlSR) > 0)
		{
			$EmailIDOfCategory = $sqlSR[0]['email'];
			$Emailidofmember2 = $sqlSR[0]['other_email'];
			$CCEmailIDOfCategory = $sqlSR[0]['email_cc'];
		}		
			$this->ServiceRequestMobileNotification($_POST['request_no'], $_POST['category'], $_POST['priority'], $_POST['summery'],$EmailIDOfCategory, $CCEmailIDOfCategory,$sqlSR[0]['unitID'],$sqlSR[0]['co_unitID'], $_POST['unit_no'], false);
			$return_value="Update";
			//return $result;
		}
		
		
}
	public function insertComments($request_no,$email, $ccEmails)
	{
		if($_SESSION['role'] && $_SESSION['role']==ROLE_ADMIN)
		{
			$updateReqPriority="update `service_request` set `priority`='".$_POST['priority']."' where  `request_no`=".$request_no." and `society_id`=".$_SESSION['society_id']." ";
			$priority = $this->m_dbConn->update($updateReqPriority);
		}
		
		$sql = "INSERT INTO `service_request` (`request_no`, `society_id`, `reportedby` , `summery`, `status`, `unit_id`,`email`) VALUES ('".$request_no."', '".$_SESSION['society_id']."', '".$_POST['changedby']."', '".$_POST['comments']."', '".$_POST['status']."', '".$_POST['unit']."', '".$_POST['emailID']."')";						
		//echo $sql;		
		$result = $this->m_dbConn->insert($sql);
		$this->sendEmail($request_no, $_POST['changedby'], $_POST['status'], $_POST['comments'], $email, $ccEmails, $_POST['unit']);
		return;		
	}
	
	public function GetCategoryDetails($sCategory)
	{
		$sqlSRQuery  = "select ID, unitID, co_unitID,category, email,email_cc from `servicerequest_category` where ID='". $sCategory."'";
		return $this->m_dbConn->select($sqlSRQuery);
	}
		
	public function GetMemberName($sCategory)
	{
		//select c.category, c.member_id, m.mem_other_family_id, m.other_name from `servicerequest_category` c, `mem_other_family` m where m.mem_other_family_id = c.member_id and c.ID=9
		$sqlSRQuery  = "select c.category, c.co_member_id, m.mem_other_family_id, m.other_name, m.other_email from `servicerequest_category` c, `mem_other_family` m where m.mem_other_family_id = c.co_member_id and c.ID='". $sCategory."'";
		//print_r($this->m_dbConn->select($sqlSRQuery));
		return $this->m_dbConn->select($sqlSRQuery);
	}
	
	public function GetUnitNoIfZero($request_no)
	{
		$sqlSRQuery = "SELECT unit_id FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `request_no` = '".$request_no."'  and  `visibility`='1'";	
		return $this->m_dbConn->select($sqlSRQuery);
	}
	
	public function GetUnitNoIfNZero($request_no)
	{
		$fArray=array();
		//SELECT s.unit_id, u.unit_no FROM `unit` u, `service_request` s where u.unit_id = s.unit_id
		//SELECT s.unit_id, u.unit_no FROM `service_request` s, `unit` u WHERE s.unit_id=u.unit_id AND s.`society_id` = '59' AND s.`request_no` = '53' and s.`visibility`='1'
		$sqlSRQuery = "SELECT s.`unit_id`, u.`unit_no` FROM `service_request` s, `unit` u WHERE s.`unit_id` = u.`unit_id` AND s.`society_id` = '".$_SESSION['society_id']."' AND s.`request_no` = '".$request_no."'  and  s.`visibility`='1' and u.unit_id = s.unit_id";	
		$res_req= $this->m_dbConn->select($sqlSRQuery);
		
		
		return $res_req;
	}
		
	public function getDetails()
	{
		$sql = "SELECT * FROM `member_main` WHERE `unit` = '".$_SESSION['unit_id']."' AND `society_id` = '".$_SESSION['society_id']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;	
	}	
	
	public function getRecords($id, $type="")
	{
		
		 $sqlSelect="select mof.mem_other_family_id,mof.other_name,mm.unit from mem_other_family as mof JOIN member_main as mm ON mm.member_id = mof.member_id JOIN `servicerequest_category` as sc ON sc.member_id=mof.mem_other_family_id where mm.unit= '".$_SESSION['unit_id']."'";
		$MemberID = $this->m_dbConn->select($sqlSelect);
		
		if($MemberID <> '')
		{
			$SqlCategories ="select * from servicerequest_category where member_id='".$MemberID[0]['mem_other_family_id']."'";
			$sCategoryID = $this->m_dbConn->select($SqlCategories);
			//print_r($sCategoryID);
		}
		
			if($sCategoryID[0]['ID'] == '')
			{
		 		$catID= 0;	
			}
			else
			{
			$catID=$sCategoryID[0]['ID']; 	
			}
		
		
			if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
			
			{
				if( $type == "assign")
				{
					  $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id <= m2.request_id) WHERE m2.request_id AND m1.category = '".$catID."' and m1.visibility='1' ";
				}
				else
				{
					 $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE  m2.request_id IS NULL  and m1.`visibility`='1' ";
				}
			
			}
			
			else
			{
				if($type == "assign" && $MemberID[0]['mem_other_family_id'] <>'')
				{
					  $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id <= m2.request_id) WHERE m2.request_id AND m1.category = '".$catID."' and m1.visibility='1'  ";
				}
				else 
				{
					$sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE m2.request_id IS NULL AND m1.unit_id = '".$_SESSION['unit_id']."' and m1.visibility='1' ";
				}
				
			}
		
			if($type <> "" && $type == "resolved")
			{
				if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
				{
					$sql .= '  and  m1.status="Resolved" OR  m1.status="Closed" and m2.unit_id = "0"';
				}
				else
				{
					$sql .= '  and  m1.status="Resolved" OR  m1.status="Closed" and m1.unit_id = "'.$_SESSION['unit_id'].'"';	
				}
			}
			else if($type <> "" && $type == "createdme" )
			{ 
				if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN  )
				{
					$sql .= ' and m1.reportedby="'.$_SESSION['name'].'" and m1.unit_id NOT IN(0)';
				}
				else if($_SESSION['role']==ROLE_ADMIN_MEMBER  )
				{
				 $sql .= ' NOT IN ( m1.status="Resolved", m1.status="Closed")  and m1.unit_id="'.$_SESSION['unit_id'].'" ';
				}
				else
				{
					 $sql .= ' NOT IN ( m1.status="Resolved", m1.status="Closed")';
				}
				
			}
		
			else if($type <> "resolved") 
			{
				
				 $sql .= '   NOT IN ( m1.status="Resolved", m1.status="Closed")';	
			}
			
			$sql .= '  ORDER BY m1.request_no DESC';
		//echo $sql;
			$result = $this->m_dbConn->select($sql);
		
		for($i=0;$i<count($result);$i++)
		{
			$sql="select * from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			$res1 = $this->m_dbConn->select($sql);
			$result[$i]['status']=$res1[0]['status'];
			$result[$i]['dateofrequest'] = $res1[(sizeof($res1)-1)]['dateofrequest']; 
			$result[$i]['priority'] = $res1[(sizeof($res1)-1)]['priority']; 
			$result[$i]['category'] = $res1[(sizeof($res1)-1)]['category']; 
			$result[$i]['summery'] = $res1[(sizeof($res1)-1)]['summery']; 
			$result[$i]['unit_id'] = $res1[(sizeof($res1)-1)]['unit_id']; 
			$result[$i]['reportedby'] = $res1[(sizeof($res1)-1)]['reportedby']; 
				
		}
	//}
		//var_dump($result);	
		return $result;
	//}
	}
/*	public function getRecords($id, $type="")
	{
		
		  $sqlSelect="select mof.mem_other_family_id,mof.other_name,mm.unit from mem_other_family as mof JOIN member_main as mm ON mm.member_id = mof.member_id JOIN `servicerequest_category` as sc ON sc.member_id=mof.mem_other_family_id where mm.unit= '".$_SESSION['unit_id']."'";
		$MemberID = $this->m_dbConn->select($sqlSelect);
		//print_r($MemberID);
		if($MemberID <> '')
		{
			$SqlCategories ="select * from servicerequest_category where member_id='".$MemberID[0]['mem_other_family_id']."'";
			$sCategoryID = $this->m_dbConn->select($SqlCategories);
			//print_r($sCategoryID);
			
		}
		if($type <> "assign")
		{
			if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
			{
			
		  		$sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE  m2.request_id IS NULL  and m1.`visibility`='1'";
			}
		
			else
			{
				  $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id < m2.request_id) WHERE m2.request_id IS NULL AND m1.unit_id = '".$_SESSION['unit_id']."' and m1.visibility='1' ";
			}
			if($type <> "" && $type == "resolved" )
			{
				echo $type;
				 $sql .= '  and  m1.status="Resolved" OR  m1.status="Closed" ';	
			}
			else if($type <> "resolved" ) 
			{
				//echo $sql .= '  and m1.status <> "Resolved" OR m1.status <> "Closed"';	
				 $sql .= '   NOT IN ( m1.status="Resolved", m1.status="Closed")';	
			}
		
		
			$sql .= '  ORDER BY m1.request_no DESC';
		
			$result = $this->m_dbConn->select($sql);
		}
		else if($type <> "" && $type <> "resolved")
		{
		
	  $sql = "SELECT m1.* FROM service_request m1 LEFT JOIN service_request m2 ON (m1.request_no = m2.request_no AND m1.request_id <= m2.request_id) WHERE m2.request_id AND m1.category = '".$sCategoryID[0]['ID']."' and m1.visibility='1' ";
		$result = $this->m_dbConn->select($sql);
		
		}
		for($i=0;$i<count($result);$i++)
		{
			$sql="select * from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			$res1 = $this->m_dbConn->select($sql);
			$result[$i]['status']=$res1[0]['status'];
			$result[$i]['dateofrequest'] = $res1[(sizeof($res1)-1)]['dateofrequest']; 
			$result[$i]['priority'] = $res1[(sizeof($res1)-1)]['priority']; 
			$result[$i]['category'] = $res1[(sizeof($res1)-1)]['category']; 
			$result[$i]['summery'] = $res1[(sizeof($res1)-1)]['summery']; 
			$result[$i]['unit_id'] = $res1[(sizeof($res1)-1)]['unit_id']; 
			
				
		}
		
		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";	
		return $result;
	}
*/	
	
	public function getRecordsRight($id)
	{
	if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			$sql="select * from `service_request` inner join (select request_no, min(timestamp) as ts from `service_request` group by request_no) maxt on (`service_request`.request_no = maxt.request_no and `service_request`.timestamp = maxt.ts)   WHERE service_request.`society_id` = ".$_SESSION['society_id']." and service_request.`visibility`='1'  ORDER BY service_request.request_no  DESC  LIMIT 5 ";
			
		}
		else
		{
			$sql="select * from `service_request` inner join (select request_no, min(timestamp) as ts from `service_request` group by request_no) maxt on (`service_request`.request_no = maxt.request_no and `service_request`.timestamp = maxt.ts)   WHERE service_request.`unit_id` = ".$_SESSION['unit_id']." and service_request.`visibility`='1'  ORDER BY service_request.request_no  DESC  LIMIT 5 ";
			
		}
		$result = $this->m_dbConn->select($sql);
		//print_r($result);
		for($i=0;$i<count($result);$i++)
		{
			$sql="select status from service_request where request_no='".$result[$i]['request_no']."' order by timestamp DESC";
			$res1 = $this->m_dbConn->select($sql);
			$result[$i]['status']=$res1[0]['status'];
		}
		return $result;
	}
	public function getnewdetails($Id)
	{
		$sql="select * from service_request where request_no=".$Id."";
		$res=$this->m_dbConn->select($sql);
		return $res;
	}

	
	public function getViewDetails($request_no,$isview=false)
	{ 
		$fieldname='request_id';
		if($isview==true)
		{
				$fieldname='request_no';
		}
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['unit_id']==0))
		{
			//SELECT service_request.*,`unit`.unit_no FROM `service_request` join `unit` on `service_request`.unit_id=`unit`.unit_id WHERE service_request.`society_id` = '156' AND `request_no` = '19' and `visibility`='1'
			$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `".$fieldname."` = '".$request_no."'  and  `visibility`='1'";	
		}
		else
		{
			$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `".$fieldname."` = '".$request_no."'  and  `visibility`='1'";	
		}
		
		$result = $this->m_dbConn->select($sql);
		$result[0]['raisedDate'] = getDisplayFormatDate($result[0]['dateofrequest']);
		for($i = 0;$i < sizeof($result);$i++)
		{
			if($result[$i]['category'] == $_SESSION['RENOVATION_DOC_ID'])
			{
				$sql = "Select `Id` from `renovation_details` where request_id = '".$result[$i]['request_id']."';";
				$sqlRes = $this->m_dbConn->select($sql);
				$result[$i]['Id'] = $sqlRes[0]['Id'];
			}
			else if($result[$i]['category'] == $_SESSION['TENANT_REQUEST_ID'])
			{
				$sql = "Select `tenant_id` from `tenant_module` where serviceRequestId = '".$result[$i]['request_id']."';";
				$sqlRes = $this->m_dbConn->select($sql);
				$result[$i]['tenant_id'] = $sqlRes[0]['tenant_id'];
			}
			else
			{
				$result[$i]['Id'] = 0;
				$result[$i]['tenant_id'] = 0;
			}
		}
		return $result;
	}
	
	public function getUpdatedStatus($requestNo)
	{
		$sql = "SELECT `status` FROM `service_request` WHERE `visibility`='1' and `request_no` = '".$requestNo."'  ";
		$result = $this->m_dbConn->select($sql);
		return $result[sizeof($result) - 1]['status'];	
	}
		
	public function comboboxEx($query,$id)
	{ //$str.="<option value=''>All</option>";
		$str.="<option value='0'>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
				
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}	
		}
		return $str;
	}
	
	
	
	
	public function combobox1($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConn->select($query);
		//echo $data;
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
		return $str;
	}
		
	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
		return $str;
	}
	
//***--------------------------Service Request Mobile Notificatiion---------------------------------


	public function ServiceRequestMobileNotification($RequestNo, $CategoryID, $PriorityID, $summery, $EmailIDOfCategory, $CCEmailIDOfCategory,$C_unitID,$C_co_unitID, $RequestedUnitID,$IsCreated)
	{					
						$RequestedUnitEmail = $this->m_objUtility->GetmemberDetail($RequestedUnitID);
						$RequestedUnitDetails = array('unit_id' => $RequestedUnitID,'email' => $RequestedUnitEmail[0]['email']);
						$CategoryDetails = array('unit_id' => $C_unitID,'email' => $EmailIDOfCategory);
						$CategoryDetails1 = array('unit_id' => $C_co_unitID,'email' => $CCEmailIDOfCategory);
						
						if($IsCreated == true)
						{
							$ServicePrdTitle = "Service Request Created";	
						}
						else 
						{
							$ServicePrdTitle = "Service Request Updated";
						}
						$ServicePrdMassage = $summery;
						$dbName = $_SESSION['dbname'];
						$SocietyID = $_SESSION['society_id'];
					
						$obj_dbConn= new dbop(false,$dbName);
						$obj_bbConnRoot=new dbop(true)	;
						$obj_Fetch = new FetchData($obj_dbConn,$obj_bbConnRoot);
						$emailIDList = $this->GetEmailIDToSendNotification($RequestedUnitID);
						array_push($emailIDList,$CategoryDetails,$CategoryDetails1,$RequestedUnitDetails);
						for($i = 0; $i < sizeof($emailIDList); $i++)
						{	
						  if(($emailIDList[$i]['email'] <> ""))
						  {
							$unitID = $emailIDList[$i]['unit_id'];
							$objAndroid = new android($emailIDList[$i]['email'], $SocietyID, $unitID);
							$sendMobile = $objAndroid->sendServiceRequestNotification($ServicePrdTitle, $ServicePrdMassage, $RequestNo, $CategoryID, $PriorityID);
								
						  }
						}
	
	}
	
	
	public function GetEmailIDToSendNotification($RequestedUnitID)
	{
			$UnitDetails = array();
			$CurrentUserDetail = $this->m_dbConnRoot->select("SELECT m.unit_id, l.member_id as email from login as l JOIN mapping as m ON l.login_id = m.login_id WHERE l.login_id = '".$_SESSION['login_id']."' AND  m.society_id = '".$_SESSION['society_id']."' group by l.member_id");
			
			for($j = 0; $j < sizeof($CurrentUserDetail); $j++)
			{
				array_push($UnitDetails, $CurrentUserDetail[$j]);
			}
		
			$FetchSociety_details = $this->m_dbConnRoot->select("SELECT m.unit_id, l.member_id as email FROM mapping as m JOIN login as l ON m.login_id = l.login_id JOIN profile as p ON m.profile = p.id WHERE  PROFILE_SERVICE_PROVIDER = 1 AND m.society_id = '".$_SESSION['society_id']."' group by l.member_id");
			
			for($k = 0; $k < sizeof($FetchSociety_details); $k++)
			{
				array_push($UnitDetails, $FetchSociety_details[$k]);	
			} 
			return $UnitDetails;
		 
		
	}
	
	//**---------------------------------ServiceRequestSMS---------------------------------------------
	
	public function ServiceRequestSMS($SRTitle, $SpUnitID, $SpCoUnitID, $RequestedUnitID)
	{
		$unitDetails = array();
		$UnitToSMS = array("0"=>$SpUnitID, "1"=>$SpCoUnitID, "2"=>$RequestedUnitID, "3"=>$_SESSION['unit_id']);
		$msgBody = '';
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'");
		
		$SocietyMobile = $this->m_dbConn->select("SELECT `phone2` as mob , `society_code` as unit_no FROM society where society_id ='".$_SESSION['society_id']."'");
		array_push($unitDetails, $SocietyMobile[0]);
		
		for($i = 0; $i<sizeof($UnitToSMS); $i++)
		{
			$UnitMobileNumber = $this->m_objUtility->GetmemberDetail($UnitToSMS[$i]);
			for($j =0 ; $j <sizeof($UnitMobileNumber); $j++)
			{
					array_push($unitDetails,$UnitMobileNumber[$j]);
			}
		}	
						
				$msgBody = "".$smsDetails[0]['sms_start_text'].", ".$SRTitle." New Service Request is Generated. Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']." ";
	
		//**----Making log file name as SendClassifiedSMS.html to track Classified sms logs ----**
		
		$Logfile=fopen("SendServiceRequestSMS.html", "a");	
		$msg = "<center><b><font color='#003399' >  DATE : </b>".date('Y-m-d')."</font></center> <br /> ";
		fwrite($Logfile,$msg);		
		date_default_timezone_set('Asia/Kolkata');
				
		$msg = "<b>DBNAME : </b>". $_SESSION['dbname'] ."<br /><b> SOCIETY : </b>".$smsDetails[0]['society_name']."<br /><b> START TIME : </b>".date('Y-m-d h:i:s ')."<br /><br />";

		fwrite($Logfile,$msg);
		
		//** --------- Now further code execute for requested unit---**
		for($i = 0 ; $i < sizeof($unitDetails) ; $i++)
		{
			//echo '<BR>After getting array values';
			//**-----Check mobile number exits---**
				if($unitDetails[$i]['mob'] <> '' && $unitDetails[$i]['mob'] <> 0)
				{	
					//echo '<BR> We got some mobile number '.$unitDetails[$i]['mob'];
					$smsText = $msgBody;
					
					//**Check for client id 	
					$clientDetails = $this->m_dbConnRoot->select("SELECT `client_id` FROM  `society` WHERE  `dbname` ='".$_SESSION['dbname']."' ");
					if(sizeof($clientDetails) > 0)
					{
						$clientID = $clientDetails[0]['client_id'];
					}
					//**---Calling SMS function for utility---***
					$response =  $this->m_objUtility->SendSMS($unitDetails[$i]['mob'], $smsText, $clientID);
					$ResultAry[$unitDetails[$i]['unit_id']] =  $response;
					$status = explode(',',$response);	
					//echo '<BR>Status'.$status[1];	
					$msg = "<b>** INFORMATION ** </b>Unit - '".$unitDetails[$i]['unit_no']."' : Message Sent['".$smsText."']. <br /><br />";
					fwrite($Logfile,$msg);
					
					$current_dateTime = date('Y-m-d h:i:s ');
				}
				else
				{
					$msg = "<b>** ERROR ** </b>Unit - '".$units[$i]['unit_no']."' : Invalid Mobile Number. <br /><br />";
					fwrite($Logfile,$msg);
				}		
		}
		$msg = "<b> END TIME : </b>".date('Y-m-d h:i:s ')."<br /><hr />";
		fwrite($Logfile,$msg);
		
		return true;

	}
	
	public function sendEmail($requestNo, $name, $status, $desc, $email,$catEmail = '',$catEmailCC = '', $RequestedID)
	{	
		$RequestedEmailID = $this->m_objUtility->GetmemberDetail($RequestedID);
		$details = $this->getViewDetails($requestNo,true);

		$CategoryDetails = $this->GetCategoryDetails($details[0]['category']);
		//print_r($CategoryDetails);
		$CoAssignedemailDetails = $this->GetMemberName($details[0]['category']);

		
		$societyName =  $this->objFetchData->objSocietyDetails->sSocietyName;
		//$this->objFetchData->objSocietyDetails->sSocietyEmail
		date_default_timezone_set('Asia/Kolkata');
		
		$mailSubject = "[SR#".$requestNo."] - ".substr(strip_tags($details[0]['summery']),0,50)." - ".$status;
		$Raisename=$details[0]['reportedby'];
		$raisedtimestamp = strtotime($details[0]['timestamp']);
		$updatedtimestamp = strtotime($details[sizeof($details)-1]['timestamp']);
		//$url="<a href='http://localhost/society-shared-template/viewrequest.php?rq=".$requestNo. "'>Go to Service Request</a>";
		$url="<a href='http://way2society.com/viewrequest.php?rq=".$requestNo. "'>http://way2society.com/viewrequest.php?rq=".$requestNo. "</a>";
		
		
		if($status == 'Raised')
		{
			
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>' .$societyName .'</b> </td></tr>	
							<tr> <td colspan="3"> <b> New Service Request [SR#'.$requestNo.'] Raised: </b> </td></tr>
							<tr> <td style="width:30%;border-right:none;"><b>Raised By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$Raisename.'<br/>'.date("d-m-Y (g:i:s a)", $raisedtimestamp).'</td></tr>
							<tr><td style="border-right:none;"><b>Category</b></td><td style="border-left:none;"> : </td><td>'.$CategoryDetails[0]['category'].'</td></tr>
							<tr><td style="border-right:none;"><b>Priority</b></td><td style="border-left:none;"> : </td><td>'.$details[0]['priority'].'</td></tr>
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"> : </td><td>'.$status.'</td></tr>
    						
							<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>
							<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>
							     
						</table><br />'	;		
		}
		else
		{
												
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>' .$societyName .'</b> </td></tr>  
							<tr> <td colspan="3"> <b>Service Request [SR#'.$requestNo.'] Updated: </b> </td></tr>  							
							<tr> <td style="width:30%;border-right:none;"><b>Updated By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$name.' <br/> '.date("d-m-Y (g:i:s a)", $updatedtimestamp).'</td></tr>
							<tr> <td style="width:30%;border-right:none;"><b>Raised  By</b></td><td style="width:10%;border-left:none;"> : </td><td style="width:60%;">'.$Raisename.' <br/> '.date("d-m-Y (g:i:s a)", $raisedtimestamp).'</td></tr>
							<tr><td style="border-right:none;"><b>Category</b></td><td style="border-left:none;"> : </td><td>'.$CategoryDetails[0]['category'].'</td></tr>
							<tr><td style="border-right:none;"><b>Priority</b></td><td style="border-left:none;"> : </td><td>'.$details[0]['priority'].'</td></tr> 
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"> : </td><td>'.$status.'</td></tr>
    						
							<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>
							<tr><td style="border-right:none;"><b>Comments</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>
							
							        
						</table><br />'	;			
		}
		
		$mailBody .="You may view or update this service request by copying below link to browser or by clicking here<br />".$url;
		// Create the mail transport configuration				
	  $societyEmail = "";	  
	  if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  {
		 $societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  }
	  else
	  {
		 $societyEmail = "techsupport@way2society.com";
	  }
	  	 
	  try
	  {	
			  $EMailIDToUse = $this->m_objUtility->GetEmailIDToUse(false, 0, 0, 0, 0, 0, $_SESSION['society_id']);
				//print_r($EMailIDToUse);
			
			if($EMailIDToUse['status'] == 0)
			{	
				//$EMailID = $EMailIDToUse['email'];
				//$Password = $EMailIDToUse['password'];			
				//$host = "103.50.162.146";
				//$host = "smtp.gmail.com";
				//$transport = Swift_SmtpTransport::newInstance($host,587)
					//	->setUsername($EMailID)
						//->setSourceIp('0.0.0.0')
					//	->setPassword($Password) ; 
				//AWS Config
				
				$AWS_Config = CommanEmailConfig();
				 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				   ->setUsername($AWS_Config[0]['Username'])
				  ->setPassword($AWS_Config[0]['Password']);	 														
				// Create the message

				$arraymail =array();

				array_push($arraymail ,array($CategoryDetails[0]['email'],$CoAssignedemailDetails[0]['other_email'],$CategoryDetails[0]['email_cc']));

				$arrEmailsfinal = implode(",", $arraymail[0]);
				$emailContent =  $mailBody ;
				//send to member who created request		
				$message = Swift_Message::newInstance();
				$message->setTo(array(
					$CategoryDetails[0]['email'] => $CategoryDetails[0]['email'], 
					$CategoryDetails[0]['email_cc'] => $CategoryDetails[0]['email_cc'],$CoAssignedemailDetails[0]['other_email'] => $CoAssignedemailDetails[0]['other_name'],
					$RequestedEmailID[0]['email'] => $RequestedEmailID[0]['primary_owner_name'])); //$mailToName  email email_cc 
				

				$message->setSubject($mailSubject);
				$message->setBody($emailContent);
				$message->setFrom(array('no-reply@way2society.com' => 'way2society'));
				if($status == 'Raised')
				{
					if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN ||  $_SESSION['role']==ROLE_SUPER_ADMIN))
					{
						$message->setFrom('no-reply@way2society.com', $name);
					}
					else
					{
						$from=$_SESSION['name']."[".$_SESSION['desc']."] ";
						$message->setFrom('no-reply@way2society.com', $name);
						$message->setCc(array($societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName));
					}
				}
					else
					{
							$message->setFrom("no-reply@way2society.com", $this->objFetchData->objSocietyDetails->sSocietyName);
							$message->setTo(array($arEmails[0] => '',$arEmailsCC[0] => ''));
							$message->setCc(array($societyEmail => $this->objFetchData->objSocietyDetails->sSocietyName));				
					}
					
				$message->setContentType("text/html");										 
						
				// Send the email				
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);											
								
				if($result > 0)
				{
					echo 'Success';
				}
				else
				{
					echo 'Failed';
				}
			}
			
	  }
		catch(Exception $exp)
		{
			echo "Error occured in email sending.".$exp;
		}
	}
	
	function getEmailFromCategory()
	{
		$sql = "SELECT `email` FROM `servicerequest_category` WHERE `id` = '".$_REQUEST['categoryId']."'";				
		$result = $this->m_dbConn->select($sql);
		return $result[0]['email'];
	}
	
		public function up_photo($name,$tmp_path,$location)
	{
		 $photo_name = $name;
		 $photo_name1 = str_replace(' ','-',$name);
		 $old_path = $tmp_path;
		 $new_path = $location.'/'.time().'_'.$photo_name1;
		 $image = move_uploaded_file($old_path,$new_path);
		
		return $new_path;
	}
	public function thumb_photo($thumbWidth,$thumbHeight,$pathToThumbs,$newpath,$exe,$image_name)
	{
		$kk = 0;
					
	  if($exe=='.jpg' || $exe=='.jpeg')
	  {
		$img = imagecreatefromjpeg($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
		<!--	<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.gif')
	  {
		$img = imagecreatefromgif($newpath);				  //die();				  
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.png')
	  {
		$img = imagecreatefrompng($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else if($exe=='.bmp')
	  {
		$img = imagecreatefromwbmp($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else {} 
		  
	  if($kk<>1)
	  {
		  $width  = imagesx($img);
		  $height = imagesy($img);

		  $new_width  = $thumbWidth;
		  $new_height = $thumbHeight;
	
		  $tmp_img = imagecreatetruecolor($new_width,$new_height);
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		  imagejpeg($tmp_img,"{$pathToThumbs}{$image_name}");
		  
		  $thum_path = $pathToThumbs.$image_name;
		  
		  return $thum_path;
	  }
	}
	
	public function selecting($id)
	{
		$sql= "SELECT `request_id`,`reportedby`,`dateofrequest`,`email`,`phone`,`priority`,`category`,`summery`,`unit_id`,`details`, `request_no` FROM `service_request` WHERE  `request_id` = '".$id."'";
		//$sql = "SELECT * FROM `notices` WHERE `id` = '".$_REQUEST['noticeId']."'";		
		$res = $this->m_dbConn->select($sql);
		//print_r($res);
			if($res <> '')
		{
			$res[0]['dateofrequest'] = getDisplayFormatDate($res[0]['dateofrequest']);
		}
		return $res;
}
public function selecting1($request_no)
	{
		$PArray=array();
		$sqlQuery = "select * from tasklist where request_no='".$request_no."'";	
		//echo $sqlQuery;			
		$res = $this->m_dbConn->select($sqlQuery);	
		//print_r($res);
		//return $res;
		for($i=0;$i<sizeof($res);$i++)
		{
			$taskowner=$res[$i]['Task_Owner'];
			$title=$res[$i]['Title'];
			$priority=$res[$i]['Priority'];
			$duedate=$res[$i]['DueDate'];
			$status=$res[$i]['Status'];
			$pcomp=$res[$i]['PercentCompleted'];
			
			 $sql="select mapping.id,login.name from mapping join login on mapping.login_id=login.login_id where mapping.id='".$taskowner."'";
			 $r=$this->m_dbConnRoot->select($sql);
			for($k=0;$k<sizeof($r);$k++){
				  $name=$r[$k]['name'];
				  array_push($PArray,array('nameto'=>$name,'title'=>$title,'priority'=>$priority,'duedate'=>$duedate,'status'=>$status,'pcomp'=>$pcomp));				 
			}
	//var_dump($PArray);
		}
		return $PArray;
	
	}

public function deleting($id)
	{
	 $sql = "update  `service_request` set `visibility`='0' where request_no='".$id."'";
		$res = $this->m_dbConn->update($sql);
		return $res;
	}
	public function getRenovationId() //Vaishali
	{
		$sql2 = "SELECT `Value` FROM `appdefault_new` WHERE `Property` = 'RenovationRequestCategoryId' and module_id = '2'";
		$renovationRequestId = $this->m_dbConn->select($sql2);
		$_SESSION['RENOVATION_DOC_ID'] = $renovationRequestId[0]['Value'];
		$sql3 = "SELECT `Value` FROM `appdefault_new` WHERE `Property` = 'AddressRequestCategoryId' and module_id = '4'";
		$addressRequestId = $this->m_dbConn->select($sql3);
		$_SESSION['ADDRESS_PROOF_ID'] = $addressRequestId[0]['Value'];
		$sql4 = "SELECT `Value` FROM `appdefault_new` WHERE `Property` = 'TenantRequestCategoryId'";
		$tenantRequestId = $this->m_dbConn->select($sql4);
		$_SESSION['TENANT_REQUEST_ID'] = $tenantRequestId[0]['Value'];
		$result = array();
		$result['RenovationRequestId'] = $renovationRequestId[0]['Value'];
		$result['AddressProofId'] = $addressRequestId[0]['Value'];
		$result['TenantRequestId'] = $tenantRequestId[0]['Value'];
		return ($result);
	}
	public function getMemberId($unitId)
	{
		$sql1 = "SELECT `member_id` FROM `member_main` where unit = '".$unitId."'";
		$sql1_res = $this->m_dbConn->select($sql1);
		return ($sql1_res[0]['member_id']);
	}
	public function getMemberAddress($unitId,$societyId)
	{
		$sql1 = "Select s.`society_add`,s.`city`,s.`region`,s.`postal_code`,u.`unit_no`, u.`floor_no`,w.`wing` FROM `society` as s,`unit` as u,wing as `w` where u.`wing_id` = w.`wing_id` and u.`unit_id` = '".$unitId."' and u.`society_id` = '".$societyId."';";
		$sql1_res = $this->m_dbConn->select($sql1);
		//var_dump($sql1_res);
		$floor = $sql1_res[0]['floor_no'];
		$cLast = substr($floor, -1);
		$sAppend = "th";
		if($cLast == '1')
		{
			$sAppend = "st";
		}
		if($cLast == '2')
		{
			$sAppend = "nd";
		}
		if($cLast == '3')
		{
			$sAppend = "rd";
		}
		$address = $sql1_res[0]['unit_no']." ".$sql1_res[0]['floor_no']."".$sAppend." floor, ".$sql1_res[0]['wing']." Wing, ".$sql1_res[0]['society_add'];
		return ($address);
	}
}
?>
