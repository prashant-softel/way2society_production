
<?php
include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("../ImageManipulator.php");
include_once( "include/fetch_data.php");
include_once('../swift/swift_required.php');
include_once("android.class.php");
include_once("email.class.php");
//$obj_utility = new utility();


class classified 
{
	public $actionPage = "../my_listing_classified.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $table='<table>';
	public $table1='<table>';
	public $errormsg;
	public $errormsg1;
	public $display_pg;
	public $obj_utility;
	//public $obj_getClassified;
	function __construct($dbConnRoot,$dbConn)
	{
		$this->display_pg=new display_table();
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		//$this->obj_getClassified=new classified($this->m_dbConnRoot);
		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
		$return_value="";
//print_r($_REQUEST);
		if($_POST['insert']=='Submit' && $errorExists==0)
		{   //echo "hello";
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
	
        $manipulator->save('../ads/' . $random_name.'.'.$fileExt);
		
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
	}
		//print_r($image_list);
		 $image_collection = implode(',', $image_list);
		if($_POST['varified'] == 1)
		{
			$status ='Y';
		}
		else
		{
			$status ='N';
		}	
			
		$insert_query="insert into classified (`ad_title`,`login_id`,`society_id`,`location`,`email`,`phone`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_id`,`img`,`status`) values ('".$_POST['ad_title']."','".$_SESSION['login_id']."','".$_SESSION['society_id']."','".$_POST['location']."','".$_POST['email']."','".$_POST['phone']."','".$_POST['desp']."','".date('Y-m-d')."','".getDBFormatDate($_POST['act_date'])."','".getDBFormatDate($_POST['exp_date'])."','".$_POST['cat_id']."','".$image_collection."', '".$status."')";
			//echo $insert_query;	
			
			$data = $this->m_dbConnRoot->insert($insert_query);
			//echo $data ;
			if($_POST['varified'] == 1)
			{
			//when create and approved by admin	
			$this->SendClassifiedEmail($data, $_POST['ad_title'],$_POST['location'],$_POST['phone'],$_POST['email'],$_POST['act_date'],$_POST['exp_date'],$_POST['desp'],$_POST['cat_id'], false);
			
			//Mobile Notification Go when Classfied approve
			
			$this->sendClassfiedMobileNotification($data, $_POST['ad_title'], false, 0,true);
			
				if($_POST['ClassifiedSMS'] == 1)
				{
					$this->ClassifiedSms($_POST['ad_title'], true);
				}
			}
			else
			{
				$this->sendClassfiedMobileNotification($data, $_POST['ad_title'], false, 0,false);
				//Email to send for approval
				$this->SendClassifiedEmail($data, $_POST['ad_title'],$_POST['location'],$_POST['phone'],$_POST['email'],$_POST['act_date'],$_POST['exp_date'],$_POST['desp'],$_POST['cat_id'], true);
				
				//This is for only send for approval
				$this->ClassifiedSms($_POST['ad_title'], false);
				
			}
			$return_value= "Insert";
		}
		
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{   
		//print_r($_REQUEST);
		$id=$_REQUEST['id'];
		//echo $id;
		$active=$_POST['varified'];
		//echo "acive value=".$active;
		//die();
		$image_list=array(); 
		$select = "select `img` FROM `classified` WHERE id='".$id."'";
	  			$res2 =$this->m_dbConnRoot->select($select);
				//print_r($res2);
				 $image=$res2[0]['img'];
				if($image <> "")
				{
					$image_list = explode(',', $image);
				}
		//	echo"hi";
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
	
        $manipulator->save('../ads/' . $random_name.'.'.$fileExt);
		array_push($image_list,$random_name.'.'.$fileExt);
			}
		}
	}
	
			
		$image_collection = implode(',', $image_list);
		 if($active==1)
		 {
			 $up_query="update classified set `ad_title`='".$_POST['ad_title']."',`location`='".$_POST['location']."',`phone`='".$_POST['phone']."',`email`='".$_POST['email']."',`act_date`='".getDBFormatDate($_POST['act_date'])."',`exp_date`='".getDBFormatDate($_POST['exp_date'])."', `cat_id`='".$_POST['cat_id']."',`desp`='".$_POST['desp']."',`img`='".$image_collection."',`status`='Y' where id='".$id."'";
		 }
		 else{
			 $up_query="update classified set `ad_title`='".$_POST['ad_title']."',`location`='".$_POST['location']."',`phone`='".$_POST['phone']."',`email`='".$_POST['email']."',`act_date`='".getDBFormatDate($_POST['act_date'])."',`exp_date`='".getDBFormatDate($_POST['exp_date'])."', `cat_id`='".$_POST['cat_id']."',`desp`='".$_POST['desp']."',`img`='".$image_collection."',`status`='N' where id='".$id."'";
			 
			 }
			$data = $this->m_dbConnRoot->insert($up_query);
			//echo $data;
			//die();
			$return_value="Update";
		}
	
	$this->table .='</table>';
	$this->table .='</table>';
	$this->errormsg = $this->table;
	$this->errormsg1 = $this->table1;
	

	if($active == 1)
	{
		$this->SendClassifiedEmail($id, $_POST['ad_title'],$_POST['location'],$_POST['phone'],$_POST['email'],$_POST['act_date'],$_POST['exp_date'],$_POST['desp'],$_POST['cat_id']);
		
		$this->sendClassfiedMobileNotification($id, $_POST['ad_title'], false, 0,true);
		
		if($_POST['ClassifiedSMS'] == 1)
				{
					$this->ClassifiedSms($_POST['ad_title'], true);
				}
	}
	if($error <> '')
	{
		
	return $error;	
	}
		
	return $return_value;
	
	}				


	public function combobox($query, $id)
	{
		$str.="<option value='0'>All</option>";
		$data = $this->m_dbConnRoot->select($query);
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
	
	public function combobox1($query, $id,$bShowAll=true)
	{   
		if($bShowAll==true)
		{
			$str.="<option value='0'>All</option>";
		}
		else
		{
			$str.="<option value=''>Please select</option>";
		}
		$data = $this->m_dbConnRoot->select($query);
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
	public function display1($rsas)
	{
		//$thheader = array('ad_title','desp','post_date','exp_date','img');
		$thheader = array('Image','Details');
		$this->display_pg->edit		= "getclassified";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "addclassified.php";

		$res = $this->display_pg->display_datatable($rsas, false /*Show Edit Option*/, false /*Hide Delete Option*/,false /*Show View Option*/,false);
		return $res;
	}
	public function pgnation($cat_id)
	{
		
		
		$FinalArray = array();
	if($cat_id==0)
	{	$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and society_id= '".$_SESSION['society_id']."'and c.status='Y' and c.active='1' ";
	}
	else
	{
		$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and cc.cat_id='".$cat_id."' and society_id= '".$_SESSION['society_id']."' and c.status='Y' and c.active='1' ";
	}
		// $sql1 = "select id,`ad_title`,`location`,`email`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_type`,`img` from classified where status='Y'  and active='1'";
		$cntr = "select count(status) as cnt from classified where status='Y'";
			$result = $this->m_dbConnRoot->select($sql1);
			
			
			for($i=0;$i <= sizeof($result)-1; $i++)
			{ 
			$image=$result[$i]['img'];
			$image_collection = explode(',', $image);
			//print_r($image_collection);
			//echo $image;
			if($image=='')
				{
					$image_collection[0]="nophoto.PNG";
				}
			 $date = $this->obj_utility->getDateDiff($result[$i]["exp_date"], date("Y-m-d"));
			
				//$tempHtmlTable ="<table>";
				$FinalArray[$i]["id"] = $result[$i]["id"];
				//$tempHtmlTable ="<table><tr><td>"
				if($date <= 0){
				$FinalArray[$i]["name"] = "<a href='show_classified.php?id=".$result[$i]["id"]."''><table><tr><td><p style='width: 180px; height: 110px; margin-top: 5px;'><img style='width: 180px; height: 120px;'  src='ads/".$image_collection[0]."'  title='".$result[$i]["name"]."'/></p><span><img  style='width: 110px;margin-top: -120px;margin-left: -1px;' src='images/sash-expired.png'></span></td></tr><tr><td style='background-color: #d9edf7;'><span style='float: left;font-size: 18px; color: currentColor;font-weight: 600;margin-left:70px;height:30px;text-transform: capitalize;'>".$result[$i]["name"]."</span></td></tr></table></a>";
				}
				else{
					$FinalArray[$i]["name"] = "<a href='show_classified.php?id=".$result[$i]["id"]."''><table><tr><td><p style='width: 180px; height: 110px; margin-top: 5px;'><img style='width: 180px; height: 120px;'  src='ads/".$image_collection[0]."'  title='".$result[$i]["name"]."'/></p><img  style='width: 100px;margin-top: -120px;margin-left: -2px;' src='images/sash-aprove.png'></span></td></tr><tr><td style='background-color: #d9edf7;'><span style='float: left;font-size: 18px; color: currentColor;font-weight: 600;margin-left:60px;height:30px;text-transform: capitalize;'>".$result[$i]["name"]."</span></td></tr></table></a>";
					}
				$tempHtmlTable ="<a href='show_classified.php?id=".$result[$i]["id"]."''><table>
				<tr><!--<td style='float: left; height: auto;'><img  src='ads/".$result[$i]["img"]."'/></td>-->
				<td valign='top'><div style='width:325px;'>
				<span style='float: left;color: blue;font-size: 18px;text-transform: capitalize; text-align: left;'>".$result[$i]["ad_title"]."</span></div><br>.<!--<span style='float: left;margin-top: 14px;font-size: 18px; color: currentColor;font-weight: 600; margin-left: -37px;'>".$result[$i]["cat_type"]."</span><br>-->
				<div style='width:300px;'><span style='float: left;font-size: 12px;font-weight: 700;text-transform: capitalize;'>".$result[$i]["location"]."</span></div><br>
				<div style='width:150px; float:right'><span style='float: right;margin-top: -39px; margin-left: 0px;font-size: 11px;font-weight: bold;color: black;'>Publish On :&nbsp;&nbsp;".getDisplayFormatDate($result[$i]["act_date"])."</span></div><br>";
				
				if($date <= 0)
				{ 
				$tempHtmlTable .="<div style='width:150px; float:right'><span style='float: right;margin-top: -35px;font-size: 11px;font-weight: bold; color: red;'>&nbsp;Expired :&nbsp;&nbsp;".getDisplayFormatDate($result[$i]["exp_date"]);
				}
				else
				{
					$tempHtmlTable .="<div style='width:150px; float:right'><span style='float: right;margin-top: -35px;font-size: 11px;font-weight: bold;color: blue;'>&nbsp;Expire On :&nbsp;&nbsp;".getDisplayFormatDate($result[$i]["exp_date"]);
				}
					
				$tempHtmlTable .="</span></div><br><div class='r' ><span style='text-align: justify; float: left;margin-left: 0px;font-size: 12px;'><div  class='box after' id='dot3'>".substr($result[$i]["desp"],0,350)."....<a href='show_classified.php?id=".$result[$i]["id"]."'   
				class='readmore'><b>Read more...</b> &raquo;</a></div></span></div></td></tr></table></a>";
				$FinalArray[$i]["data"] = $tempHtmlTable;
			}
		$this->display1($FinalArray);
		//$this->display_pg->sql1		= $sql1;
		//$this->display_pg->cntr1	= $cntr;
		/*$this->display_pg->mainpg	= "addclassified.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
	}
	public function display2($rsas)
	{
		//$thheader = array('Image','Category','Title','Post-Date','Expiry-Date','Details','Aprove');
		$thheader = array('Image','Title','Category','Post-Date','Expiry-Date','Details','Status');
		$this->display_pg->edit		= "getclassified";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "addclassified.php";

		//$res = $this->display_pg->display_datatable($rsas, true /*Show Edit Option*/, true /*Hide Delete Option*/,false /*Show View Option*/,false);
		$this->display_reg_short($result);
		return $res;
	}
	public function pgnation1($cat_id)
	{
	//echo "hellow";
	if($_SESSION['role'] == ROLE_SUPER_ADMIN ){
		
			$FinalArray = array();
			if($cat_id==0)
			{
				$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and society_id= '".$_SESSION['society_id']."' and  active='1' ";
			}
			else{
				
				$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and cc.cat_id='".$cat_id."' and society_id= '".$_SESSION['society_id']."'   and  active='1' ";
				}
	/* $sql1 = "select `id`,`ad_title`,`location`,`email`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_type`,`img` ,`status`,`active` from classified where active='1'";*/
		// echo $sql1;
		
	}
	else if($_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN)
	{  
	if($cat_id==0)
	{
		$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and society_id= '".$_SESSION['society_id']."' and  active='1' ";
		}
		else
		{
			$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and cc.cat_id='".$cat_id."'  and society_id= '".$_SESSION['society_id']."' and  active='1' ";
		}
		/* $sql1 = "select `id`,`ad_title`,`location`,`email`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_type`,`img` ,`status` from classified where society_id= '".$_SESSION['society_id']."' and active='1'";*/
	}
	else
	{
		
		if($cat_id==0)
		{
		 $sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and c.login_id= '".$_SESSION['login_id']."' and  c.active='1' ";
		}
		else
		{
			$sql1 ="select c.id,c.ad_title,c.location,c.email,c.desp,c.post_date,c.act_date,c.exp_date,cc.name,c.img,c.status,c.active from `classified` as c join `classified_cate` as cc on  c.cat_id=cc.cat_id  where  c.cat_id=cc.cat_id and cc.cat_id='".$cat_id."' and c.login_id= '".$_SESSION['login_id']."' and  c.active='1' ";
		}
		// $sql1="select `id`,`ad_title`,`location`,`email`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_type`,`img`,`status` from classified where login_id= '".$_SESSION['login_id']."'  and active='1'";
	}
	
			$result = $this->m_dbConnRoot->select($sql1);
			$this->display_reg_short($result);
			/*for($i=0;$i <= sizeof($result)-1; $i++)
			{ 
			 $image=$result[$i]['img'];
			 $image_collection = explode(',', $image);
				$FinalArray[$i]["id"] = $result[$i]["id"];
				
				if($image=='')
				{
					$image_collection[0]="nophoto.PNG";
				}
					$FinalArray[$i]["name"] = "<table><tr><td><p style='width:60px; height: 20px; margin-top: 5px;'><img style='width: 70px; height: 40px; margin-top: -7px;'  src='ads/".$image_collection[0]."'  title='".$result[$i]["name"]."'/></p></td></tr><!--<tr><td style='background-color: #d9edf7;'><span style='float: left;font-size: 14px; color: currentColor;font-weight: 600;margin-left:43px;height:15px;margin-top: -5px;'>".$result[$i]["name"]."</span></td></tr>--></table>";
					
				$tempHtmlTable ="<table>
				<tr>
				<td valign='top'><!--<div style='width:250px;'>
				<span style='float: left;color: blue;font-size: 15px;margin-top: -8px;'>".$result[$i]["ad_title"]."</span></div><br>-->";
						$tempHtmlTable .="</span></div><br><div class='r' ><span style='text-align: justify; float: left;margin-left: 0px;margin-top: -25px;font-size: 11px;'><div  class='box after' id='dot3'>".substr($result[$i]["desp"],0,90)."...</div></span></div></td></tr></table>";
		$FinalArray[$i]["category"] = $result[$i]["name"];
		$FinalArray[$i]["title"] = $result[$i]["ad_title"];
		$FinalArray[$i]["post_date"] = getDisplayFormatDate($result[$i]["post_date"]);
		$FinalArray[$i]["expiry_date"] = getDisplayFormatDate($result[$i]["exp_date"]);
		$FinalArray[$i]["data"] = $tempHtmlTable;
		
		if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN)
		{
			if($result[$i]["status"]=='N')
			{
		
			
				$FinalArray[$i]["aprove"] = "<p style='color:red;font-size:12px;'><b>Pending</b></p>";
				
			} 
			else
			{
		
			$FinalArray[$i]["aprove"] = "<p style='color:green;font-size:12px;'><b>Aproved</b></p>";
			}
		
		}
		else
		{
			if($result[$i]["status"]=='N')
			{
			$FinalArray[$i]["aprove"] = "<p style='color:red;font-size:12px;'><b>Pending</b></p>";
		
			}
			else
			{
			$FinalArray[$i]["aprove"] = "<p style='color:green;font-size:12px;'><b>Aproved</b></p>";
			}
		}
		}
		$this->display2($FinalArray);*/
		//print_r($FinalArray);
		
	}
	//**************************************************Display *********************************************888//
	public function display_reg_short($res)
	{ 
	//print_r($res);
	if($res<>"")
		{
			?>
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
            <tr  height="30" bgcolor="#CCCCCC">
           	<th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Post-Date</th>
            <th>Expiry-Date </th>
            <th style="text-align:center">Details</th>
            <th>Status</th>
            <th>Edit</th>
            <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
			foreach($res as $k => $v)
			{
            $image=$res[$k]['img'];
			$image_collection = explode(',', $image);
			//print_r ($image_collection);
			if($image=='')
				{
					$image_collection[0]="nophoto.PNG";
					
				}
			 ?>
				 <tr height="25" bgcolor="#BDD8F4" align="center"> 
                <td >
                	<img src='ads/<?php echo $image_collection[0];?>'  height="60" width="60"/>
                </td>
                <td align="center"><?php echo $res[$k]['ad_title'];?></td>
                
                <td align="center"><?php echo $res[$k]['name'];?> </td>
                
                <td align="center"><?php echo getDisplayFormatDate($res[$k]['act_date']);?> </td>
                
               <td align="center"><?php echo getDisplayFormatDate($res[$k]['exp_date']);?> </td>
               
               <td align="center">
			  <div class='r' ><span style='text-align: justify; float: left;margin-left: 0px;margin-top: -0px;font-size: 11px;'> <div  class='box after' id='dot3'><?php echo substr($res[$k]["desp"],0,90);?>...</div></span></div></td>
             
         <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN)
			{
				if($res[$k]['status']=='N')
				{?>
               <td>
               <p style='color:red;font-size:12px;'><b>Pending</b></p>
          		</td>
			<?php 
				} 
			else
				{?>
               
            	<td>
            	<p style='color:green;font-size:12px;'><b>Aproved</b></p>
				</td>
     	<?php 
	 			}
			}
		else
			{
			if($res[$k]['status']=='N')
			{?>
            	<td>
				<p style='color:red;font-size:12px;'><b>Pending</b></p>
                </td>
                <?php
			}
			else
			{
			?>
			<td>
			<p style='color:green;font-size:12px;'><b>Aproved</b></p>
            </td>
			<?php
			 }
		
			}//}?>
   
                <?php
				if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN) )
				{
				?>
                    <td>
                	<a href="addclassified.php?edt&id=<?php echo $res[$k]['id']?>"><img src="images/edit.gif"  /></a>
                  </td>
                                
                <td>	
              		<a onclick="getclassified('delete-<?php echo $res[$k]['id']?>')"><img src="images/del.gif" /></a>                
                </td>
      		<?php
				}       
				else if($res[$k]['status']=='Y')
				{
					?>
                		<td>
                			<a href="addclassified.php?edt&id=<?php echo $res[$k]['id']?>"><img src="images/edit.gif"  style="display:none"/></a>
                     	</td>
                    	 <td>	
              			<a onclick="getclassified('delete-<?php echo $res[$k]['id']?>')"><img src="images/del.gif"  style="display:none" /></a>                
               			 </td>
                <?php 
				}
				else
				{
				?>
                 <td>      
                	<a href="addclassified.php?edt&id=<?php echo $res[$k]['id']?>"><img src="images/edit.gif" /></a>       
                  </td>   
                 <td>	
              		<a onclick="getclassified('delete-<?php echo $res[$k]['id']?>')"><img src="images/del.gif" /></a>                
               	 </td>
                <?php
				 	}
			
			}
			?>
            </tr>
            </tbody>
            </table>
			<?php
		}
		 else
			{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
			}
	}
	public function selecting()
	{
		
	$sql = "select c.`id`,c.`cat_id`,c.`ad_title`,c.`location`,c.`phone`,c.`email`,c.`act_date`,c.`exp_date`,c.`desp`,c.`img`,login.`name`,c.`status` from `classified` as c JOIN login on c.login_id = login.login_id where c.active='1' and c.id='".$_REQUEST['id']."'";
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res <> '')
		{
			$res[0]['act_date'] = getDisplayFormatDate($res[0]['act_date']);
			$res[0]['exp_date'] = getDisplayFormatDate($res[0]['exp_date']);
		}
		
		//print_r($res);
		return $res;
		
	}
	public function deleting()
	{
		$sql = "update classified set `active`='0', `status`='N' where id='".$_REQUEST['id']."'";
		$res = $this->m_dbConnRoot->update($sql);
	}
	public function member()
	{
		$sql = "SELECT * FROM `member_main` WHERE `unit` = '".$_SESSION['unit_id']."' AND `society_id` = '".$_SESSION['society_id']."'";
		$result = $this->m_dbConn->select($sql);
		return $result;	
		
		
	}
	
	public function getClassified()
	{
		//$todayDate=date('Y-m-d');
		
		
		 $sql = "SELECT * FROM `classified` WHERE status='Y' and active='1' and society_id='".$_SESSION['society_id']."' ORDER BY id DESC LIMIT 3";	
		//echo $sql;
		//$res =  $this->m_dbConnRoot->select($sql);
		
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
		public function reg_edit()
	{
		$sql = "select `img`,`id` from classified where id='".$_REQUEST['id']."' and active='1'";	
		//echo $sql;			
		$res = $this->m_dbConnRoot->select($sql);	
		
		return $res;
	}
	
	
	public function insertComments($ClassfiedID)
	{
	//print_r($_POST)	;
	date_default_timezone_set('Asia/Kolkata');
	$readDate = date('Y-m-d h:i:s');
		
			  $insertQuery="INSERT INTO `classified_commnet` (`clssified_id`, `changed_by`, `status` , `comment`,`timestamp`) VALUES ('".$ClassfiedID."', '".$_SESSION['login_id']."', '".$_POST['status']."', '".$_POST['comments']."','".$readDate."')";	
			$data = $this->m_dbConnRoot->insert($insertQuery);
		
		$selectQuery="SELECT c.login_id,c.society_id,c.ad_title,c.email,l.name FROM `classified` as c join `login` as l on c.login_id=l.login_id where c.id='".$ClassfiedID."'";
		$result = $this->m_dbConnRoot->select($selectQuery);
		
		$this->sendClassfiedMobileNotification($data, $_POST['ad_title'], true, $_POST['comments'], false);
		$this->sendEmail($ClassfiedID,$result[0]['ad_title'], $_SESSION['name'], $_POST['comments'],$_POST['status'], $result[0]['email'],$readDate );
		return;		
	}
	
	//**----------------------------Classified SMS --------------------------------------------------------
	
	public function ClassifiedSms($ClassifiedTitle, $IsApproved)
	{
		$unitDetails = array();
		$msgBody = '';
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'");
			
		if($IsApproved == true)
		{
				$units = $this->m_dbConn->select("SELECT u.id, u.unit_no, mm.mob,mm.alt_mob,u.unit_id FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."'" );
				for($j = 0; $j < sizeof($units); $j++)
					{
						array_push($unitDetails, $units[$j]);
					}
				$msgBody = "".$smsDetails[0]['sms_start_text'].", ".$ClassifiedTitle." New Classified is approved. Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']." ";
			
		}
		else
		{
			$units = $this->m_dbConn->select("SELECT u.unit_no, mm.mob FROM `unit` AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit WHERE u.society_id = '".$_SESSION['society_id']."' AND u.unit_id = '".$_SESSION['unit_id']."'");
			
			$SocietyMobile = $this->m_dbConn->select("SELECT `phone2` as mob , `society_code` as unit_no FROM society where society_id ='".$_SESSION['society_id']."'");
			
			for($j = 0; $j < sizeof($SocietyMobile); $j++)
					{
						array_push($unitDetails, $SocietyMobile[$j]);
					}
				$msgBody = "".$smsDetails[0]['sms_start_text'].", ".$ClassifiedTitle." New Classified is Generated. Please login to www.way2society.com to view or approve the classified. ".$smsDetails[0]['sms_end_text']." ";
		}
		
		
		//**----Making log file name as SendClassifiedSMS.html to track Classified sms logs ----**
		$Logfile=fopen("SendClassifiedSMS.html", "a");	
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
					$response =  $this->obj_utility->SendSMS($unitDetails[$i]['mob'], $smsText, $clientID);
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
	
		
	public function sendEmail($ClassfiedID,$title, $name, $comment, $status, $email,$date)
	{	
		
		 $sqlQuery="select society_name from `society` where society_id='".$_SESSION['society_id']."'";
		 $res = $this->m_dbConnRoot->select($sqlQuery);
		
		//echo $res[0]['society_name'];
		date_default_timezone_set('Asia/Kolkata');
		
		$mailSubject = "[Classified #".$title."]";
		$DBdate = new DateTime($date);
		$new_date_format = $DBdate->format('d-m-Y H:i:s');
		
		$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Title : '.$title.'  </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Updated By</b></td><td style="width:5%;border-left:none;"></td><td style="width:60%;">'.$name.'<br>'.$new_date_format.'</td></tr>
							<tr><td style="border-right:none;"><b>Comments</b></td><td style="border-left:none;"></td><td>'.$comment.'</td></tr>
    						<tr><td style="border-right:none;"><b>Status</b></td><td style="border-left:none;"></td><td>'.$status.'</td></tr>
    						
							<!--<tr><td style="border-right:none;"><b>Subject</b></td><td style="border-left:none;"> : </td><td>'.nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8')).'</td></tr>-->
							<!--<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"> : </td><td>'.$desc.'</td></tr>-->
							     
						</table><br />'	;		
	
	  						
				$EMailID = "";
				$Password = "";
				$EMailIDToUse = $this->obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, $_SESSION['dbname'], $_SESSION['society_id'], 0, 0);
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
					try
					{	
					// $transport = Swift_SmtpTransport::newInstance('103.50.162.146', 465, "ssl")
					//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',25)
					//->setUsername('no-reply14@way2society.com')
					//->setSourceIp('0.0.0.0')
					//->setPassword('society123') ;
					//$transport = Swift_SmtpTransport::newInstance('103.50.162.146', 465, "ssl")
					//->setUsername($EMailID)
					//->setSourceIp('0.0.0.0')
					//->setPassword($Password) ; 
					$AWS_Config = CommanEmailConfig();
					$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 			 ->setUsername($AWS_Config[0]['Username'])
				  			->setPassword($AWS_Config[0]['Password']);	
								
					$message = Swift_Message::newInstance();
					$UserEmails=$email;
					$message->setTo(array(
					$email => $name
					 	));	 
							
					$message->setSubject($mailSubject);
					$message->setBody($mailBody);
				
					$message->setFrom('no-reply@way2society.com',$res[0]['society_name']);
					
					$message->setContentType("text/html");										 
					$mailer = Swift_Mailer::newInstance($transport);
					$resultEmailSend = $mailer->send($message);											
				
						if($resultEmailSend == 1)
						{
							echo 'Success';
						}
						else
						{
							echo 'Failed';
						}	
					}
				catch(Exception $exp)
					{
				
						echo "Error occure in email sending.";
					}
			}
			
