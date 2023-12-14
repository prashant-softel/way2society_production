
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Photo Gallery</title>
</head>




<?php include_once("includes/head_s.php");
//include_once("classes/client.class.php");
include_once("classes/dbconst.class.php");
?>
<?php
include_once("classes/upload_index.class.php");

$m_dbConnRoot = new dbop(true);
$obj_upload=new show_album($m_dbConn,$m_dbConnRoot);
$UnitBlock = $_SESSION["unit_blocked"];
?>

<!doctype html>
<html>
<head>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<link rel="stylesheet" href="css/style_gallery.css">
<link rel="stylesheet" type="text/css" href="css/pagination.css">

<script tytpe="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/gallery_upload20190625.js"></script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId        : '570093403199583',
            channelUrl   : '', //opional
            status       : true, // check login status (we don't make use of this)
           // cookie       : true, // enable cookies to allow the server to access the session
            xfbml        : true  // parse XFBML
        });
    };

    // Load the SDK Asynchronously
    (function(d){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);           
    }(document));
	
	var sError = '';
	var sError1 = '';
	//showLoader();
	
	//$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
			window.location.href='suspend.php';
		}
    //});
	
</script>
<script>
function sharephoto(imgurl)
{
 imgurl = 'https://way2society.com'+imgurl;	
  FB.ui(
  {
    method: 'share',
    name: 'Facebook Dialogs',
	href: 'https://way2society.com/',
    link: 'https://way2society.com/',
    picture: imgurl,
    caption: 'Reference Documentation',
    description: ''
  },
  function(response) {
    if (response && response.post_id) {
      alert('Post was published.');
    } else {
      //alert('Post was not published.');
    }
  }
);
}
</script>
<script>
function myFunction() {
	 var x = document.getElementById("action").value;
    document.getElementById("upload").innerHTML
	
	if(x == "add_photo")
	{
	
		var album_id = document.getElementById('album_id').value;
		tabBtnClicked('upload.php?id=' + album_id);
		
	}
	if(x == "cover")
	{	
		var cover=document.getElementById('album_id').value;
		tabBtnClicked('view.php?id=' + cover + '&radio=1 & share=0 & action=' +x);		
	}
	if(x == "del_photo")
	{
		var cover=document.getElementById('album_id').value;
		tabBtnClicked('view.php?id=' + cover + '&photo=1 & share=0 & action=' +x);	
	}
	
	if(x == "del")
	{
		var del=document.getElementById('album_id').value;
		tabBtnClicked('upload_index.php?id=' + del + '&album_del=1 & share=0 & action=' +x);
	}
	if(x == "send")
	{
		var send=document.getElementById('album_id').value;
		tabBtnClicked('view.php?id=' +send + '&check=1 & share=0 & action=' +x);
		//window.location.href = 'http://comnetcs.net/way2society_new/wordpress/';
	}
}


</script>
<script>
function fbs_click(TheImg) {
     u=TheImg.src;
     // t=document.title;
    t=TheImg.getAttribute('src');
    window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;
}
function tabBtnClicked(sFileName,id)
{
	//alert(sFileName);
	//alert('tabBtnClicked');
	showLoader();

	//document.getElementById('show').innerHTML = '<div style="float: left; margin-top: 200px; margin-left: 500px; font-size: 25px; font-weight: bold;">Please Wait....</div>';

	var obj = {};
	remoteCallNew(sFileName, obj, "setHome('"+id+"')");
	
	
}

function setHome(id)
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('show').innerHTML = sResponse;
	
	if(sError.length > 0)
	{
		if(document.getElementById('ErrorDiv') != null)
		{
			document.getElementById('ErrorDiv').innerHTML = sError;
		}
	}
	if(sError1.length > 0)
	{
		if(document.getElementById('ErrorDiv1') != null)
		{
			document.getElementById('ErrorDiv1').innerHTML = sError1;
		}
	}
	//alert(id);
	/*document.getElementById("btnHome").className = "";
	document.getElementById("btnCreate").className = "";
	document.getElementById("btnUpload").className = "";
	document.getElementById("btnHomePage").className = "";
	
	document.getElementById(id).className ="active";*/
	//var id2 = $(id).closest('li').attr('id');
	$('.nav-tabs li.active').removeClass('active'); 
	//alert(id);
	//$(id).addClass('active');
	if(id=='undefined')
	{
	 document.getElementById('btnHome').className ="active";	
	}
	else
	{
		document.getElementById(id).className ="active";
	}
	hideLoader();
}
function valTest()
{
	//return false;
	//alert('hi');
	var albumName=document.getElementById('album').value;
//var oFile = document.getElementById('img').files[0];
if(albumName=="")
{
 document.getElementById('ErrorDiv2').style.display='block';
  document.getElementById('ErrorDiv2').innerHTML ="Please select Album Name";
  return false; 	 
}
 
}


