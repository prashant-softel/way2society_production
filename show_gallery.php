<?php 
//echo '123';

if(!isset($_SESSION)){ session_start(); } 

//include_once "ses_set_s.php";
//include_once("includes/head_s.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/show_gallery.class.php");?> 
<?php
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_show=new show_album($m_dbConn,$m_dbConnRoot);
//print_r($_SESSION);

?>
<html>
<head>

<link rel="stylesheet" href="css/style_gallery.css"/>

<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/upload_index.js"></script>
    
    
</head>


<body>


<br>
<br>
<br>
<div id="middle">

<br>

<div class="panel panel-info" style="margin-top:0%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center">
         Photo Gallery</div>
<br>
<div  class="col-md-12">
<div class="col-md-6"></div>

<?php 

						  if($_SESSION['login_id'] == 4)
	
						  {
							  ?>
<div class="col-md-3">
<a href="gallery_group.php" style="text-decoration:none; float:right; font-size:14px;">
                            <button type="button" class="btn btn-info btn-circle" title="Manage Groups"  style="font-size:10px;font-size:1.75vw;width:2vw;height:2vw">
                            <i class="fa fa-group  " style="font-size:10px;font-size:1.25vw"></i>
                           </button>&nbsp;Manage Groups</a></div>
                           
<?php }?>
              

	
	<?php if($_SESSION['profile'][PROFILE_CREATE_ALBUM] == "1" || $_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_MANAGER)
	{ ?>   
<div class="col-md-3">     
 <a href="gallery_upload.php" style="text-decoration:none; float:right; font-size:14px;">
                            <button type="button" class="btn btn-info btn-circle" title="Upload Images"  style="font-size:10px;font-size:1.75vw;width:2vw;height:2vw">
                            <i class="fa fa-upload   " style="font-size:10px;font-size:1.25vw"></i>
                           </button>&nbsp;Manage Album</a>
                           </div>
     <?php } ?>
                           </div>
                           <br><br><br><br>
		<!--<div id="body2" style="    margin-top: 4.5%;" class="col-md-12">-->
			<form method="" enctype="multipart/form-data" action="process/show_gallery.process.php">
  			<?php
				
				
				$query="SELECT society.society_name,group.group_name,album.`name`,album.`id`,album.`folder` FROM `soc_group` JOIN `society` ON (society.society_id = soc_group.society_id) JOIN `group` ON (group.group_id = soc_group.group_id) join `album` on(soc_group.group_id=album.group_id) where society.society_id=".$_SESSION['society_id']."";
	 			$res1 = $m_dbConnRoot->select($query);
				$query1="SELECT society_name as 'society_name',society_name as 'group_name',album.`name`,album.`id`,album.`folder` FROM album  JOIN `society` ON (society.society_id = album.group_id) where society.society_id=".$_SESSION['society_id']."";
	 			$res2 = $m_dbConnRoot->select($query1);
				//var_dump($res1);
				//var_dump($res2);
				if(empty($res1))
				{
					$res = $res2;
				}
				if(empty($res2))
				{
					$res = $res1;
				}
				else
				{
				$res = array_merge($res1,$res2);
				}
				//var_dump($res);
				//die();
				for($i=0;$i<sizeof ($res);$i++)
				 {
					 
		 	$albumsql ="SELECT count(*) as cnt FROM `photos` WHERE `album_id`='".$res[$i]['id']."' ";
				$resCount = $m_dbConnRoot->select($albumsql);
					 
					 $album_id=$res[$i]['id'];
					 $album_name=$res[$i]['name'];
					  $folder_name=$res[$i]['folder'];
					 
						$query1="SELECT `url` FROM `photos` WHERE `album_id`='".$res[$i]['id']."' ";
						$res1 = $m_dbConnRoot->select($query1);
						
						//$run1= mysqli_fetch_array($res);
						$pic=$res1[0]['url'];
						
						
						//echo $album_name;
						?>
			 <?php /*?><!-- <a href="#" onClick="tabImgClicked('show_photo.php?id=<?php echo  $album_id?>');">--><?php */?>
			  <a href="show_photo.php?id=<?php echo $album_id?>">
				<div id="view_box" style="width:170px; height:170px;">
				  <?php if($pic<>'')
	{?>
    <center><img src="uploads/<?php echo $folder_name.'/thumb/'.$pic;?>" style="height: 115px;
 width: auto "></center>
    
	<?php }
	else
	{?>
    <center><img src="uploads/no-photo-grey_1x.png" style="width:auto; height:auto;"></center>
   <?php  }?>
   <br>
				<div style="float:left;font-size: 12px;width: 115px; text-align: left;"> <b><?php echo  substr($album_name ,0,20);?></b></div><div style="float:right; font-size:12px;"><b>
                <div style="float:right;margin-right: 12px;margin-top:-3px">
                <div  style="font-size:10px;font-size:1.75vw;width: 20px;height: 21px;">
                            <i class="fa  fa-camera " style="font-size:20px;"></i>
                            </div>
                            </div>
                            <div style="float: right;margin-top: -12px;margin-right: -34px;"><img src="images/icon.png" style="float: right;width: 25px;height: 25px;margin-top: -1px;"><b style="float: right;font-size: 10px;margin-top: 8%;margin-right: -50%;color:#fff;"><?php echo $resCount[0]['cnt'];?></b></div></div>
				
				</div>
                
<!--<a  href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>"><img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;/"></a>
-->
				
				<?php }?>
                
			<div class="clear"></div>

</div></div></div></form></center>
<body>
</html>