//**--------------------When Classified is approve------------------------------------------------------///

			public function sendClassfiedMobileNotification($ClassID, $ad_title,$Iscomment,$Comment,$IsApproved)
				{	
					if($Iscomment == true)
					{
						$ClassfiedTitle = "New Comment on ".$ad_title;
						$classifiedMassage = $Comment;
					}
					else if($IsApproved == true)
					{
						$ClassfiedTitle = "Classfied Approved";
						$classifiedMassage = $ad_title;
					}
					else
					{
						$ClassfiedTitle = "New Classified Generated";
						$classifiedMassage = $ad_title;
					}

							$obj_dbConn= new dbop(false,$_SESSION['dbname']);
							$obj_bbConnRoot=new dbop(true)	;
							$obj_Fetch = new FetchData($obj_dbConn,$obj_bbConnRoot);
							
							if($IsApproved == true)
							{
								$emailIDList = $obj_Fetch->GetEmailIDToSendNotification(0);	
							}
							else if($IsApproved == false)
							{
								$emailIDList = $this->GetEmailIDToSendNotification($ClassID, $Iscomment);
							}
							for($i = 0; $i < sizeof($emailIDList); $i++)
							{	
		
							  if(($emailIDList[$i]['to_email'] <> ""))
							  {
								$unitID = $emailIDList[$i]['unit'];
								
								$objAndroid = new android($emailIDList[$i]['to_email'], $_SESSION['society_id'], $unitID);
								$sendMobile=$objAndroid->sendClassifiedNotification($ClassfiedTitle,$classifiedMassage,$ClassID);
									
							  }
							}
			
				}
						
			
	public function GetEmailIDToSendNotification($ClassID,$Iscomment)
	{
		
			$UnitDetails = array();
			
			if($Iscomment == true)
			{
				$selectQuery="SELECT c.login_id, m.unit_id as unit, c.society_id,c.ad_title,c.email as to_email,l.name FROM `classified` as c join `login` as l on c.login_id=l.login_id JOIN mapping as m ON m.login_id = l.login_id where c.id='".$ClassID."' group by m.login_id ";
				$result = $this->m_dbConnRoot->select($selectQuery);
				return $result;
			}
			else
			{
				$CurrentUserDetail = $this->m_dbConnRoot->select("SELECT m.unit_id as unit, l.member_id as to_email from login as l JOIN mapping as m ON l.login_id = m.login_id WHERE l.login_id = '".$_SESSION['login_id']."' AND  m.society_id = '".$_SESSION['society_id']."' group by l.member_id");
				
				
				for($j = 0; $j < sizeof($CurrentUserDetail); $j++)
				{
					array_push($UnitDetails, $CurrentUserDetail[$j]);
				}
			
				$FetchSociety_details = $this->m_dbConnRoot->select("SELECT m.unit_id as unit, l.member_id as to_email FROM mapping as m JOIN login as l ON m.login_id = l.login_id JOIN profile as p ON m.profile = p.id WHERE  PROFILE_CLASSIFIED = 1 AND m.society_id = '".$_SESSION['society_id']."' group by l.member_id");
				
				
				for($k = 0; $k < sizeof($FetchSociety_details); $k++)
				{
					array_push($UnitDetails, $FetchSociety_details[$k]);	
				}
				
				return $UnitDetails;
			}
		
	}
				
						