function tabImgClicked(sImage)
{
	//document.getElementById('show').innerHTML = '<div style="float: left; margin-top: 200px; margin-left: 500px; font-size: 25px; font-weight: bold;">Please Wait....</div>';
	
	var obj = {};
	remoteCallNew(sImage, obj, 'setPhoto');
		
}	
function setPhoto()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('show').innerHTML = sResponse;
	hideLoader();
}

function valGroup()
{
	
	var groupName=document.getElementById('group').value;
	var albumName=document.getElementById('name').value;
//var oFile = document.getElementById('img').files[0];
//alert (groupName);
if(groupName=="")
{
 document.getElementById('ErrorDiv3').style.display='block';
  document.getElementById('ErrorDiv3').innerHTML ="Please select Group Name / If Group is not already created kindly create Group from Manage Group";
  return false; 	 
}
if(albumName=="")
{
 document.getElementById('ErrorDiv3').style.display='block';
  document.getElementById('ErrorDiv3').innerHTML ="Please Enter the Album Name";
  return false; 	 
}

}
</script>

<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body><div class="panel panel-info" style="margin-top:0%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px; text-align:center">
         Photo Gallery</div><?php //include 'title_bar.php';?>
<div  class="col-md-12">

<?php if($_SESSION['login_id'] == 4) 
{ ?>

<div class="col-md-6"></div>
<div class="col-md-3"></div>
<div class="col-md-3">
<a href="gallery_group.php" style="text-decoration:none; float:right; font-size:14px;margin-top: 20px;
    margin-right: 0px;">
                            <button type="button" class="btn btn-info btn-circle" title="Manage Groups"  style="font-size:10px;font-size:1.75vw;width:2vw;height:2vw">
                            <i class="fa fa-group  " style="font-size:10px;font-size:1.25vw"></i>
                           </button>&nbsp;Manage Groups</a></div>
<?php } ?>
                           

<div id="show"></div>
<div id="fetch" style="float: left; margin-top: 200px; margin-left: 500px; font-size: 25px; font-weight: bold; display:block;"></div>
</div>

</body>
</html>
<script>
	<?php
		if(!isset($_REQUEST['pg']) || $_REQUEST['pg'] == 0)
		{
			?>
				sError = '';
				tabBtnClicked('upload_index.php','btnHome');
			<?php
		}
		else if($_REQUEST['pg'] == 1)
		{
			?>
				sError = '';
				tabBtnClicked('upload_index.php','btnHome');
			<?php
		}
		else if($_REQUEST['pg'] == 2)
		{
			?>
			
				sError = '<?php echo $_REQUEST['error'] ?>';
				sError1 = '<?php echo $_REQUEST['error1'] ?>';
				//alert(sError);
				tabBtnClicked('upload.php?id=<?php echo $_REQUEST['album'] ?>&ShowData=<?php echo $_REQUEST['ShowData'] ?>','btnUpload');
			<?php
		}
		else if($_REQUEST['pg'] == 3)
		{
			?>
			tabBtnClicked('create.php?group_id=<?php echo $_REQUEST['group_id']?>','btnCreate');
		<?php
		}
		else if($_REQUEST['pg'] == 5)
		{
			?>
			tabBtnClicked('upload_index.php?grp=<?php echo $_REQUEST['grp']?>','btnHome');
		<?php
		}
		else if($_REQUEST['pg'] == 4)
		{ 
		include_once("classes/include/config_normal_wp.php");
		//include_once("../wordpress/wp-config.php");
		//include_once("../wordpress/wp-includes/wp-db.php");
		global $wpdb;
		
		
		$baseDir_for_delete = dirname( dirname(__FILE__) );
//$baseDir = dirname(dirname(__FILE__));
$baseDir2 = $baseDir_for_delete.'/beta/wp-content/uploads/photo-gallery'; 
//echo $baseDir2. "<br>";
//echo $baseDir_for_delete.'\wordpress\wp-content\uploads\photo-gallery';
 if(isset($_POST['delete'])) {
	//print_r($_POST['check']);
	  foreach($_POST['check'] as $delete_img)
	  {      
	  $delete_img= str_replace('\\','/', $delete_img);
		  $file =  $baseDir2.$delete_img;
		//echo $file. "<br>";
		 $image_name = str_replace('/thumb/', '', $delete_img);
		$outer_img_file =  $baseDir2.'/'.$image_name;
		 $sql = "DELETE FROM `wp_bwg_image` where `thumb_url`='".$delete_img."'";
		 $r_00 = mysql_query($sql );
		 // $wpdb->delete( 'wp_bwg_image', array('thumb_url' => $delete_img ) );
		  unlink($file);
		  unlink($outer_img_file);
	   }
	   }

			?>
			//alert("hi");
				tabBtnClicked('homepage_images.php','btnHomePage');
			<?php
}  
  ?>
	
</script>
<?php include_once "includes/foot.php"; ?>