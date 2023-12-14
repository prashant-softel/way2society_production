
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Photo Gallery</title>
</head>



<?php include_once("includes/head_s.php");
//include_once("classes/client.class.php");
include_once("classes/dbconst.class.php");
?>
<head>
<script tytpe="text/javascript" src="js/ajax_new.js"></script>
<script>
function tabImgClicked(sFileImage)
{

	//alert("tabimg");	
	document.getElementById('show').innerHTML = 'Fetching list. Please wait ...';
	var obj = {};
	remoteCallNew(sFileImage, obj, 'setHome');
}

function setHome()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('show').innerHTML = sResponse;

}
</script>
</head>

<body>
<div id="show" >
<a herf="">Manage Group</a>
</div>
</body>
<!--<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
--> 
    <!--<div class="panel-heading" style="font-size:20px">
         Galleries
    </div>
    <div class="panel-body">                        
        <div class="table-responsive">
             <table cellspacing="5">
            <tr><td>
            <img src="images/holi.jpg" style="max-height:100%; max-width:100%"/>
            </td>
            <td>
            <img src="images/diwali.jpg" style="max-height:100%; max-width:100%" />
            </td>
            <td>
            <img src="images/ganesh.jpg" style="max-height:100%; max-width:100%"/>
            </td>
            </tr>
            <tr><td>Holi
            </td><td>Diwali</td><td>Ganesh Chaturthi</td></tr>
            </table>
        </div>
        <div class="panel-footer">
        Print
        </div>        
</div>
-->


<?php include_once "includes/foot.php"; ?>
<script>
	tabImgClicked('show_gallery.php');
</script>