//***-------------------------------Send Email When Classified onnce Approved--------------------------//

	public function SendClassifiedEmail($Classid, $ad_title, $location, $phone, $email, $act_date, $exp_dat, $desp, $cat_id, $IsNewClassified)
		{
		 $sqlQuery="select society_name from `society` where society_id='".$_SESSION['society_id']."'";
		 $res = $this->m_dbConnRoot->select($sqlQuery);
		
		//echo $res[0]['society_name'];
		date_default_timezone_set('Asia/Kolkata');
		
		if($IsNewClassified == true)
		{
			$mailSubject = "[Classified For Approval#".$ad_title."]";
		}
		else
		{
			$mailSubject = "[Classified Approved#".$ad_title."]";
		}
		$DBdate = new DateTime($date);
		$new_date_format = $DBdate->format('d-m-Y H:i:s');
		
		
		if($IsNewClassified == true)
		{
			$userURL = 'https://way2society.com/addclassified.php?edt&id='.$Classid.'';
			//$userURL = 'http://localhost/beta_awsamit/addclassified.php?edt&id='.$title.'';
			
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Title :"'.$ad_title. '"  New classified generated </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Created By</b></td><td style="width:5%;border-left:none;"></td><td style="width:60%;">'.$_SESSION['name'].'<br>'.$new_date_format.'</td></tr>
							<tr><td style="border-right:none;"><b>Title</b></td><td style="border-left:none;"></td><td>'.$ad_title.'</td></tr>
    						<tr><td style="border-right:none;"><b>Location</b></td><td style="border-left:none;"></td><td>'.$location.'</td></tr>
							<tr><td style="border-right:none;"><b>Mobile Number</b></td><td style="border-left:none;"></td><td>'.$phone.'</td></tr>
							<tr><td style="border-right:none;"><b>Email</b></td><td style="border-left:none;"></td><td>'.$email.'</td></tr>
							<tr><td style="border-right:none;"><b>Publish On</b></td><td style="border-left:none;"></td><td>'.$act_date.'</td></tr>
							<tr><td style="border-right:none;"><b>Expire On</b></td><td style="border-left:none;"></td><td>'.$exp_dat.'</td></tr>
							<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"></td><td>'.$desp.'</td></tr>
							<tr><td colspan="3" valign="middle" style="padding-left: 35%;cursor: pointer;"><button type="button"  bgcolor="#337AB7" align="center" style ="width:30%;background-color: #337AB7;color:#ffffff;font-size:14px;"><strong><a href= "'.$userURL.'" style ="text-decoration: none;color: white;">View Classfied</a></strong></button></td></tr>
							</table><br />'	;
			
							$UserEmail = $this->m_dbConn->select("SELECT email, society_name as owner_name from society where society_id = '".$_SESSION['society_id']."'");
							$NewArray = array('owner_name' => $_SESSION['name'],'email' => $email);
							array_push($UserEmail,$NewArray);
							
		}
		else
		{
		$userURL = 'https://way2society.com/classified.php';
		//$userURL = 'http://localhost/beta_awsamit/classified.php';	
		$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Title : "'.$ad_title. '" classified is Approved  </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Approved By</b></td><td style="width:5%;border-left:none;"></td><td style="width:60%;">'.$_SESSION['name'].'<br>'.$new_date_format.'</td></tr>
							<tr><td style="border-right:none;"><b>Title</b></td><td style="border-left:none;"></td><td>'.$ad_title.'</td></tr>
    						<tr><td style="border-right:none;"><b>Location</b></td><td style="border-left:none;"></td><td>'.$location.'</td></tr>
							<tr><td style="border-right:none;"><b>Mobile Number</b></td><td style="border-left:none;"></td><td>'.$phone.'</td></tr>
							<tr><td style="border-right:none;"><b>Email</b></td><td style="border-left:none;"></td><td>'.$email.'</td></tr>
							<tr><td style="border-right:none;"><b>Publish On</b></td><td style="border-left:none;"></td><td>'.$act_date.'</td></tr>
							<tr><td style="border-right:none;"><b>Expire On</b></td><td style="border-left:none;"></td><td>'.$exp_dat.'</td></tr>
							<tr><td style="border-right:none;"><b>Description</b></td><td style="border-left:none;"></td><td>'.$desp.'</td></tr>
							<tr><td colspan="3" valign="middle" style="padding-left: 35%;cursor: pointer;"><button type="button"  bgcolor="#337AB7" align="center" style ="width:30%;background-color: #337AB7;color:#ffffff;font-size:14px;"><strong><a href= "'.$userURL.'" style ="text-decoration: none;color: white;">View Classfied</a></strong></button></td></tr></table><br />'	;	
							
					$UserEmail = $this->m_dbConn->select("SELECT email, owner_name from member_main where ownership_status = 1");
		}
	
	  			
						
				$EMailID = "";
				$Password = "";
				$EMailIDToUse = $this->obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, $_SESSION['dbname'], $_SESSION['society_id'], 0, 0);
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
				//$EMailID ="sujitkumar0304@gmail.com";
				//$Password = "sujit0304";
				
				for($i = 0 ; $i < sizeof($UserEmail) ; $i++)
				{
					 $email = $UserEmail[$i]['email'];
					 $name = $UserEmail[$i]['owner_name'];
					if($email <> '')
					{
						try
						{	
					
						// $transport = Swift_SmtpTransport::newInstance('103.50.162.146',25)
						//->setUsername($EMailID)
						//->setSourceIp('0.0.0.0')
						//->setPassword($Password) ; 
						//AWS Config
						//echo "inside";
						$AWS_Config = CommanEmailConfig();
						 $transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				 			 	->setUsername($AWS_Config[0]['Username'])
				  				->setPassword($AWS_Config[0]['Password']);	 			
						$message = Swift_Message::newInstance();
						$UserEmails=$email;
						$message->setTo(array(
						$email => $name
							));	 
								
						$message->setSubject($mailSubject);
						$message->setBody($mailBody);
					
						$message->setFrom('no-reply@way2society.com',$res[0]['society_name']);
						
						$message->setContentType("text/html");										 
						$mailer = Swift_Mailer::newInstance($transport);
						$resultEmailSend = $mailer->send($message);											
							if($resultEmailSend == 1)
							{
								echo 'Success';
							}
							else
							{
								echo 'Failed';
							}	
						}
					catch(Exception $exp)
						{
					
							echo "Error occure in email sending.";
						}
					}
					  
				}
		}
 				

}

?>
