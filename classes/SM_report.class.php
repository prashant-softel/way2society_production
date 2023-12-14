<?php
include_once("utility.class.php");
class SM_Report 
{
	
  public $m_dbConn;	
  public $m_dbConnRoot;		
  public $smConn;
  public $smConnRoot;
  public $m_objUtility;
  public $m_fetch;
  public $actionPage;
  

  function __construct($dbConn,$dbConnRoot,$smConn,$smConnRoot)
  {
	  //** assing the connection to the variable
	  $this->m_dbConn = $dbConn;
	  $this->m_dbConnRoot = $dbConnRoot;
	  $this->smConn = $smConn;
	  $this->smConnRoot = $smConnRoot;
	  $this->m_objUtility =  new utility($this->m_dbConn);
  }
  
  	public function cat($cat_id)
	{
		 return $result = $this->m_dbConnRoot->select("select cat from cat where cat_id='".$cat_id."'");
	}
  
  public function GetSocietyName($soc_id)
	{
		 return $result = $this->m_dbConnRoot->select("select society_name from society where society_id='".$soc_id."'");
	}
	
	 function GetStaffDetails($fromDate,$cat,$toDate)
  	 {
	   $sql='';
	  if($cat=="")
	  {
		  return $data;
	  }
	   
	  $d1=$fromDate;
	   $d2=$toDate;
	   $date1=date_create($fromDate);
	   $date2=date_create($toDate);
	   $diff=date_diff($date1,$date2);
	   $count=$diff->format("%a");
	   if($count>31)
	   {
		   return "0";
	   }
	   else
	   {
		   
	 $sql =  "Select s.`service_prd_reg_id`,sa.full_name from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where sa.society_id='".$_SESSION['society_id']."' and sa.status ='Y'  ";
	   if($cat=="All")
	   {
	   }
		else
		{
			$sql .= "  and `cat_id`='".$cat."'"; 
		}
		//echo $sql;
		$data = $this->m_dbConnRoot->select($sql);
		
	    $test=explode("-",$fromDate);
	   $date=$test[0];
	   $month=$test[1];
	   $year=$test[2];
	   $test1=explode("-",$toDate);
	   $tdate=$test1[0];
	   $month1=$test1[1];
	   $year1=$test1[2];
	   for($i = 0; $i < sizeof($data); $i++)
		  {
		  $present=0;
	   $absent=0;
		   //$count=0;
	  $Sql = "SELECT `staff_id`,`inTimeStamp`,`outTimeStamp`,`Entry_Gate`,`Exit_Gate`,`entry_image` FROM `staffattendance`  WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($fromDate)."' AND '".getDBFormatDate($toDate)."' and staff_id='".$data[$i]["service_prd_reg_id"]."'";
		  $StaffDeatils = $this->smConn->select($Sql);
		   
		   $date1=array();
		   $attend=array();
		   
		   if($date >= $tdate)	
			{
				if(($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12"))
				{
				 $test=31;
				}
	   		 	if(($month=="02")){$test=28;}
			  	if(($month == "04" || $month=="6"|| $month=="09"|| $month=="11"))
				{
				 $test=30;
				}
				for($k=$date;$k<=$test;$k++)
				{
					if($k=="01"){$k="1";}
					if($k=="02"){$k="2";}
					if($k=="03"){$k="3";}
					if($k=="04"){$k="4";}
					if($k=="05"){$k="5";}
					if($k=="06"){$k="6";}
					if($k=="07"){$k="7";}
					if($k=="08"){$k="8";}
					if($k=="09"){$k="9";}
					for($j = 0; $j < sizeof($StaffDeatils); $j++)
					{
						
					   $datetest=getDBFormatDate($StaffDeatils[$j]['inTimeStamp']);
					   $datetest1=explode("-","$datetest");
					   $date1=$datetest1[2];
					}

			   		if($k==$date1)	   
				   {
					   $attend="P";
					   $present++;
				   }
				   else
				   {
					   $attend="A";
					   $absent++;
				   }
			
		  	   $data[$i]["Attendance"][$k]=$attend;
		   		}
				for($k=1;$k<=$tdate;$k++)
				{
					for($j = 0; $j < sizeof($StaffDeatils); $j++)
					{
					   $datetest=getDBFormatDate($StaffDeatils[$i]['inTimeStamp']);
					   $datetest1=explode("-","$datetest");
					   $date1=$datetest1[2];
					}

			   		if($k==$date1)	   
				   {
					   $attend="P";
					   $present++;
				   }
				   else
				   {
					   $attend="A";
					   $absent++;
				   }
			
		  	   $data[$i]["Attendance"][$k]=$attend;
								
									
				}
							
			
						
		   //var_dump($data[$i]["Attendance"]);
		   }
		   else
		   {
		   for($k=$date;$k<=$tdate;$k++)
		   {
			   
			  if(($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12") && ($k>31))
						   {
							   $k=1;
						   }
	   		 if(($month=="02")&& ($k>28)){$k=1;}
			  if(($month == "04" || $month=="6"|| $month=="09"|| $month=="11") && ($k>30))
						   {
							   $k=1;
						   }
			   				if($k=="01"){$k="1";}
							if($k=="02"){$k="2";}
							if($k=="03"){$k="3";}
							if($k=="04"){$k="4";}
							if($k=="05"){$k="5";}
							if($k=="06"){$k="6";}
							if($k=="07"){$k="7";}
							if($k=="08"){$k="8";}
							if($k=="09"){$k="9";}
		   for($j = 0; $j < sizeof($StaffDeatils); $j++)
		  {
			  
			$datetest=getDBFormatDate($StaffDeatils[$j]['inTimeStamp']);
			   $datetest1=explode("-","$datetest");
			   		$date1=$datetest1[2];
					if($k==$datetest1[2]){
						$date1=$datetest1[2];
						break;
					}   
		  		}
			   if($k==$date1)
			   {
				   $attend="P";
				   $present++;
			   }
			   else
			   {
				   $attend="A";
				   $absent++;
			   }
			
		  	   $data[$i]["Attendance"][$k]=$attend;	 
		   }
		   }
		   //var_dump($data[$i]["Attendance"]);
		   $data[$i]["Staff_Name"]=$data[$i]["full_name"];
		   $data[$i]["start"]=$date;
		   $data[$i]["end"]=$tdate;
		   $data[$i]["count"]=$count+1;
		   $data[$i]["month"]=$month;
		   $data[$i]["present"]=$present;
		   $data[$i]["absent"]=$absent;
		   $data[$i]["fromdate"]=$d1;
		   $data[$i]["toDate"]=$d2;
		   
	   }
	 
return $data;
	   }
	     
   }

	
   	public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		
		$str = '';

		/*if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}*/
		//echo "$query";
		$all="All Categories";
		$str.="<OPTION VALUE=".' '.">";
		$str.='Select Category'."</OPTION>";
		$str.="<OPTION VALUE=".$all.">";
		$str.=$all."</OPTION>";
		$data = $this->m_dbConnRoot->select($query);
		//print_r($data);
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
	
  public function GetVisitorDetails($visitorID,$fromDate,$toDate)
  {  
  		// *** write query for visitor
	   $VIDSet = false;	
	   $Sql = "SELECT `id`,`visitor_ID`, `unit_id`, `purpose_id`,`otpGtimestamp` as inTimeStamp,`Entry_Gate`,`Exit_Gate`,`outTimeStamp`,`otpStatus`,`company` FROM `visitorentry`";
	    
		//*** If fromdate and todate set the below condition append to the query
	   if($fromDate <> '' && $fromDate <> 0 && $toDate <> '' && $toDate <> 0)
	   {
			$Sql .= "  WHERE DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($fromDate)."' AND '".getDBFormatDate($toDate)."' and visitor_ID !=0 ";
			
			if($visitorID <> 0 && $visitorID <> '')
			{
				$VIDSet = true;
				$Sql .= " AND `visitor_ID` = '".$visitorID."'"; 
			}   
	   }	
	 	
		//*** if user requested for single visitor the then below code append to query
	  if($VIDSet == false)
	  {
		  if($visitorID <> 0 && $visitorID <> '')
		  {
			$Sql .= " WHERE `visitor_ID` = '".$visitorID."'";  
		  }  
	  } 
	  $Sql .= "order by `otpGtimestamp`  desc";
	  $VisitorEntry = $this->smConn->select($Sql);
	  
	  if(sizeof($VisitorEntry) == 0)
	  {
		 $VisitorDetails =  $this->GetVDetails($visitorID);
		 
		 $VisitorEntry[0]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname']; 
		 $VisitorEntry[0]['entry_image'] = $VisitorDetails[0]['img'];
		 $VisitorEntry[0]['Contact'] = $VisitorDetails[0]['Mobile'];
		 $VisitorEntry[0]['Company'] = $VisitorDetails[0]['Company'];
		 $VisitorEntry[0]['vehicle'] = $VisitorDetails[0]['vehicle'];
		  $VisitorEntry[0]['id']=$VisitorDetails[0]['id'];
		 //$VisitorEntry[0]['visitorMobile']=$VisitorDetails[0]['visitorMobile'];
		 return $VisitorEntry;
	  }
	  
	  // ** Collecting whole data from perpose table
	  $PerposeData = $this->PerposeDetail();
	  //** array to store purposeid
	  $perposeIndex = array();
	  //** array to store perposeName
	  $perposeName = array();
	  
	  //*** Push the perposeid and purposeName to above array
	  for($i = 0 ; $i < sizeof($PerposeData); $i++)
	  {
		  array_push($perposeIndex,$PerposeData[$i]['purpose_id']);
     	  array_push($perposeName,$PerposeData[$i]['purpose_name']);
	  }
	  
	  for($i = 0; $i < sizeof($VisitorEntry); $i++)
	  {
		  $mobilestatus="";
		  $unitno="";
		  $ownername="";
		  // *** Collect visitor details
		 $VisitorDetails =  $this->GetVDetails($VisitorEntry[$i]['visitor_ID']);
		 $OwnerDetails=array();
		  //*** Collect MemberName And UnitNo.
		  $unitid=$VisitorEntry[$i]['unit_id'];
		  $unittest=explode(",",$unitid);
			 $cnt=0;
		  $cnt1=0;
		$status="";
		  foreach($unittest as $unit)
		  {
			  //var_dump($unit);
			  $approvalflagdetails=$this->GetApprovalStatus($unit,$VisitorEntry[$i]['id']);
			  $flag=$approvalflagdetails[0]['Entry_flag'];
			  //var_dump($flag);
			  if($flag=="1")
			  {
				 $status.="Approved"."/";
			  }
			  if($flag=="2")
			  {
				  $status.="Denied"."/";
			  }
			  if($flag=="3")
			  {
				  $status.="Without Approval"."/";
			  }
			  
			  if($unit>0)
			  {	 
				  $OwnerDetails = $this->m_objUtility->GetmemberDetailunit($unit);
				 $unitno .=$OwnerDetails[0]['unit_no'] ."/";
				  $ownername .=$OwnerDetails[0]['primary_owner_name'] . "/";
				  
			  }
			  else
			  {
				 $unitno .= "S-Office" ."/";
				  $ownername .= "Society Office" ."/";
			  }
		 }
		  if($VisitorEntry[$i]['otpStatus']=="valid")
		  {
			  $mobilestatus="Verified";
		  }
		  else
		  {
			  $mobilestatus="Not Verified";
		  }
		 
		   //var_dump($mobilestatus);
			 $unitno=substr($unitno,0,length-1);
		  
			 $ownername=substr($ownername,0,length-1);
		 	$status=substr($status,0,length-1);
		 // Store the Purpose Position in array
		 $PurposeNameRowIndex = array_search($VisitorEntry[$i]['purpose_id'],$perposeIndex);
		
		 $TimeDifference = $this->GetTimeDifference($VisitorEntry[$i]['inTimeStamp'],$VisitorEntry[$i]['outTimeStamp']);
		
		$inTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['inTimeStamp'])));
			//var_dump($outTimeStamp);
			if($VisitorEntry[$i]['outTimeStamp'] == "0000-00-00 00:00:00")
			{
				$outTimeStamp="0000-00-00 00:00:00";
				
			}
			else
			{
			$outTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['outTimeStamp'])));
			}
		 $VisitorEntry[$i]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname'];
		 $VisitorEntry[$i]['inTimeStamp']=$inTimeStamp;
		 $VisitorEntry[$i]['outTimeStamp']= $outTimeStamp;
		 $VisitorEntry[$i]['entry_image'] = $VisitorDetails[0]['img'];
		 $VisitorEntry[$i]['unit_no'] = $unitno;
		 $VisitorEntry[$i]['Owner_name'] = $ownername;
		  $VisitorEntry[$i]['approvalstatus']=$status;
		 $VisitorEntry[$i]['purpose'] = $perposeName[$PurposeNameRowIndex];
		 $VisitorEntry[$i]['Contact'] = $VisitorDetails[0]['Mobile'];
		 $VisitorEntry[$i]['Company'] = $VisitorEntry[$i]['company'];
		 $VisitorEntry[$i]['vehicle'] = $VisitorDetails[0]['vehicle'];
		 $VisitorEntry[$i]['TotalTime'] = $TimeDifference;
		  $VisitorEntry[$i]['motpstatus']=$mobilestatus;
		  
	  }
	  return $VisitorEntry;
  }
  public function GetVisitorPurposeDetails($visitorID,$fromDate,$toDate,$puposeid)
  {  
  		// *** write query for visitor
	   $VIDSet = false;	
	     $Sql = "SELECT `id`,`visitor_ID`, `unit_id`, `purpose_id`,`otpGtimestamp` as inTimeStamp,`Entry_Gate`,`Exit_Gate`,`outTimeStamp`,`otpStatus`,`company` FROM `visitorentry`";
	    
		//*** If fromdate and todate set the below condition append to the query
	   if($fromDate <> '' && $fromDate <> 0 && $toDate <> '' && $toDate <> 0)
	   {
			 $Sql .= "  WHERE DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($fromDate)."' AND '".getDBFormatDate($toDate)."' and visitor_ID !=0 and purpose_id='".$puposeid."' ";
			
			if($visitorID <> 0 && $visitorID <> '')
			{
				$VIDSet = true;
				$Sql .= " AND `visitor_ID` = '".$visitorID."'"; 
			}   
	   }	
	 	
		//*** if user requested for single visitor the then below code append to query
	  if($VIDSet == false)
	  {
		  if($visitorID <> 0 && $visitorID <> '')
		  {
			$Sql .= " WHERE `visitor_ID` = '".$visitorID."' and purpose_id='".$puposeid."'";  
		  }  
	  } 
	 $Sql .= "order by `otpGtimestamp`  desc";
	  $VisitorEntry = $this->smConn->select($Sql);
	  
	  if(sizeof($VisitorEntry) == 0)
	  {
		 $VisitorDetails =  $this->GetVDetails($visitorID);
		 
		 $VisitorEntry[0]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname']; 
		 $VisitorEntry[0]['entry_image'] = $VisitorDetails[0]['img'];
		 $VisitorEntry[0]['Contact'] = $VisitorDetails[0]['Mobile'];
		 $VisitorEntry[0]['Company'] = $VisitorDetails[0]['Company'];
		 $VisitorEntry[0]['vehicle'] = $VisitorDetails[0]['vehicle'];
		  $VisitorEntry[0]['id']=$VisitorDetails[0]['id'];
		 //$VisitorEntry[0]['visitorMobile']=$VisitorDetails[0]['visitorMobile'];
		 return $VisitorEntry;
	  }
	  
	  // ** Collecting whole data from perpose table
	  $PerposeData = $this->PerposeDetail();
	  //** array to store purposeid
	  $perposeIndex = array();
	  //** array to store perposeName
	  $perposeName = array();
	  
	  //*** Push the perposeid and purposeName to above array
	  for($i = 0 ; $i < sizeof($PerposeData); $i++)
	  {
		  array_push($perposeIndex,$PerposeData[$i]['purpose_id']);
     	  array_push($perposeName,$PerposeData[$i]['purpose_name']);
	  }
	  
	  for($i = 0; $i < sizeof($VisitorEntry); $i++)
	  {
		  $mobilestatus="";
		  $unitno="";
		  $ownername="";
		  // *** Collect visitor details
		 $VisitorDetails =  $this->GetVDetails($VisitorEntry[$i]['visitor_ID']);
		 $OwnerDetails=array();
		  //*** Collect MemberName And UnitNo.
		  $unitid=$VisitorEntry[$i]['unit_id'];
		  $unittest=explode(",",$unitid);
			 $cnt=0;
		  $cnt1=0;
		$status="";
		  foreach($unittest as $unit)
		  {
			  //var_dump($unit);
			  $approvalflagdetails=$this->GetApprovalStatus($unit,$VisitorEntry[$i]['id']);
			  $flag=$approvalflagdetails[0]['Entry_flag'];
			  //var_dump($flag);
			  if($flag=="1")
			  {
				 $status.="Approved"."/";
			  }
			  if($flag=="2")
			  {
				  $status.="Denied"."/";
			  }
			  if($flag=="3")
			  {
				  $status.="Without Approval"."/";
			  }
			  
			  if($unit>0)
			  {	 
				  $OwnerDetails = $this->m_objUtility->GetmemberDetailunit($unit);
				 $unitno .=$OwnerDetails[0]['unit_no'] ."/";
				  $ownername .=$OwnerDetails[0]['primary_owner_name'] . "/";
				  
			  }
			  else
			  {
				 $unitno .= "S-Office" ."/";
				  $ownername .= "Society Office" ."/";
			  }
		 }
		  if($VisitorEntry[$i]['otpStatus']=="valid")
		  {
			  $mobilestatus="Verified";
		  }
		  else
		  {
			  $mobilestatus="Not Verified";
		  }
		 
		   //var_dump($mobilestatus);
			 $unitno=substr($unitno,0,length-1);
		  
			 $ownername=substr($ownername,0,length-1);
		 	$status=substr($status,0,length-1);
		 // Store the Purpose Position in array
		 $PurposeNameRowIndex = array_search($VisitorEntry[$i]['purpose_id'],$perposeIndex);
		
		 $TimeDifference = $this->GetTimeDifference($VisitorEntry[$i]['inTimeStamp'],$VisitorEntry[$i]['outTimeStamp']);
		
		$inTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['inTimeStamp'])));
			//var_dump($outTimeStamp);
			if($VisitorEntry[$i]['outTimeStamp'] == "0000-00-00 00:00:00")
			{
				$outTimeStamp="0000-00-00 00:00:00";
				
			}
			else
			{
			$outTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['outTimeStamp'])));
			}
		 $VisitorEntry[$i]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname'];
		 $VisitorEntry[$i]['inTimeStamp']=$inTimeStamp;
		 $VisitorEntry[$i]['outTimeStamp']= $outTimeStamp;
		 $VisitorEntry[$i]['entry_image'] = $VisitorDetails[0]['img'];
		 $VisitorEntry[$i]['unit_no'] = $unitno;
		 $VisitorEntry[$i]['Owner_name'] = $ownername;
		  $VisitorEntry[$i]['approvalstatus']=$status;
		 $VisitorEntry[$i]['purpose'] = $perposeName[$PurposeNameRowIndex];
		 $VisitorEntry[$i]['Contact'] = $VisitorDetails[0]['Mobile'];
		 $VisitorEntry[$i]['Company'] = $VisitorEntry[$i]['company'];
		 $VisitorEntry[$i]['vehicle'] = $VisitorDetails[0]['vehicle'];
		 $VisitorEntry[$i]['TotalTime'] = $TimeDifference;
		  $VisitorEntry[$i]['motpstatus']=$mobilestatus;
		  
	  }
	  return $VisitorEntry;
  }	
  	
  public function GetApprovalStatus($unit,$visitor_ID)
		{
	  
	  	return $status =$this->smConn->select("SELECT Entry_flag from visit_approval where unit_id='".$unit."' and v_id='".$visitor_ID."' ");
		}

    public function GetStaffDeails($staffID,$fromDate,$toDate,$cat)
	{ 
///	echo "<br>inside staff function";
	$data1=array();
	$data2=array();
	$sql1='';
		 //** query for staff details
		  $scount=0;
		$newCnt=0;
		if($staffID== 0)
		{
			 $sql1="Select s.`service_prd_reg_id`,sa.full_name,`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where society_id='".$_SESSION['society_id']."'";
		if($cat=="All")
		{
			
		}
		else
		{
			$sql1 .= " and `cat_id`='".$cat."'";
		}
	//echo $sql;
		$data = $this->m_dbConnRoot->select($sql1);
		
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			$Sql = "SELECT `staff_id`,`inTimeStamp`,`outTimeStamp`,`Entry_Gate`,`Exit_Gate`,`entry_image` FROM `staffattendance`  WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($fromDate)."' AND '".getDBFormatDate($toDate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
		   // echo "<pre>";
			//var_dump($StaffDeatils);
			//echo "</pre>";
			//echo sizeof($StaffDeatils);
		  	if(sizeof($StaffDeatils)==0)
			  {
					$scount=$scount+1;
			}
				if($StaffDeatils == '')
				{
					continue;
				}
				//echo $scount;
			 for($i = 0; $i < sizeof($StaffDeatils); $i++)
		  	{
				//echo "inside loop".$i;
			  //*** collect service provider Names
			 $StaffName = $this->ServiceProviderDetails($StaffDeatils[$i]['staff_id']);
			//echo "<pre>";
			//print_r($StaffName); 
				$inTimeStamp=$StaffDeatils[$i]['inTimeStamp'];
				$outTimeStamp=$StaffDeatils[$i]['outTimeStamp'];
				$intime= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($inTimeStamp)));
				//var_dump($outTimeStamp);
				if($outTimeStamp == "0000-00-00 00:00:00")
				{
					$outtime="0000-00-00 00:00:00";
				
				}
				else
				{
				$outtime= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($outTimeStamp)));
				}
				$staff_id=$StaffDeatils[$i]['staff_id'];
				$entry_image=$StaffDeatils[$i]['entry_image'];
				$Entry_Gate=$StaffDeatils[$i]['Entry_Gate'];
				$Exit_Gate=$StaffDeatils[$i]['Exit_Gate'];
				//*** Working Times
				 $TimeDifference = $this->GetTimeDifference($StaffDeatils[$i]['inTimeStamp'], $StaffDeatils[$i]['outTimeStamp']);
			//}
			
			
			$data1[] = array("inTimeStamp"=>$intime,"outTimeStamp"=>$outtime,"Staff_name"=>$StaffName[0]['full_name'],"Entry_Gate"=>$Entry_Gate,"Exit_Gate"=>$Exit_Gate,"entry_image"=>$entry_image,"staff_id"=>$staff_id,"TotalTime"=>$TimeDifference ,"cat"=>$cat, "fromDate" =>$fromDate,"toDate"=>$toDate,"cat_id"=>$data[$j]['cat_id'] );
			//$data1[]['inTimeStamp']=$intime;
			//$data1[]['outTimeStamp']=$outtime;
			//var_dump($data1[$j]['outTimeStamp']);
			//$data1[]['Entry_Gate']=$Entry_Gate;
			//$data1[]['Exit_Gate']=$Exit_Gate;
			//$data1[]['entry_image']=$entry_image;
			//$data1[]['staff_id']=$staff_id;
			//$data1[]['Staff_name'] = $StaffName[0]['full_name'];
			//$data1[]['TotalTime'] = $TimeDifference;
			//$data1[]['cat']=$cat;
			//$data1[]['fromDate']=$fromDate;
			//$data1[]['toDate']=$toDate;
			  
			//$data1[]['cat_id']=$data[$j]['cat_id'];
			$newCnt++;
			//echo "<br>Cmt ::".$newCnt++;
		 }
		}
		 
		// echo sizeof($data1);
		 // var_dump($data1);
		//}
		//for($K = 0; $k sizeof($data1); $k++)
		//{
			
		//}
		//$data1 = reorder($data1);
		//var_dump($data1);
		if(sizeof($data)==$scount)
		{
		return $data2;
		}
		return $data1;
		}
		else{
			
			 $Sql = "SELECT `staff_id`,`inTimeStamp`,`outTimeStamp`,`Entry_Gate`,`Exit_Gate`,`entry_image` FROM `staffattendance` where staff_id='".$staffID."' order by `inTimeStamp` desc";
			echo $sql;
		  $StaffDeatils = $this->smConn->select($Sql);
				
		  for($i = 0; $i < sizeof($StaffDeatils); $i++)
		  {
			  //*** collect service provider Names
			 $StaffName = $this->ServiceProviderDetails($StaffDeatils[$i]['staff_id']);
			
			//*** Working Times
			 $TimeDifference = $this->GetTimeDifference($StaffDeatils[$i]['inTimeStamp'], $StaffDeatils[$i]['outTimeStamp']);
			 $StaffDeatils[$i]['inTimeStamp']= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($StaffDeatils[$i]['inTimeStamp'])));
				
			//var_dump($outTimeStamp);
			if($StaffDeatils[$i]['outTimeStamp'] == "0000-00-00 00:00:00")
			{
				$StaffDeatils[$i]['outTimeStamp']="0000-00-00 00:00:00";
				
			}
			else
			{
				$StaffDeatils[$i]['outTimeStamp']= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime( $StaffDeatils[$i]['outTimeStamp'])));
			}
			 $StaffDeatils[$i]['Staff_name'] = $StaffName[0]['full_name'];
			 $StaffDeatils[$i]['TotalTime'] = $TimeDifference;
			  
			  }
			
		  return $StaffDeatils;
	
			
		}
		
		
   }	
		public function GetSecurityRoundDetails($fromDate,$toDate)
		{
			$sql3="SELECT sm.id,sm.frequency,sm.schedule_name,TIME_FORMAT(sm.round_time, '%H %i %p') as rtime, sr.round_time, sr.create_by FROM `schedule_master` as sm  join `security_round` as sr on sr.schedule_id=sm.id  where DATE(sr.round_time) BETWEEN '".getDBFormatDate($fromDate)."' AND '".getDBFormatDate($toDate)."'";
			$securityround = $this->smConn->select($sql3);
		        return $securityround;
	    
		}
	 private function GetSDetails($staffID)
  {
	 $Sql = "SELECT `staff_id`,`inTimeStamp`,`outTimeStamp`,`Entry_Gate`,`Exit_Gate`,`entry_image` FROM `staffattendance` where staff_id='".$staffID."'";
	 return $Result = $this->smConn->select($Sql);
  } 
	
  private function GetVDetails($VisitorID)
  {
	 $Sql = "SELECT Fname, Lname, Mobile, Company, vehicle, img from visitors where visitor_id = '".$VisitorID."'";
	 return $Result = $this->smConnRoot->select($Sql);
  }
  
  private function PerposeDetail($purpose_id = 0)
  {
	  if($purpose_id == 0)
	  {
		$sql = "SELECT `purpose_id`,`purpose_name` from purpose";
	  }
	  else if($purpose_id > 0)
	  {
		  $sql = "SELECT `purpose_id`,`purpose_name` from purpose where purpose_id='".$purpose_id."'  ";
	  }
	return $result = $this->smConn->select($sql);   
  }

  public function ServiceProviderDetails($StaffID)
  {
	   $query = "Select full_name from service_prd_reg where service_prd_reg_id = '".$StaffID."'";
	   return $result = $this->m_dbConnRoot->select($query);
  }
  
  private function GetTimeDifference($InTime, $OutTime,$IsStaffTimeStamp)
  {
	  $First = strtotime($InTime);
	  $Second = strtotime($OutTime);
	  $TotalValue = $Second -$First;
	  $Min = $TotalValue/60;
	  
	  if($Min < 60 && $Min > 0)
	  {
		  return $totaltime = '00:'.(int)$Min. '  H:M';
	  }
	  
	  if($Min < 0)
	  {
		  return $totaltime = 'Wrong Entry';
	  }	
	  
      if($Min >= 60)
	  {
		 $Hour = $Min/60;
		 
	     $hourDecimal = explode('.',$Hour);
		 $RemainingMin = (($hourDecimal[1]*60)/100);
		 return $totaltime = (int)$Hour.':'.substr($RemainingMin,0,2).'  H:M';
	 }
  }
  
  public function GetVisitorByUnitID($unitID,$type)
  {
	  
	 // echo $type;
	  if($type == 'current')
	  {
		  $Sql = "SELECT v.`id`,v.`visitor_ID`, va.`unit_id`, v.`purpose_id`,v.`otpGtimestamp` as inTimeStamp,v.`Entry_Gate`,v.`Exit_Gate`,v.`outTimeStamp`,v.`otpStatus`,`company` FROM `visitorentry` as v join `visit_approval` as va on va.v_id = v.id where va.unit_id = '".$unitID."' and status ='inside' and 	outTimeStamp = '0000-00-00 00:00:00' order by v.`otpGtimestamp`  desc";
	  }
	 else if($type =='past')
	 {
		 $Sql = "SELECT v.`id`,v.`visitor_ID`, va.`unit_id`, v.`purpose_id`,v.`otpGtimestamp` as inTimeStamp,v.`Entry_Gate`,v.`Exit_Gate`,v.`outTimeStamp`,v.`otpStatus`,`company` FROM `visitorentry` as v join `visit_approval` as va on va.v_id = v.id where va.unit_id = '".$unitID."' and status ='outside' order by v.`otpGtimestamp` desc";
	 }
	/* else if($type == 'expected')
	 {
		 $expectedVisitor = $this->GetExpectedVisitor($unitID); 
	 }*/
	 $VisitorEntry = $this->smConn->select($Sql);
	// var_dump($VisitorEntry);
	 $PerposeData = $this->PerposeDetail();
	 //echo "1";
	  //** array to store purposeid
	  $perposeIndex = array();
	  //** array to store perposeName
	  $perposeName = array();
	  
	  //*** Push the perposeid and purposeName to above array
	  for($i = 0 ; $i < sizeof($PerposeData); $i++)
	  {
		 // echo "1";
		  array_push($perposeIndex,$PerposeData[$i]['purpose_id']);
     	  array_push($perposeName,$PerposeData[$i]['purpose_name']);
	  }
	  
	  for($i = 0; $i < sizeof($VisitorEntry); $i++)
	  {
		 // echo "1";
		  $mobilestatus="";
		  $unitno="";
		  $ownername="";
		 
		  // *** Collect visitor details
		 $VisitorDetails =  $this->GetVDetails($VisitorEntry[$i]['visitor_ID']);
		 $OwnerDetails=array();
		  //*** Collect MemberName And UnitNo.
		  $unitid=$VisitorEntry[$i]['unit_id'];
		  $unittest=explode(",",$unitid);
			 $cnt=0;
		  $cnt1=0;
		$status="";
		  foreach($unittest as $unit)
		  {
			 // var_dump($unit);
			  $approvalflagdetails=$this->GetApprovalStatus($unit,$VisitorEntry[$i]['id']);
			  $flag=$approvalflagdetails[0]['Entry_flag'];
			 // var_dump($flag);
			  if($flag=="1")
			  {
				 $status.="Approved"."/";
			  }
			  if($flag=="2")
			  {
				  $status.="Denied"."/";
			  }
			  if($flag=="3")
			  {
				  $status.="Without Approval"."/";
			  }
			  else if($flag=="0")
			  {
				   //echo "ID :".$VisitorEntry[$i]['id'];
				  $status.="<a href='visitorApproval.php?id=".$VisitorEntry[$i]['id']."' style='text-decoration:none;'><span style='color:red'>Waiting for Approval</span></a>";
				 
			  }
			  
			  if($unit>0)
			  {	 
				  $OwnerDetails = $this->m_objUtility->GetmemberDetailunit($unit);
				 $unitno .=$OwnerDetails[0]['unit_no'] ."/";
				  $ownername .=$OwnerDetails[0]['primary_owner_name'] . "/";
				  
			  }
			  else
			  {
				 $unitno .= "S-Office" ."/";
				  $ownername .= "Society Office" ."/";
			  }
		 }
		  if($VisitorEntry[$i]['otpStatus']=="valid")
		  {
			  $mobilestatus="Verified";
		  }
		  else
		  {
			  $mobilestatus="Not Verified";
		  }
		 
		   //var_dump($mobilestatus);
			 $unitno=substr($unitno,0,length-1);
		  
			 $ownername=substr($ownername,0,length-1);
		 	$status=substr($status,0,length-1);
		 // Store the Purpose Position in array
		 $PurposeNameRowIndex = array_search($VisitorEntry[$i]['purpose_id'],$perposeIndex);
		
		 $TimeDifference = $this->GetTimeDifference($VisitorEntry[$i]['inTimeStamp'],$VisitorEntry[$i]['outTimeStamp']);
		
		$inTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['inTimeStamp'])));
			//var_dump($outTimeStamp);
			if($VisitorEntry[$i]['outTimeStamp'] == "0000-00-00 00:00:00")
			{
				$outTimeStamp="0000-00-00 00:00:00";
				
			}
			else
			{
			$outTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['outTimeStamp'])));
			}
		 	$VisitorEntry[$i]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname'];
		 	$VisitorEntry[$i]['inTimeStamp']=$inTimeStamp;
		 	$VisitorEntry[$i]['outTimeStamp']= $outTimeStamp;
		 	$VisitorEntry[$i]['entry_image'] = $VisitorDetails[0]['img'];
		 	$VisitorEntry[$i]['unit_no'] = $unitno;
		 	$VisitorEntry[$i]['Owner_name'] = $ownername;
		  	$VisitorEntry[$i]['approvalstatus']=$status;
		 	$VisitorEntry[$i]['purpose'] = $perposeName[$PurposeNameRowIndex];
		 	$VisitorEntry[$i]['Contact'] = $VisitorDetails[0]['Mobile'];
		 	$VisitorEntry[$i]['Company'] = $VisitorEntry[$i]['company'];
		 	$VisitorEntry[$i]['vehicle'] = $VisitorDetails[0]['vehicle'];
		 	$VisitorEntry[$i]['TotalTime'] = $TimeDifference;
		  	$VisitorEntry[$i]['motpstatus']=$mobilestatus;
		  
	  }
	 // var_dump($VisitorEntry);
	  return $VisitorEntry;
  
 
}
public function GetExpectedVisitor($unitID,$type)
{
	$today=  date("Y-m-d");
	$selectQuery = "SELECT CONCAT(ex.`fname`,ex.`lname`) as VisistorName, ex.`mobile`,ex.`expected_date`,ex.`note`,p.`purpose_name` FROM `expected_visitor` as ex join `purpose` as p on p.`purpose_id`=ex.purpose_id where ex.unit = '".$unitID."' and expected_date >='".$today."'";
	
   $ExpEntry = $this->smConn->select($selectQuery);
   $finalArray= array();
   if($ExpEntry <> '')
   {
	 for($i = 0; $i< sizeof($ExpEntry);$i++)
	 { 
		$selectViEntry = "Select * from `visitorentry` where visitorMobile = '".$ExpEntry[$i]['mobile']."' and status= 'inside'";   
	 	$VisitorIn = $this->smConn->select($selectViEntry);
	 
	 	if( $VisitorIn <> '')
	 	{
			 //$ExpectedVisitor = '';
	 	}
	 	else
   		{
			array_push($finalArray,$ExpEntry[$i]);
			// $ExpectedVisitor = $ExpEntry[$i];
		}
	 }
   }
  // var_dump($finalArray);
   return $finalArray;
}

