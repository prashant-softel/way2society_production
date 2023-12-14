<?php 

include_once("includes/head_s.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/upload_index.class.php");?> 
<?php
$m_dbConnRoot = new dbop(true);
$obj_show=new show_album($m_dbConn,$m_dbConnRoot);

?>
<!doctype html>
<html>
<head>

<link rel="stylesheet" href="css/style_gallery.css">
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery.fancybox-1.3.4.css" />

<meta charset="utf-8">
<style type="text/css">
    a.fancybox img {
        border: none;
        box-shadow: 0 1px 7px rgba(0,0,0,0.6);
        -o-transform: scale(1,1); -ms-transform: scale(1,1); -moz-transform: scale(1,1); -webkit-transform: scale(1,1); transform: scale(1,1); -o-transition: all 0.2s ease-in-out; -ms-transition: all 0.2s ease-in-out; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out;    } 
    a.fancybox:hover img {
        position: relative; z-index: 999; -o-transform: scale(1.03,1.03); -ms-transform: scale(1.03,1.03); -moz-transform: scale(1.03,1.03); -webkit-transform: scale(1.03,1.03); transform: scale(1.03,1.03);
    }
/** {
    -webkit-box-sizing: none;
    -moz-box-sizing: none;
    box-sizing: none; 
}*/
#fancybox-content { 
    box-sizing:inherit;
	 -webkit-box-sizing:content-box;
}
</style>
<title>Untitled Document</title>
<script type="text/javascript" src="lib/jquery-1.11.0.min.js"></script>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript" src="lib/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="lib/jquery.fancybox-1.3.4.pack.min.js"></script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId        : '570093403199583',
            channelUrl   : '', //opional
            status       : true, // check login status (we don't make use of this)
            cookie       : true, // enable cookies to allow the server to access the session
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
	
</script>
<script>
function sharephoto(imgurl)
{
 imgurl = 'http://way2society.com'+imgurl;	
  FB.ui(
  {
    method: 'share',
    name: 'Facebook Dialogs',
	href: 'http://way2society.com/',
    link: 'http://way2society.com/',
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


<script type="text/javascript">
    $(function($){
        var addToAll = false;
        var gallery = true;
        var titlePosition = 'inside';
        $(addToAll ? 'img' : 'img.fancybox').each(function(){
            var $this = $(this);
            var title = $this.attr('title');
            var src = $this.attr('data-big') || $this.attr('src');
            var a = $('<a href="#" class="fancybox"></a>').attr('href', src).attr('title', title);
            $this.wrap(a);
        });
        if (gallery)
            $('a.fancybox').attr('rel', 'fancyboxgallery');
        $('a.fancybox').fancybox({
            titlePosition: titlePosition
        });
    });
    $.noConflict();
</script>
</head>
<?php


 $album_id=$_GET['id'];
 
 $queryFolder = "SELECT `name`,`folder` FROM `album` WHERE `id`='$album_id'";
  		//echo $queryFolder;
		$resFolder = $m_dbConnRoot->select($queryFolder);
 
		$foldername=$resFolder[0]['folder'];
		$albumname=$resFolder[0]['name'];
		
		$query="SELECT `url`,`id` FROM `photos` WHERE `album_id`='$album_id'";
 		$res = $m_dbConnRoot->select($query);
		?>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9&appId=570093403199583";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="panel panel-info" style="margin-top:4%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
         <?php echo $albumname?></div>
<br>
<div id="body1" style="margin-top: 5.5%;">

        <button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>

<div id="container">
    
   
       <!-- <div style="font-size:15px; font-weight:bold;margin-top: -10px;color: blue;float: left; margin-left: 290px;">Album Name:&nbsp;&nbsp;&nbsp;<br></div><br><br>-->
  <br><br>      
        <?php
for($i=0;$i<sizeof( $res);$i++)

 //while($run=$res)
 {
	 
	 $name=$res[$i]['name'];
	 $url=$res[$i]['url']; 
	// $res = $this->m_dbConnRoot->select($query);

?>
<div id="view_box1" style="width:170px; height:170px;">
<!--<a href="uploads/<?php //echo $foldername.'/'.$url;?>"  class="fancybox"><img  src="uploads/<?php //echo $foldername.'/thumb/'.$url ?>"  style= "height: 112px; width: auto;"/></a>
--><a href="uploads/<?php echo $foldername.'/'.$url;?>" class="fancybox"><img  src="uploads/<?php echo $foldername.'/thumb/'.$url?>" style= "height: 112px; width: auto;" /></a>
<br>
<b><?php echo $name ;?></b>
<br>
<div class="fb-like" data-href="https://way2society.com/uploads/'<?php echo '/uploads/'.$foldername.'/'.$url; ?>'" data-width="200" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="false"></div>

<a  class ='share_button' id="share_button" onClick="sharephoto('<?php echo '/uploads/'.$foldername.'/'.$url; ?>') "  data-folder = "<?php echo $foldername; ?>" data-img = "<?php echo $url; ?>"  href="#" >
<img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;"/></a>

<!--<a  href="https://www.facebook.com/sharer/sharer.php?u=<?php //echo urlencode($url); ?>"><img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;/"></a>
-->
</div>

<?php }?>
<div class="clear"></div>
<!--<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
-->
<!--<div style="float:left; font-size:15px;"><a href="Gallery.php"> Back </a></div>-->
</div>

</div>
</body>
</html>
<?php include_once "includes/foot.php"; ?>