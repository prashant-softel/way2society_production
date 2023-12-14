<?php
include_once("include/display_table.class.php");
include_once ("dbconst.class.php"); 
include_once("utility.class.php");
include_once( "include/fetch_data.php");
include_once("android.class.php");
include_once("email.class.php");
class service_prd_reg 
{
	public $actionPage = "../service_prd_reg.php?srm";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_utility;
	public $objFetchData;
	public $obj_fetch;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
		$this->obj_fetch = new FetchData($this->m_dbConn);

		$a = $this->obj_fetch->GetSocietyDetails($_SESSION['society_id']);
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Register' && $errorExists==0)
		{
			
				$CheckQuery= "Select * From service_prd_society where society_id ='".$_SESSION['society_id']."' and 	society_staff_id='".$this->m_dbConn->escapeString(ucwords(trim($_POST['staff_id'])))."'" ;
				$dataFound =$this->m_dbConnRoot->select($CheckQuery);
				$Staff_id= $dataFound[0]['society_staff_id'];
				if($Staff_id <> "")
				{
					return "Staff Id Already Exist".$Staff_id;

				}
				else
				{
				//die();
				if($_FILES['photo']['name'] <> "")
				{ 
					//$exe_photo_main = strtolower(substr($_FILES['photo']['name'],-4));
						
					//if($exe_photo_main=='.jpg' || $exe_photo_main=='.png' || $exe_photo_main=='.jpeg' || $exe_photo_main=='.bmp' || $exe_photo_main=='.gif')
					if (($_FILES["photo"]["type"] == "image/gif") || 
									($_FILES["photo"]["type"] == "image/jpeg") || 
									($_FILES["photo"]["type"] == "image/png") || 
									($_FILES["photo"]["type"] == "image/jpg")) 
							{
								 $exe_photo_main = "";
								//$extension= "";
								//$url="";
								if ($_FILES["photo"]["type"] == "image/jpeg")
								{
									$exe_photo_main =".jpeg" ;
								}
								else if($_FILES["photo"]["type"] == "image/png")
								{
									$exe_photo_main =".png" ;
								}
								else if ($_FILES["photo"]["type"] == "image/gif")
								{
									 $exe_photo_main =".gif" ;
								}
								else if ($_FILES["photo"]["type"] == "image/jpg")
								{
									 $exe_photo_main =".jpg" ;
								}	
								
					$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],'../upload/main');
					////////////////////////////////////////
					$thumbWidth_index  = 140;
					$thumbHeight_index = 130;
					
					//$pathToThumbs_index = '../upload/thumb/';
					//$pathToThumbs_index = $_SERVER['DOCUMENT_ROOT'].'/upload/thumb/';
					$pathToThumbs_index = '../upload/thumb/';
					$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
					
					$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
					////////////////////////////////////////
					//$thumb_path='';
					}
					else 
					{					
						return 'Invalid File Type For Photo';
					}
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
			$dob = $_POST['dob'];
			$dob1 = explode('-',$dob);
			$dd = $dob1[0];
			$mm = $dob1[1];
			$yy = $dob1[2];		
			$age = $this->age($dd, $mm, $yy);
			 $selectQuery= "select * from service_prd_reg where cur_con_1 = '".$_POST['cur_con_1']."'";
			$Result = $this->m_dbConnRoot->select($selectQuery);
			
			$ProviderRegId=$Result[0]['service_prd_reg_id'];
			//echo "Provider ID :".$ProviderRegId;
			//die();
			if($ProviderRegId > 0)
			{
				 $insert_query1= "insert into service_prd_society (`provider_id`,`society_id`,`society_staff_id`) value ('".$ProviderRegId."','".$_SESSION['society_id']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['staff_id'])))."')"	;	
			$data1 = $this->m_dbConnRoot->insert($insert_query1);
			
			
				$units = json_decode($_POST['unit1']);						
			
				for($i = 0; $i < sizeof($units); $i++)
				{   $unit = $units[$i+1];
				
					//$selectUnit ="Select * From service_prd_units where service_prd_id ='".$data."'" ;
					
					 $sql = "INSERT INTO `service_prd_units`(`service_prd_id`, `unit_id`, `unit_no`, `society_id`) VALUES ('".$ProviderRegId."','".$units[$i]."', '".$unit."', '".$_SESSION['society_id']."')";	
					$result1 = $this->m_dbConnRoot->insert($sql);
					$i++;					
				}		
			}
			else
			{
			$insert_query = "insert into service_prd_reg
							(`society_id`,`full_name`,`photo`,`photo_thumb`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,
							`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,
							`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ`) 
							 
							 values 
							('".$_SESSION['society_id']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['full_name'])))."','".$photo_new_path."','".$thumb_path."','".$age."','".$_POST['dob']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['identy_mark'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['cur_resd_add'])))."','".$_POST['cur_con_1']."','".$_POST['cur_con_2']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['native_add'])))."',
							
							'".$_POST['native_con_1']."','".$_POST['native_con_2']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_add'])))."','".$_POST['ref_con_1']."','".$_POST['ref_con_2']."','".getDBFormatDate($_POST['since'])."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['education'])))."','".$_POST['marry']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_name'])))."',
							
							'".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_occ'])))."')";
			$data = $this->m_dbConnRoot->insert($insert_query);
			
		 $insert_query1= "insert into service_prd_society (`provider_id`,`society_id`,`society_staff_id`) value ('".$data."','".$_SESSION['society_id']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['staff_id'])))."')"	;	
			$data1 = $this->m_dbConnRoot->insert($insert_query1);	
			if($_POST['cat_id']<>"")
			{
				foreach($_POST['cat_id'] as $k => $v)
				{
					$sql1 = "insert into spr_cat(`service_prd_reg_id`,`cat_id`)values('".$data."','".$v."')";
					$res1 = $this->m_dbConnRoot->insert($sql1);
				}
			}
			
	
				for($i = 0; $i < $_POST['totaldoc']; $i++)
				{
					$fileName = "";						
					if($_POST['document'.$i] <> "")					
					{
						if($_FILES['file'.$i]['name'] <> "")
						{	
							$fileTempName = $_FILES['file'.$i]['tmp_name'];  
							$fileSize = $_FILES['file'.$i]['size'];
							$fileName = time().'_'.basename($_FILES['file'.$i]['name']);
									
							$uploaddir = "../Service_Provider_Documents";			   
							$uploadfile = $uploaddir ."/". $fileName;	
												
							move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);							
						}									
						$sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`, `attached_doc`)values('".$data."','".$_POST['document'.$i]."', '".$fileName."')";
						$res2 = $this->m_dbConnRoot->insert($sql2);
					}
				}
			//}
						
			$units = json_decode($_POST['unit1']);						
			
			for($i = 0; $i < sizeof($units); $i++)
			{
				$unit = $units[$i+1];
				$sql = "INSERT INTO `service_prd_units`(`service_prd_id`, `unit_id`, `unit_no`, `society_id`) VALUES ('".$data."','".$units[$i]."', '".$unit."', '".$_SESSION['society_id']."')";	
				$result = $this->m_dbConnRoot->insert($sql);
				$i++;					
			}		
			?>
            <script>//window.location.href = '../service_prd_reg_view.php?srm&add&idd=<?php //echo time();?>';</script>
            <?php
			$this->Ser_Prv_Approved_email($data , false);
			$this->sendServicePrdMobileNotification($data, false);
			$this->sendServicePrdSMS($data, $IsApproved);
			return "Insert";
		}		
	}
		/*
			}
			else
			{
				return "Already Exist";	
			}
		*/
	}
	}
	public function age( $day,$month,$year)
	{
		(checkdate($month, $day, $year) == 0) ? die("no such date.") : "";
		$y = gmstrftime("%Y");
		$m = gmstrftime("%m");
		$d = gmstrftime("%d");
		$age = $y - $year;
		return (($m <= $month) && ($d <= $day)) ? $age - 1 : $age;
	}
	
	public function startProcess1()
	{
		if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query = "update service_prd_reg set `full_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['full_name'])))."',`photo`='".$_POST['photo']."',`age`='".$_POST['age']."',`dob`='".$_POST['dob']."',`identy_mark`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['identy_mark'])))."',`cur_resd_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['cur_resd_add'])))."',`cur_con_1`='".$_POST['cur_con_1']."',`cur_con_2`='".$_POST['cur_con_2']."',`native_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['native_add'])))."',`native_con_1`='".$_POST['native_con_1']."',`native_con_2`='".$_POST['native_con_2']."',`ref_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_name'])))."',`ref_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_add'])))."',`ref_con_1`='".$_POST['ref_con_1']."',`ref_con_2`='".$_POST['ref_con_2']."',`since`='".$_POST['since']."',`education`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['education'])))."',`marry`='".$_POST['marry']."',`father_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_name'])))."',`father_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_occ'])))."',`mother_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_name'])))."',`mother_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_occ'])))."',`hus_wife_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_name'])))."',`hus_wife_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_occ'])))."',`son_dou_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_name'])))."',`son_dou_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_occ'])))."',`other_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_name'])))."',`other_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_occ'])))."' where service_prd_reg_id='".$_POST['id']."'";
			$data=$this->m_dbConn->update($up_query);
			return "Update";
		
			
			$s  = "delete from hotel_cat where service_prd_reg_id='".$_POST['id']."' and status='Y'"; //echo '<br />';
			$s1 = "delete from spr_document where service_prd_reg_id='".$_POST['id']."' and status='Y'"; //echo '<br />';
			
			$r  = $this->m_dbConn->delete($s);
			$r1 = $this->m_dbConn->delete($s1);
			
			if($_POST['cat_id']<>"")
			{
				foreach($_POST['cat_id'] as $k => $v)
				{
					$sql1 = "insert into spr_cat(`service_prd_reg_id`,`cat_id`)values('".$data."','".$v."')";
					$res1 = $this->m_dbConn->insert($sql1);
				}
			}
			
			if($_POST['document']<>"")
			{
				foreach($_POST['document'] as $k1 => $v1)
				{
					$sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`)values('".$data."','".$v1."')";
					$res2 = $this->m_dbConn->insert($sql2);
				}
			}
			return "Update";
		}
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
	
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
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
	public function combobox($query)
	{
			$str.="<option value=''>Please Select</option>";
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
	
	public function combobox11($query,$name,$id)
	{
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
					}
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	public function combobox111($query,$name,$id,$new_id)
	{
		$ww = explode(",",$new_id);
		
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if(in_array($v,$ww))
						{
							$s="checked";
						}
						else
						{
							$s="";
						}
					?>
					<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" <?php echo $s;?>/>					
					<?php
					}
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	public function display1($rsas)
	{
			$thheader=array('full_name','photo','age','dob','identy_mark','cur_resd_add','cur_con_1','cur_con_2','native_add','native_con_1','native_con_2','ref_name','ref_add','ref_con_1','ref_con_2','since','education','marry','father_name','father_occ','mother_name','mother_occ','hus_wife_name','hus_wife_occ','son_dou_name','son_dou_occ','other_name','other_occ');
			$this->display_pg->edit="getservice_prd_reg";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="service_prd_reg.php";
			
			//$res=$this->display_reg($rsas);
			$res=$this->display_reg_short($rsas);
			
			return $res;
	}
	public function pgnation()
	{
		$sql1 ="select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, sp.active, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat,sps.society_staff_id from service_prd_reg as sp, society as s, spr_cat as sc, cat as c , service_prd_society as sps where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sps.provider_id = sp.service_prd_reg_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y' and sp.society_id = '" . $_SESSION['society_id'] . "'";
		// $sql1 = "select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, sp.active, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y' and sp.society_id = '" . $_SESSION['society_id'] . "'";
		
		
		//$sql1 = "select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$sql1 .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			foreach($_REQUEST['cat_id'] as $k => $v)
			{
				$cat_id0 .= $v.',';
			}
			$cat_id = substr($cat_id0,0,-1);
			$sql1 .= " and sc.cat_id in (".$cat_id.")";
		}
		if($_REQUEST['key']<>"")
		{
			$sql1 .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		$sql1 .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$cntr = "select count(*) as cnt from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$cntr .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$cntr .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			foreach($_REQUEST['cat_id'] as $k1 => $v1)
			{
				$cat_id00 .= $v1.',';
			}
			$cat_id0 = substr($cat_id00,0,-1);
			$cntr .= " and sc.cat_id in (".$cat_id0.")";
		}
		if($_REQUEST['key']<>"")
		{
			$cntr .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		/*$cntr .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="service_prd_reg.php";
		$limit = "30";
		$page = $_REQUEST['page'];
		
		$extra = "&srm";
		
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
			
		$result=$this->m_dbConnRoot->select($sql1);
		$this->display_reg_short($result);
			
	}
	
	
	
	########################################################################################################################################
	########################################################################################################################################
	
	
	public function display_reg_short($res)
	{
		//print_r($res);
		if($res<>"")
		{
			?>
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
            <tr  height="30" bgcolor="#CCCCCC">
            	<?php //if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN){?>
                <!--<th width="180">Society Name</th>-->
                <?php //}?>
                <th ><input type="checkbox" name="allcheck" id="allcheck" onClick="SelectAllPrintIDCard(this)"></th>
            	<th >Staff ID</th>
                <th >Photo</th>
                <th >Full Name</th>
                <th >Age(Year)</th>
                <th style="width:80px;" >Category</th>
                <th> Working in Units </th>
                <th >View</th>
                <th >Print</th>
                <!--<th >Print Id Card</th>-->
                <?php //if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN) ){?>
            	<th style="width:46px;" >Status</th>
                <th >Edit</th>
                <th >Delete</th>
                <?php //} ?>
            </tr>
            </thead>
            <tbody>
            <?php
			foreach($res as $k => $v)
			{?>
				
			
            <tr height="25" bgcolor="#BDD8F4" align="center" id="tr_<?php echo $res[$k]['service_prd_reg_id'] ?>">
            <?php if($res[$k]['active']==0)
				{?>
			
            <td><input type="checkbox" name="check" id="check_<?php echo $res[$k]['service_prd_reg_id']?>"  value="<?php echo $res[$k]['service_prd_reg_id']?>" style="display:none;"></td>
				<?php }
				else
				{?>
                
            <td><input type="checkbox" name="check" id="check_<?php echo $res[$k]['service_prd_reg_id']?>"  value="<?php echo $res[$k]['service_prd_reg_id']?>"></td>
            			<script>
						 aryServiceRegID.push("<?php echo  $res[$k]['service_prd_reg_id']?>");
						</script>
            	<?php } ?>
                <td><?php echo $res[$k]['society_staff_id']?></td>
                <td >
                	<a href="<?php echo substr($res[$k]['photo'],3);?>" target="_blank" class="fancybox"><img src="<?php echo substr($res[$k]['photo_thumb'], 3);?>" height="45" width="45"/></a>
                </td>
                <td align="center">
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><?php echo $res[$k]['full_name'];?></a>
                </td>
                <?php if($res[$k]['age']==0 || $res[$k]['age']==-1)
				{?>
                <td align="center"><?php echo "NA";?> </td>
                <?php }
				else
				{?>
				<td align="center"><?php echo $res[$k]['age'];?> </td>	
				<?php }?>
                <td align="center">
                    <!--<div style="overflow-y:scroll;overflow-x:hidden;width:200px; height:50px; border:solid #CCCCCC 1px;">-->
                    <?php $get_reg_cat = $this->get_reg_cat($res[$k]['service_prd_reg_id']);?>
                    <!--</div>-->
                </td>
                
                <td align="center">
                	<?php $get_reg_units = $this->get_reg_units($res[$k]['service_prd_reg_id']);?>
                </td>
               <?php if($res[$k]['active']==0)
				{?>
               	<td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><img src="images/view.jpg" width="20" width="20" style="display:none;" /></a>
                </td>
                
                <td>
                	<a href="reg_form_print1.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35" style="display:none;"/></a>
                </td>
                <?php }
				else
				{?>
                 	<td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><img src="images/view.jpg" width="20" width="20" style="display:block;" /></a>
                </td>
                
                <td>
                	<a href="reg_form_print1.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35" style="display:block;"/></a>
                </td>
                <?php }?>
         <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['profile']['#1.php'] == '1')
			{
				if($res[$k]['active']==0)
				{?>
               <!-- <td>
                	<a href="printcert.php?id=<?php //echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35"  style="display:none;"/></a>
                </td>-->
            		<td>
          			<span style="color:red; font-size:12px;" id="st_<?php echo $res[$k]['service_prd_reg_id'];?>"  onClick="statusapproved(this.id)" ><b>&nbsp;&nbsp;Pending</b><span style="font-size: 10px;color: black;"><br>( Click here to Aprove )</span></span></td>
			<?php 
				} 
			else
				{?>
                <!-- <td>
                	<a href="printcert.php?id=<?php //echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35"  style="display:block;"/></a>
                </td>-->
            		<td>
            		<p style='color:green;font-size:12px;'><b>Aproved</b></p>
					</td>
     	<?php 
	 			}
			}
		else
			{
			if($res[$k]['active']==0)
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
                	<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif"  /></a>
                  </td>
                                
                <td>					
                    <a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif" /></a>                 </td>
      		<?php
				}       
					else if($res[$k]['active']==1)
					{
					?>
                		<td>
                			<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif"  style="display:none"/></a>
                     	</td>
                    	<td>					
                    	<a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif"  style="display:none;"/></a> 
                		</td>
                <?php 
					}
					else
					{
						?>
                        <td>      
                		<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif" /></a>       
                        </td>   
                		<td>					
                    	<a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif" /></a>                		</td>
                        
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
	
	########################################################################################################################################
	########################################################################################################################################

	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	public function get_reg_cat($service_prd_reg_id)
	{
		$sql = "select c.cat from spr_cat as sc,cat as c where sc.cat_id=c.cat_id and sc.service_prd_reg_id='".$service_prd_reg_id."' and sc.status='Y' and c.status='Y' ";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{
				$reg_cat .= $res[$k]['cat'].', ';
			}			
			echo substr($reg_cat,0,-2);
		}
	}
	public function get_reg_doc($service_prd_reg_id)
	{
		$sql = "select d.document from spr_document as sd, document as d where sd.document_id=d.document_id and sd.service_prd_reg_id='".$service_prd_reg_id."' and sd.status='Y' and d.status='Y' ";
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{
				$reg_doc .= $res[$k]['document'].', ';
			}
			echo substr($reg_doc,0,-2);
		}
	}
	
	public function selecting()
	{
		$sql1="select service_prd_reg_id,`full_name`,`photo`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ` from service_prd_reg where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$var=$this->m_dbConnRoot->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1 = "update service_prd_reg set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConnRoot->update($sql1);
		
		$sql = "update spr_cat set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res = $this->m_dbConnRoot->update($sql);
		
		$sql2 = "update spr_document set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res2 = $this->m_dbConnRoot->update($sql2);
		
		$deleteQuery = "DELETE FROM `service_prd_units` WHERE `service_prd_id` = '".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConnRoot->delete($deleteQuery);
	}
	
	public function reg_edit()
	{
		$sql = "select * from service_prd_reg where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'";
		$res = $this->m_dbConnRoot->select($sql);	
		return $res;
	}
	
	public function fetchUnits()
	{
		//$sql = 'SELECT `unit_no`, `unit_id` FROM `unit` WHERE `society_id` = "'.$_SESSION['society_id'].'"';	
		$sql = 'SELECT unit.unit_no, unit.unit_id, member_main.owner_name FROM `unit` JOIN `member_main` on unit.unit_id = member_main.unit WHERE unit.society_id = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function fetchDocuments()
	{
		$sql = "select document_id,document from document where status='Y' order by document_id";
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function get_reg_units($service_prd_reg_id)
	{
		$sql = "SELECT `unit_no` FROM `service_prd_units` WHERE `service_prd_id` ='".$service_prd_reg_id."'";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{				
				$var = explode('[', $res[$k]['unit_no']);				
				$reg_units .= $var[0].', ';
			}			
			echo substr($reg_units,0,20);
		}
	}
	
		public function get_reg_units_societywise($service_prd_reg_id)
		{
		$resArray = array();
		$sql = "SELECT sp.unit_no,sp.society_id,societytbl.society_name FROM `service_prd_units` as sp JOIN `society` as societytbl on sp.society_id = societytbl.society_id WHERE `service_prd_id`='".$service_prd_reg_id."'";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{				
				/*$var = explode('[', $res[$k]['unit_no']);				
				$reg_units .= $var[0].', ';*/
				$var = explode('[', $res[$k]['unit_no']);			
				if (array_key_exists($res[$k]['society_name'],$resArray))
				{
					$resArray[$res[$k]['society_name']] = $resArray[$res[$k]['society_name']].",".$var[0];
				}
				else
				{
					$resArray[$res[$k]['society_name']] = $var[0];
				}
			}			
			//echo substr($reg_units,0,-2);
			return $resArray;
			
			
		}
	}
	
	//*************************SMS For Servider Provider*****************
	
	public function sendServicePrdSMS($SerPrdID, $IsApproved)
	{
		$SPCategory = $this->m_dbConnRoot->select("SELECT c.cat from cat as c JOIN spr_cat as sc ON sc.cat_id = c.cat_id WHERE sc.service_prd_reg_id = '".$SerPrdID."'");

		$Details = "SELECT full_name from service_prd_reg WHERE society_id = '".$_SESSION['society_id']."' AND service_prd_reg_id = '".$SerPrdID."'";
		$DetailsResult  = $this->m_dbConnRoot->select($Details);
						
		$ServicePrdName = $DetailsResult[0]['full_name'];
		
		$smsDetails = $this->m_dbConn->select("SELECT `society_name`, `sms_start_text`,`sms_end_text` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'");
		
		$obj_dbConn= new dbop(false,$dbName);
		$obj_bbConnRoot=new dbop(true)	;
		$obj_Fetch = new FetchData($obj_dbConn,$obj_bbConnRoot);

		$unitDetails = $this->GetEmailIDToSendNotification($SerPrdID, $IsApproved, true);
		
		if($IsApproved == true)
		{
			$msgBody = "".$smsDetails[0]['sms_start_text'].", ".$ServicePrdName."  is Approved as ".$SPCategory[0]['cat']." Service Provider. Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']." ";
		}
		else
		{
			$msgBody = "".$smsDetails[0]['sms_start_text'].", ".$ServicePrdName." as ".$SPCategory[0]['cat']." New Service Provider is Generated. Please login to www.way2society.com to know more details. ".$smsDetails[0]['sms_end_text']." ";
		}
		echo $msgBody;
		//**----Making log file name as SendClassifiedSMS.html to track Classified sms logs ----**
		
		$Foldername = $this->obj_fetch->objSocietyDetails->sSocietyCode;

		if (!file_exists('../logs/import_log/'.$Foldername)) 
		{
			mkdir('../logs/import_log/'.$Foldername, 0777, true);
		}

		
		$Logfile=fopen('../logs/import_log/'.$Foldername.'/SendServiceProviderSMS.html', "a");	
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
	public function sendServicePrdMobileNotification($SerPrdID, $IsApproved)
				
				{
					$Details = "SELECT full_name from service_prd_reg WHERE society_id = '".$_SESSION['society_id']."' AND service_prd_reg_id = '".$Ser_PrdID."'";
					$DetailsResult  = $this->m_dbConnRoot->select($Details);
						
					$ServicePrdTitle = "Service Provider Approved";
					$ServicePrdMassage = $DetailsResult[0]['full_name'];
	
					$dbName = $_SESSION['dbname'];
					$SocietyID = $_SESSION['society_id'];
					
					$obj_dbConn= new dbop(false,$dbName);
					$obj_bbConnRoot=new dbop(true)	;
					$obj_Fetch = new FetchData($obj_dbConn,$obj_bbConnRoot);
					$emailIDList = $this->GetEmailIDToSendNotification($SerPrdID, $IsApproved, false);
							
					for($i = 0; $i < sizeof($emailIDList); $i++)
						{	
						  if(($emailIDList[$i]['email'] <> ""))
						  {
							$unitID = $emailIDList[$i]['unit_id'];
							$objAndroid = new android($emailIDList[$i]['email'], $SocietyID, $unitID);
							$sendMobile = $objAndroid->sendServicePrdNotification($ServicePrdTitle,$ServicePrdMassage,$SerPrdID);
						  }
						}			
				}
						

	public function GetEmailIDToSendNotification($SerPrdID, $Approved, $IsSMS)
	{
		//same function use for notification and sms to get mobile number and emails 
			$UnitDetails = array();
		
		 	if($Approved == true)
		 	{
			 $Details = "SELECT unit_id FROM service_prd_units  WHERE society_id = '".$_SESSION['society_id']."' AND service_prd_id = '".$SerPrdID."'";
			 $DetailsResult  = $this->m_dbConnRoot->select($Details);
			 
			$finalEmailArray = array();
			for($i = 0 ; $i < sizeof($DetailsResult); $i++)
			{
			//Fetching connected unit details for send emails and show in unit		
			 $UnitNumber = $this->m_dbConn->select("SELECT u.unit_id, m.email, u.unit_no, m.mob from unit as u JOIN member_main as m ON u.unit_id = m.unit where u.unit_id = '".$DetailsResult[$i]['unit_id']."' AND m.society_id = '".$_SESSION['society_id']."' AND m.ownership_status = 1");	
			
			$Unit = array('unit_id' => $UnitNumber[0]['unit_id'],'email' => $UnitNumber[0]['email'], 'unit_no' => $UnitNumber[0]['unit_no'],'mob' => $UnitNumber[0]['mob']);
			array_push($UnitDetails,$Unit);	
			}
			
		 }
	
			$CurrentUserDetail = $this->m_dbConn->select("SELECT u.unit_id, m.email, u.unit_no, m.mob from unit as u JOIN member_main as m ON u.unit_id = m.unit where u.unit_id = '".$_SESSION['unit_id']."' AND m.society_id = '".$_SESSION['society_id']."' AND m.ownership_status = 1");
			
			
			for($j = 0; $j < sizeof($CurrentUserDetail); $j++)
			{
				array_push($UnitDetails, $CurrentUserDetail[$j]);
			}
			if($IsSMS == true)
			{
				$SocietyMobile = $this->m_dbConn->select("SELECT `phone2` as mob, `society_code` as unit_no FROM society where society_id ='".$_SESSION['society_id']."'");
				array_push($UnitDetails, $SocietyMobile[0]);
				return $UnitDetails;
				
			}
			else
			{
				$FetchSociety_details = $this->m_dbConnRoot->select("SELECT m.unit_id, l.member_id as email FROM mapping as m JOIN login as l ON m.login_id = l.login_id JOIN profile as p ON m.profile = p.id WHERE  PROFILE_SERVICE_PROVIDER = 1 AND m.society_id = '".$_SESSION['society_id']."' group by l.member_id");
				
				
				for($k = 0; $k < sizeof($FetchSociety_details); $k++)
				{
					array_push($UnitDetails, $FetchSociety_details[$k]);	
				}
				
				return $UnitDetails;
			}
		
	}
	
//***-----------------------------------Send Service Provider Email -------------------------------------------//	
	
	public function Ser_Prv_Approved_email($Ser_PrdID, $Approved)
	{
		//Fetch Society details
		$sqlQuery="select society_name from `society` where society_id='".$_SESSION['society_id']."'";
		$res = $this->m_dbConnRoot->select($sqlQuery);
		 $resUnit = "select * from service_prd_units where service_prd_id='".$Ser_PrdID."'";
		  $searchUnit  = $this->m_dbConnRoot->select($resUnit);
		//Fetch service provider deatial and connected unit
		if($searchUnit <> '')
		{
		  $Details = "SELECT ser.full_name, ser.age, ser.dob, ser.cur_resd_add, ser.cur_con_1, ser.native_add, su.unit_id FROM service_prd_reg as ser JOIN service_prd_units as su ON ser.service_prd_reg_id = su.service_prd_id WHERE ser.society_id = '".$_SESSION['society_id']."' AND ser.service_prd_reg_id = '".$Ser_PrdID."'";
		}
		else
		{
			  $Details = "SELECT ser.full_name, ser.age, ser.dob, ser.cur_resd_add, ser.cur_con_1, ser.native_add FROM service_prd_reg as ser WHERE ser.society_id = '".$_SESSION['society_id']."' AND ser.service_prd_reg_id = '".$Ser_PrdID."'";
		}
		$DetailsResult  = $this->m_dbConnRoot->select($Details);
		
		$unit = array();
		$UnitEmail = array();
		$finalEmailArray = array();
		for($i = 0 ; $i < sizeof($DetailsResult); $i++)
		{
		//Fetching connected unit details for send emails and show in unit		
		 $UnitNumber = $this->m_dbConn->select("SELECT u.unit_no, m.email, m.owner_name as owner_name from unit as u JOIN member_main as m ON u.unit_id = m.unit where u.unit_id = '".$DetailsResult[$i]['unit_id']."' AND m.society_id = '".$_SESSION['society_id']."' AND m.ownership_status = 1");
		
		array_push($unit,$UnitNumber[0]['unit_no']);
		
		$Unit = array('owner_name' => $UnitNumber[0]['owner_name'],'email' => $UnitNumber[0]['email']);
		array_push($UnitEmail,$Unit);
		
		}
		//society details to send email for approval or aknowledgement
		$Society = $this->m_dbConn->select("SELECT email, society_name as owner_name from society where society_id = '".$_SESSION['society_id']."'");
		
		array_push($finalEmailArray,$Society[0]);
		$CurrentUserDetails = $this->m_dbConnRoot->select("SELECT member_id from login where login_id = '".$_SESSION['login_id']."'");
		
		//unit detais who created or approved 
		$NewArray = array('owner_name' => $_SESSION['name'],'email' => $CurrentUserDetails[0]['member_id']);
		
		array_push($finalEmailArray,$NewArray);

		//Calculating age here with date of birth
		$d1 = new DateTime($DetailsResult[0]['dob']);
		$d2 = new DateTime(date("Y-m-d"));
		$diff = $d2->diff($d1);

		//making string of connected unit
		$ConnectedUnit = implode(", ",$unit);
		
		//service provider category detais
		$Category_id = "SELECT c.cat from cat as c JOIN spr_cat as spc ON c.cat_id = spc.cat_id WHERE spc.service_prd_reg_id = '".$Ser_PrdID."' AND c.status = 'Y'";
		
		$categoryResult = $this->m_dbConnRoot->select($Category_id);
			
		//echo $res[0]['society_name'];
		date_default_timezone_set('Asia/Kolkata');
		
		if($Approved == false)
		{
			$mailSubject = "[Service Provider  #".$categoryResult[0]['cat']."]  For Approval";
		}
		else if(true)
		{
			$mailSubject = "[Service Provider Approved #".$categoryResult[0]['cat']."]";
		}
		$DBdate = new DateTime($date);
		$new_date_format = $DBdate->format('d-m-Y H:i:s');
		
		
		if($Approved == false)
		{
			$userURL = 'https://way2society.com/service_prd_reg_view.php?srm';
			//$userURL = 'http://localhost/beta_awsamit/addclassified.php?edt&id='.$title.'';
			$SendEmail = $finalEmailArray;
			$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Title : "'.$categoryResult[0]['cat'].'" New  Service Provider  </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Created By</b></td><td style="width:5%;border-left:none;"></td><td style="width:60%;">'.$_SESSION['name'].'<br>'.$new_date_format.'</td></tr>
							<tr><td style="border-right:none;"><b>Name </b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['full_name'].'</td></tr>
    						<tr><td style="border-right:none;"><b>Age</b></td><td style="border-left:none;"></td><td>'.$diff->y.'</td></tr>
							<tr><td style="border-right:none;"><b>Date of Birth</b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['dob'].'</td></tr>
							<tr><td style="border-right:none;"><b>Contact No.</b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['cur_con_1'].'</td></tr>
							<tr><td style="border-right:none;"><b>Unit Connected</b></td><td style="border-left:none;"></td><td>'.$ConnectedUnit.'</td></tr>
							</table><br />'	;
							
		
					$mailBody .="You may view or Approve this service provider by copying below link to browser or by clicking here<br />".$userURL;					
			
							
							
		}
		else if($Approved  == true)
		{	
		$mailBody = '<table border="black" style="border-collapse:collapse;" cellpadding="10px">
							<tr> <td colspan="3"> <b>Title : "'.$categoryResult[0]['cat'].'" service provider is Approved  </b> </td></tr>   							
							<tr> <td style="width:30%;border-right:none;"><b>Approved By</b></td><td style="width:5%;border-left:none;"></td><td style="width:60%;">'.$_SESSION['name'].'<br>'.$new_date_format.'</td></tr>
							<tr><td style="border-right:none;"><b>Name </b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['full_name'].'</td></tr>
    						<tr><td style="border-right:none;"><b>Age</b></td><td style="border-left:none;"></td><td>'.$diff->y.'</td></tr>
							<tr><td style="border-right:none;"><b>Date of Birth</b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['dob'].'</td></tr>
							<tr><td style="border-right:none;"><b>Contact No.</b></td><td style="border-left:none;"></td><td>'.$DetailsResult[0]['cur_con_1'].'</td></tr>
							<tr><td style="border-right:none;"><b>Unit Connected</b></td><td style="border-left:none;"></td><td>'.$ConnectedUnit.'</td></tr>
							</table><br />'	;	
							$SendEmail = array_merge($finalEmailArray, $UnitEmail);
		}
		
				$EMailID = "";
				$Password = "";
				$EMailIDToUse = $this->obj_utility->GetEmailIDToUse(false, 0, 0, 0, 0, $_SESSION['dbname'], $_SESSION['society_id'], 0, 0);
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password'];
				//$EMailID ="sujitkumar0304@gmail.com";
				//$Password = "sujit0304";
				for($i = 0 ; $i < sizeof($SendEmail) ; $i++)
				{
					$email = $SendEmail[$i]['email'];
					$name = $SendEmail[$i]['owner_name'];
					if($email <> '')
					{
						try
						{	
						
						//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
						//->setUsername($EMailID)
						//->setSourceIp('0.0.0.0')
						//->setPassword($Password) ; 
						//AWS Config
				
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
	
	
	public function fetchServiceProvider()
	{
		//ftechinf list of service provider from common database
		$sql1 = "select sp.service_prd_reg_id, sp.full_name,sp.father_name,sp.father_occ,sp.mother_name,sp.mother_occ,sp.hus_wife_name,sp.hus_wife_occ,sp.son_dou_name,sp.son_dou_occ,sp.other_name,sp.other_occ,sp.marry as married,sp.cur_con_1,sp.cur_con_2,sp.since,sp.photo, sp.photo_thumb, sp.age,sp.cur_resd_add, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat,sp.identy_mark,sp.education,sp.native_add,sp.native_con_1,sp.native_con_2 from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$sql1 .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			//add condition to fetch service provider category wise
			foreach($_REQUEST['cat_id'] as $k => $v)
			{
				$cat_id0 .= $v.',';
			}
			$cat_id = substr($cat_id0,0,-1);
			$sql1 .= " and sc.cat_id in (".$cat_id.")";
		}
		if($_REQUEST['key']<>"")
		{
			$sql1 .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		$sql1 .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		$result=$this->m_dbConnRoot->select($sql1);
		return $result;
	}
function reg_UniqeID($societyId)
{
	//$sql= "Select MAX(society_staff_id) as StaffID from service_prd_society where 	society_id = '".$societyId."'";
	$sql= "SELECT society_staff_id as StaffID FROM `service_prd_society` where status ='Y' and society_id = '".$societyId."' order by sp_id desc LIMIT 1";
	$result=$this->m_dbConnRoot->select($sql);
	return $result;
}
}
?>