public function combobox1($query,$id)
	{
//echo "call";
	$str.="<option value=''>Please Select</option>";
	//$data = $this->m_dbConn->select($query);
	$data =$this->smConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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

public function startProcess()
{
// function AddExpectedVistor()
	$errorExists=0;
	if($_POST['insert']=='Submit')
	{
		$ExpTime = "00:00";
	//var_dump($_POST);
	$insertQuery = "insert into `expected_visitor` (`fname`,`lname`,`mobile`,`expected_date`,`expected_time`,`unit`,`purpose_id`,`note`) value('".$_POST['firstName']."','".$_POST['LastName']."','".$_POST['contact']."','".getDBFormatDate($_POST['ExpDate'])."','".$ExpTime."','".$_SESSION['unit_id']."','".$_POST['purpose']."','".$_POST['note']."')";
	 
	 $Insert = $this->smConn->insert($insertQuery);
	
	$this->actionPage='../MyVisitor.php?type=expected' ;
	 return $Insert;
	
	   
 } 
 else if($_POST['insert']=='Approve')
 {
	$updateQUery = "Update `visit_approval` set Entry_flag='1' ,login_id = '".$_SESSION['login_id']."',	login_name='".$_SESSION['name']."',approvewith='W2S' ,approvemsg='".$_POST['note']."' where v_id='".$_POST['visitorId']."' "; 
	$this->actionPage='../MyVisitor.php?type=current' ;
	 $Approved = $this->smConn->update($updateQUery);
 }
  else if($_POST['insert']=='Denite')
 {
	$updateQUery = "Update `visit_approval` set Entry_flag='2' ,login_id = '".$_SESSION['login_id']."',	login_name='".$_SESSION['name']."',approvewith='W2S' ,approvemsg='".$_POST['note']."' where v_id='".$_POST['visitorId']."' "; 
	$this->actionPage='../MyVisitor.php?type=current' ;
	 $Approved = $this->smConn->update($updateQUery);
 }
}

public function GetIncommingVisitorForApproval($id)
{
  $selectVisitor ="SELECT v.`id`,v.`visitor_ID`, va.`unit_id`, v.`purpose_id`,v.`otpGtimestamp` as inTimeStamp,v.`Entry_Gate`,v.`Exit_Gate`,v.`outTimeStamp`,v.`otpStatus`,`company` FROM `visitorentry` as v join `visit_approval` as va on va.v_id = v.id where v.id = '".$id."'";	
  $Visitors = $this->smConn->select($selectVisitor);
   $PerposeData = $this->PerposeDetail($Visitors[0]['purpose_id']);
	$perposeIndex = array();
	$perposeName = array();
	 //*** Push the perposeid and purposeName to above array
	   $VisitorDetails =  $this->GetVDetails($Visitors[0]['visitor_ID']);
	  // var_dump($VisitorDetails);
	   $TimeDifference = $this->GetTimeDifference($VisitorEntry[0]['inTimeStamp'],$Visitors[0]['outTimeStamp']);
		
		$inTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($Visitors[0]['inTimeStamp'])));
			//var_dump($outTimeStamp);
			if($Visitors[0]['outTimeStamp'] == "0000-00-00 00:00:00")
			{
				$outTimeStamp="0000-00-00 00:00:00";
				
			}
			else
			{
			$outTimeStamp= date('Y-m-d H:i',strtotime('+5 hour +30 minutes',strtotime($VisitorEntry[$i]['outTimeStamp'])));
			}
		 	$Visitors[0]['VName'] = $VisitorDetails[0]['Fname'].' '.$VisitorDetails[0]['Lname'];
		 	$Visitors[0]['inTimeStamp']=$inTimeStamp;
		 	$Visitors[0]['outTimeStamp']= $outTimeStamp;
		 	$Visitors[0]['entry_image'] = $VisitorDetails[0]['img'];
		 	$Visitors[0]['unit_no'] = $unitno;
		 	$Visitors[0]['Owner_name'] = $ownername;
		  	$Visitors[0]['approvalstatus']=$status;
		 	$Visitors[0]['purpose'] = $PerposeData[0]['purpose_name'];
		 	$Visitors[0]['Contact'] = $VisitorDetails[0]['Mobile'];
		 	$Visitors[0]['Company'] = $VisitorEntry[0]['company'];
		 	$Visitors[0]['vehicle'] = $VisitorDetails[0]['vehicle'];
		 	$Visitors[0]['TotalTime'] = $TimeDifference;
		  	$Visitors[0]['motpstatus']=$mobilestatus;
			//var_dump($Visitors);
			return $Visitors;
}
public function GetApprovalMsg()
{
 $selectMag = "select * from approvemsg";
 $Appmsg = $this->smConn->select($selectMag);	
 return $Appmsg;
}
} 
?> 