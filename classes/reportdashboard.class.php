<?php 
include_once("utility.class.php");
class Report_Dash 
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
  
  public function TotalFlat()
  {
	  $sql="SELECT count(distinct(unit_id)) as unit_id FROM `unit`";
	  $retData = $this->m_dbConn->select($sql);
		
	  return $retData[0]['unit_id'];
	  //return $retData;
	  
  }
  
  public function StaffselCategory()
  {
	  $NArray=array();
		$sql1="SELECT * FROM `cat` where status='Y'";
		//echo "<BR>Test1.1";
	  	$getData=$this->m_dbConnRoot->select($sql1);
		for($i=0;$i<sizeof($getData);$i++)
		{
			$cat_id=$getData[$i]['cat_id'];
			$getCountCat=$this->getStaffselCount($cat_id);
			array_push($NArray,array('cat' => $getData[$i]['cat'] ,'cat_id'=>$getData[$i]['cat_id'],'cat_count' => $getCountCat[0]['service_prd_reg_count'] ));
		}
		
		//$getCountCat=$this->
		return $NArray;
  }
  public function getStaffselCount($catID)
  {
	  $sqlcount="Select count(distinct(s.service_prd_reg_id)) as service_prd_reg_count from spr_cat as s inner join `service_prd_reg` as sa on s.service_prd_reg_id =sa.service_prd_reg_id where sa.society_id='".$_SESSION['society_id']."' and s.cat_id='".$catID."' and s.status='Y'";
	  $getCountData=$this->m_dbConnRoot->select($sqlcount);
	  return $getCountData;
  }
  
  //*****************************************New code******************************************************//
   public function StaffCategory($start_date,$end_date,$statuscheck)
  {
	  $NArray=array();
		$sql1="SELECT * FROM `cat` where status='Y'";
	  	$getData=$this->m_dbConnRoot->select($sql1);
		//var_dump($getData);
		for($i=0;$i<sizeof($getData);$i++)
		{
			$cat_id=$getData[$i]['cat_id'];
			$getCountCat=$this->getStaffCount($start_date,$end_date,$cat_id,$statuscheck);
			//var_dump($getCountCat);
			if($getCountCat[0]['TotStaff']<>0){
			$outstaf = $getCountCat[0]['TotStaff'];
			//echo "<BR> outstaff: ".$outstaf;
			if($statuscheck == 1 || $statuscheck == 3 || $statuscheck == 4) $outstaf = 0;
			array_push($NArray,array('cat' => $getData[$i]['cat'] ,'cat_id'=>$getData[$i]['cat_id'],'cat_incount' => $getCountCat[0]['Instaff'],'cat_outstaff'=> $outstaf));
			}
		}
		//$getCountCat=$this->
		//var_dump($NArray);
		return $NArray;
  }
  public function getStaffCount($start_date,$end_date,$catID,$statuscheck)
  {
	 /* $sqlcount="Select count(distinct(s.service_prd_reg_id)) as service_prd_reg_count from spr_cat as s inner join `service_prd_reg` as sa on s.service_prd_reg_id =sa.service_prd_reg_id where sa.society_id='".$_SESSION['society_id']."' and s.cat_id='".$catID."' and s.status='Y'";
	  $getCountData=$this->m_dbConnRoot->select($sqlcount);
	  return $getCountData;*/
	  $carray=array();
	  $scountIn=0;
	  $scounttot =0;
	  $scountOut=0;
		 $sqlcat="Select s.`service_prd_reg_id`, s.`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where sa.society_id='".$_SESSION['society_id']."' and s.cat_id='".$catID."'
";
//echo "<br>".$sqlcat
	  $data = $this->m_dbConnRoot->select($sqlcat);
	  //echo "data size: ";
	  //var_dump(sizeof($data));
		for($j = 0; $j < sizeof($data); $j++)
		  {
			  	if($statuscheck == 0 || $statuscheck == 1 ){
					//in out between dates
				  $SqlIn = "select 1 as allstaff from staffattendance where DATE(inTimeStamp) BETWEEN '".getDBFormatDate($start_date)."' AND '".getDBFormatDate($end_date)."' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
			 $SqlOut = "select 1 as pstaff from staffattendance where DATE(inTimeStamp) BETWEEN '".getDBFormatDate($start_date)."' AND '".getDBFormatDate($end_date)."' and DATE(outTimeStamp)<>'000-00-00' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
				  
				  }
				else if($statuscheck == 2 || $statuscheck == 3){
					//total in out
					$SqlIn = "select 1 as allstaff from staffattendance where status = 'inside' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
			 $SqlOut = "select 1 as pstaff from staffattendance where status = 'outside' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
					
					}
					//total in+ today entries
				else if ($statuscheck == 4)
				{
					$SqltotalIn = "select 1 as allstaff from staffattendance where DATE(inTimeStamp) NOT BETWEEN '".getDBFormatDate($start_date)."' AND '".getDBFormatDate($end_date)."' and status = 'inside' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
			 $SqlOut = "select 1 as pstaff from staffattendance where DATE(inTimeStamp) NOT BETWEEN '".getDBFormatDate($start_date)."' AND '".getDBFormatDate($end_date)."' and status = 'outside' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
			 $SqlIn = "select 1 as allstaff from staffattendance where DATE(inTimeStamp) BETWEEN '".getDBFormatDate($start_date)."' AND '".getDBFormatDate($end_date)."' and staff_id='".$data[$j]["service_prd_reg_id"]."' limit 1";
			 		
					
				}
			   
			//echo "<br>Outside:".
		  	
			if($statuscheck == 4)
			{
				$TotalStaffInDeatils = $this->smConn->select($SqltotalIn);
				$counttotalstaffIn=$TotalStaffInDeatils[0]['allstaff'];
				$scounttot = $scounttot + $counttotalstaffIn;
				//var_dump($scountIn);
			}
			 //echo "hello";
			 //echo $countstaffIn=$StaffInDeatils[0]['allstaff'];
			$StaffInDeatils = $this->smConn->select($SqlIn);
			$countstaffIn=$StaffInDeatils[0]['allstaff'];
			$scountIn +=$countstaffIn;
			//var_dump($scountIn);
			$StaffOutDeatils = $this->smConn->select($SqlOut);
			$countstaffOut=$StaffOutDeatils[0]['pstaff'];
			$scountOut = $scountOut + $countstaffOut;
			 
		  }
		  //echo "<BR> scount: ";
		  //var_dump($scountIn);
		  if($statuscheck == 4)
			{
					$scountIn = $scountIn + $scounttot;
			}
	  	array_push($carray,array("Instaff"=>$scountIn,"TotStaff"=>$scountOut));
	  //var_dump($carray);
	    return $carray;
  }
  
  public function getVisitorCount($date)
  {
	  $sqlVcount="SELECT count(*) as total FROM `visitorentry` where otpGtimestamp like '".$date."%' and purpose_id<>0";
	  $VisitorEntryCount = $this->smConn->select($sqlVcount);
	  return $VisitorEntryCount;
	  
  }
  public function getAllPurpose(){
	$purpose="select * from purpose order by purpose_id";
	$AllPurpose = $this->smConn->select($purpose);
	return $AllPurpose;
  }
  public function getAllPurposeCount($pid,$startdate,$enddate,$status)
  {
	  	if($status ==0){
		  $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and unit_id not in(-1) and visitor_ID NOT IN(0)";
	    }
		else if($status ==1){
			
		  $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and unit_id not in(-1) and visitor_ID NOT IN(0) and status = 'inside'";
	    }
		else if($status ==2){
			
		  $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and unit_id not in(-1) and visitor_ID NOT IN(0) and status = 'outside'";
	    }
		else if($status ==3){
			
		  $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and unit_id not in(-1) and visitor_ID NOT IN(0) and status = 'inside'";
	    }
		else if($status ==4){
			
		  $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and unit_id not in(-1) and visitor_ID NOT IN(0) and status = 'outside'";
	    }
		else if($status ==5){
		 $sqlVPcount="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and DATE(otpGtimestamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and unit_id not in(-1) and visitor_ID NOT IN(0)";
		 $sqlVPcountinside="select count(*) as ptotal from `visitorentry` where purpose_id='".$pid."' and DATE(otpGtimestamp) NOT BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and unit_id not in(-1) and visitor_ID NOT IN(0) and status = 'inside'";
		 $Allcountinside=$this->smConn->select($sqlVPcountinside);
	    }
	  $AllpurposeCount=$this->smConn->select($sqlVPcount);
	  
	  if($status==5)
	  	{
			  $AllpurposeCount[0]['ptotal'] = $Allcountinside[0]['ptotal'] + $AllpurposeCount[0]['ptotal'];
		}
	  //var_dump($AllpurposeCount[0]['ptotal']);
	  
	  return $AllpurposeCount;
  }
  public function getVisitors($startdate,$enddate,$status)
  	{
		  $Narray=array();
		  if($status == 0){
			  $sqlVisitors="SELECT p.purpose_name,v.visitor_ID,substring(v.otpGtimestamp,1,10) as indate FROM `visitorentry` as v JOIN `purpose` as p on p.purpose_id = v.purpose_id where v.visitor_ID NOT IN(0) and v.unit_id NOT IN(-1) and v.status = 'inside' and otpGtimestamp between '".$startdate."' and '".$enddate."' order by indate desc";
			  }
		  else if($status == 1){
			  $sqlVisitors="SELECT p.purpose_name,v.visitor_ID,substring(v.otpGtimestamp,1,10) as indate FROM `visitorentry` as v JOIN `purpose` as p on p.purpose_id = v.purpose_id where v.visitor_ID NOT IN(0) and v.unit_id NOT IN(-1) and v.status = 'inside' order by indate desc";
			  }
		  $visitorin=$this->smConn->select($sqlVisitors);
		  for($i=0;$i<sizeof($visitorin);$i++)
		  {
				  $purpose_name=$visitorin[$i]['purpose_name'];
				  $v_id=$visitorin[$i]['visitor_ID'];
				  $indate = $visitorin[$i]['indate'];
				  $sqlname = "SELECT CONCAT(Fname,' ',Lname) as FullName FROM `visitors` where visitor_ID = '".$v_id."'";
				  $visitor_name=$this->smConnRoot->select($sqlname);
				  array_push($Narray,array("visitor_name"=>$visitor_name[0]['FullName'],"purpose_name"=>$purpose_name,"indate"=>$indate));
			}
		 return $Narray; 
	}
   public function getAllPurposeCard($todaydate){
	    $Narray=array();
	$purpose="select * from purpose order by purpose_id";
	$AllPurpose = $this->smConn->select($purpose);
	
	for($i=0;$i<sizeof($AllPurpose);$i++)
		{
			$purpose_id=$AllPurpose[$i]['purpose_id'];
			$purpose_name=$AllPurpose[$i]['purpose_name'];
			$getCountPurpose=$this->gettodayAllPurposeCount($todaydate,$purpose_id);
			
			//if($getCountCat[0]['allstaff']<>0){
			//array_push($NArray,array('cat' => $getData[$i]['cat'] ,'cat_id'=>$getData[$i]['cat_id'],'cat_count' => $getCountCat[0]['allstaff'] ));
			if($getCountPurpose[0]['Ctotpeople']<>0){
			  array_push($Narray,array("purposeid"=>$purpose_id,"purpose_name"=>$purpose_name,"CpeopleInside"=>$getCountPurpose[0]['CpeopleInside'],"Ctotpeople"=>$getCountPurpose[0]['Ctotpeople']));
			}
	}
	return $Narray;
  }
  
  public function gettodayAllPurposeCount($todaydate,$purpose_id)
  {
	  $Marray=array();
	 //echo"<br>".$purpose="select * from purpose order by purpose_id";
	 // $AllPurpose = $this->smConn->select($purpose);
	//for($i=0;$i<sizeof($AllPurpose);$i++){
		//echo "size==========>".$Allpurpose[$i]['purpose_name'];
	   $sqlVPcountIns="select count(*) as ptotal from `visitorentry` where purpose_id='".$purpose_id."' and DATE(otpGtimestamp) = '".getDBFormatDate($todaydate)."' and DATE(outTimeStamp) ='0000-00-00'";
	  $AllpurposeCountIns=$this->smConn->select($sqlVPcountIns);
	  $sqlVPcountOut="select count(*) as ototal from `visitorentry` where purpose_id='".$purpose_id."' and DATE(otpGtimestamp) = '".getDBFormatDate($todaydate)."' and DATE(outTimeStamp) <>'0000-00-00'";
	  $AllpurposeCountOut=$this->smConn->select($sqlVPcountOut);
	 $totalCount=$AllpurposeCountIns[0]['ptotal']+$AllpurposeCountOut[0]['ototal'];
	  array_push($Marray,array("CpeopleInside"=>$AllpurposeCountIns[0]['ptotal'],"Ctotpeople"=>$totalCount));
	//}
	  return $Marray;
  }
  public function StaffCountdetails($date,$catval,$staffID)
  {
	  $carray=array();
	  if($staffID==0){
	  	if($catval=='all')
	  	{
			$scount=0;
		   "<br>".$sqlcat="Select s.`service_prd_reg_id`,`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where society_id='".$_SESSION['society_id']."'";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			  "<br>".$Sql = "select count(*) as allstaff from staffattendance where DATE(inTimeStamp) = '".$date."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
			  "<br>".$countstaff=$StaffDeatils[0]['allstaff'];
			$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
		  }
		 //echo "<br>scount:"+$scount;
		 array_push($carray,array("allstaff"=>$scount));
		  
		  //return $scount;
		  
	  	}	  	
	
	  	else
	  	{
			
			$scount=0;
		   "<br>".$sqlcat="Select s.`service_prd_reg_id`,`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where society_id='".$_SESSION['society_id']."' and cat_id='".$catval."'
";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			  "<br>".$Sql = "select count(*) as allstaff from staffattendance where DATE(inTimeStamp) = '".$date."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
			  "<br>".$countstaff=$StaffDeatils[0]['allstaff'];
			$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
		  }
		 //echo "<br>scount:"+$scount;
		 array_push($carray,array("allstaff"=>$scount));
		  
		  //return $scount;
		  
	  	}
	  }
	  else
	  {
		  $Sql = "select count(*) as allstaff from staffattendance where DATE(inTimeStamp) = '".$date."' and staff_id='".$staffID."'";
			//echo $sql;
		  $StaffDeatils = $this->smConn->select($Sql);
		  array_push($carray,array("allstaff"=>$StaffDeatils[0]['allstaff']));
	  }
	  return $carray;
	  
  }
  public function StaffCountattdetails($startdate,$enddate,$catval,$staffID)
  {
	  $karray=array();
	  if($staffID==0){
	  	if($catval=='all')
	  	{
			$scount=0;
		    "<br>".$sqlcat="Select s.`service_prd_reg_id`,`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where society_id='".$_SESSION['society_id']."'";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			 "<br>".$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
			 //$countstaff=$StaffDeatils[0]['allstaff'];
			//$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
			 $staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
			  "<br>staff_countInit " . $StaffDeatils[0]['staffcount'] . "    " . $staff_name;
			 if($staff_count>0)
			 {
			  
					array_push($karray,array("staffname"=>$staff_name,"scount"=>$staff_count));
			 }
		  }
		}
		else
		{
			//$scount=0;
		  $sqlcat="Select s.`service_prd_reg_id`,`cat_id` from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id where society_id='".$_SESSION['society_id']."' and `cat_id`='".$catval."'";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
			 //$countstaff=$StaffDeatils[0]['allstaff'];
			//$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
			 $staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
			 $staff_count=$StaffDeatils[0]['staffcount'];
			array_push($karray,array("staffname"=>$staff_name,"scount"=>$staff_count));
		  }
		}
		
	}
	 else
	  {
		  $Sql = "select count(*) as allstaff from staffattendance where DATE(inTimeStamp) = '".$date."' and staff_id='".$staffID."'";
			//echo $sql;
		  $StaffDeatils = $this->smConn->select($Sql);
		   $staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
			 $staff_count=$StaffDeatils[0]['staffcount'];
			array_push($karray,array("staffname"=>$staff_name,"scount"=>$staff_count));
	  }
	 
	return $karray;
  }
  
  public function StaffDetails($StaffID)
  {
	   $query = "Select full_name from service_prd_reg where service_prd_reg_id = '".$StaffID."'";
	   $result = $this->m_dbConnRoot->select($query);
	   return $result[0]['full_name'];
  }
  
  
  	public function getStaffDetails_del($toDate, $fromDate)
	{
		$returnArray  = array();
		$arpTemp = array("label"=> $staffcat[$i]['cat'],"y"=> $staffcat[$i]['cat_incount']+$staffcat[$i]['cat_outstaff']);
								//$arpTemp = array( "y"=> $getpcount[0]['ptotal'],"label"=> $Allpurpose[$i]['purpose_name']);
		$arpTemp = array("label"=> "Security Guard","y"=> "Ghanashyam");
		array_push($returnArray, $arpTemp);
		$arpTemp = array("label"=> "Security Guard","y"=> "Vikram");
		array_push($returnArray, $arpTemp);
		$arpTemp = array("label"=> "Security Guard","y"=> "Raju");
		array_push($returnArray, $arpTemp);
		$arpTemp = array("label"=> "Maid","y"=> "Shanta bai");
		array_push($returnArray, $arpTemp);
		$arpTemp = array("label"=> "Electrician","y"=> "Arjun");
		//var_dump($returnArray);
		return $returnArray;		
	}

 public function StaffAttDetails($startdate,$enddate,$catval,$staffID,$statuscheck)
  {
	  //statuscheck = 0,1,2
	  $karray=array();
	  if($staffID==0){
	  	if($catval=='all')
	  	{
			//echo "hello1";
			$scount=0;
		    $sqlcat="select s.`service_prd_reg_id`,s.`cat_id`,c.`cat` as catname from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id join cat c on c.cat_id=s.cat_id where society_id='".$_SESSION['society_id']."'";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
		if($statuscheck == 0) //entry and inside between start and end dates
		{
			$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."' and DATE(outTimeStamp) = '0000-00-00'";
		}
		else if($statuscheck == 1)//entries exited between start and end dates
		{
			$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."' and DATE(outTimeStamp) <> '0000-00-00'";
			
			}
		else if($statuscheck == 2) //total entries between start and end dates
		{
			$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate		)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
		}
		else if($statuscheck == 3) // total inside
		{
			//echo "in";
			$Sql = "select count(*) as staffcount FROM staffattendance WHERE status = 'inside' AND staff_id='".$data[$j]["service_prd_reg_id"]."'";
		}
		else if($statuscheck == 4) // total outside
		{
			//echo "in";
			$Sql = "select count(*) as staffcount FROM staffattendance WHERE status = 'outside' AND staff_id='".$data[$j]["service_prd_reg_id"]."'";
		}
			 
			
			//echo "out";
		  	$StaffDeatils = $this->smConn->select($Sql);
			 //$countstaff=$StaffDeatils[0]['allstaff'];
			//$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
			 $staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
			  $staff_count=$StaffDeatils[0]['staffcount'];
			 if($staff_count >0)
			 {
				 //echo $staff_name;
			array_push($karray,array("staffname"=>$staff_name,"cat_name"=>$data[$j]["catname"],"scount"=>$staff_count));
		  }
		  }
		}
		else
		{
			//$scount=0;
		  $sqlcat="Select s.`service_prd_reg_id`,s.`cat_id`,c.`cat` as catname from service_prd_reg as sa inner join `spr_cat` as s on sa.service_prd_reg_id =s.service_prd_reg_id join cat c on c.cat_id=s.cat_id where society_id='".$_SESSION['society_id']."' and `cat_id`='".$catval."'";
		$data = $this->m_dbConnRoot->select($sqlcat);
		for($j = 0; $j < sizeof($data); $j++)
		  {
			 
		//var_dump($data[$j]["service_prd_reg_id"]);
			$Sql = "select count(*) as staffcount from staffattendance WHERE DATE(inTimeStamp) BETWEEN '".getDBFormatDate($startdate)."' AND '".getDBFormatDate($enddate)."' and staff_id='".$data[$j]["service_prd_reg_id"]."'";
			
		  	$StaffDeatils = $this->smConn->select($Sql);
			 //$countstaff=$StaffDeatils[0]['allstaff'];
			//$scount +=$countstaff;
			//echo "<br>scount:"+$scount;
			 $staff_count=$StaffDeatils[0]['staffcount'];
			 echo "$staff_count  2: " . $staff_count ; 
			 if($staff_count > 0)
			 {
				$cat_name=$data[$j]["catname"];
				$staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
				array_push($karray,array("staffname"=>$staff_name,"cat_name"=>$cat_name,"scount"=>$staff_count));
			 }
		  }
		}
		
	}
	 else
	  {
		  $Sql = "select count(*) as allstaff from staffattendance where DATE(inTimeStamp) = '".$date."' and staff_id='".$staffID."'";
			//echo $sql;
		  $StaffDeatils = $this->smConn->select($Sql);
		   $staff_name=$this->StaffDetails($data[$j]["service_prd_reg_id"]);
			 $staff_count=$StaffDeatils[0]['staffcount'];
			 echo "staff_count 3 : " . $staff_count ; 
			 if($staff_count > 0)
			 {
				array_push($karray,array("staffname"=>$staff_name,"cat_name"=>$data[$j]["catname"],"scount"=>$staff_count));
			 }
	  }
	 
	return $karray;
  }


  public function getCatName($catval)
  {
	  //echo "======================>".$catval;
	  $farray=array();
	  if($catval=="all")
	  {
		  array_push($farray,array("cat_name"=>"ALL Categories"));
	  }
	  else
	  {
		   $query = "Select cat from cat where cat_id ='".$catval."'";
	   	  $result = $this->m_dbConnRoot->select($query);
		  array_push($farray,array("cat_name"=>$result[0]['cat']));
	  }
	  return $farray;
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
		//$str.="<OPTION VALUE=".' '.">";
		//$str.='Select Category'."</OPTION>";
		$str.="<OPTION VALUE=all>";
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
  
}
