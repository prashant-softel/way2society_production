
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Documents View</title>
</head>



<?php
include_once("classes/include/check_session.php");

	//echo "loading gdrive documents";
	 //include_once "ses_set_s.php"; 
if(!isset($_REQUEST["Mode"]))
{
	$_REQUEST["Mode"] = "1";
	header("Location: https://www.way2society.com/GDrive_view.php?Mode=1");
}
else
{

}
$sViewMode = $_REQUEST['Mode'];
$sUnitID = "ALL";

if(isset($_REQUEST['UID']))
{
	$sUnitID = $_REQUEST["UID"];

}
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

include_once("classes/utility.class.php");
include_once("classes/ChequeDetails.class.php");
include_once("classes/CDocumentsUserView.class.php");
$objUtility = new utility($m_dbConn);
$obj_ChequeDetails = new ChequeDetails($m_dbConn);
include_once("GDrive.php");
?>

<link href="styles/default/default.css" rel="stylesheet" type="text/css" media="screen"/>
		
		<!-- Makes the file tree(s) expand/collapsae dynamically -->
		<!-- <script src="js/jquery_min.js" type="text/javascript"></script> -->
		<script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
		<script src="js/php_file_tree.js" type="text/javascript"></script>	

<style type="text/css">
.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('https://media.giphy.com/media/y1ZBcOGOOtlpC/giphy.gif') 50% 50% no-repeat rgb(249,249,249);
    opacity: .8;
}
/* PHP File Tree Default Theme

	By Cory LaViska (http://abeautifulsite.net/)
	Featuring the Silk Icon Set from famfamfam (http://www.famfamfam.com/lab/icons/silk/)

*/

.php-file-tree {
	font-family: Georgia;
	font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
	font-size: 12px;
	letter-spacing: 1px;	
	line-height: 1.5;
	text-align: left;
}
ul, menu, dir {
    display: block;
    list-style-type: disc;
    -webkit-margin-before: 1em;
    -webkit-margin-after: 1em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
    -webkit-padding-start: 40px;
    margin-left: 3%;
}
	.php-file-tree A {
		color: #000000;
		text-decoration: none;
	}
	
	.php-file-tree A:hover {
		
		color: #666666;
	}

	.php-file-tree .open {
		all:unset;
		font-style: italic;
	}
	
	.php-file-tree .closed {
		
		font-style: normal;
	}
	
	.php-file-tree .pft-directory {

		list-style-image: url(styles/default/images/directory.png);
		
	}
	
	/* Default file */
	.php-file-tree LI.pft-file { list-style-image: url(styles/default/images/file.png); }
	/* Additional file types */
	.php-file-tree LI.ext-3gp { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-afp { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-afpa { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-asp { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-aspx { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-avi { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-bat { list-style-image: url(styles/default/images/application.png); }
	.php-file-tree LI.ext-bmp { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-c { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-cfm { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-cgi { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-com { list-style-image: url(styles/default/images/application.png); }
	.php-file-tree LI.ext-cpp { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-css { list-style-image: url(styles/default/images/css.png); }
	.php-file-tree LI.ext-doc { list-style-image: url(styles/default/images/doc.png); }
	.php-file-tree LI.ext-docx { list-style-image: url(styles/default/images/docx.png); }
	.php-file-tree LI.ext-exe { list-style-image: url(styles/default/images/application.png); }
	.php-file-tree LI.ext-gif { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-fla { list-style-image: url(styles/default/images/flash.png); }
	.php-file-tree LI.ext-h { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-htm { list-style-image: url(styles/default/images/html.png); }
	.php-file-tree LI.ext-html { list-style-image: url(styles/default/images/html.png); }
	.php-file-tree LI.ext-jar { list-style-image: url(styles/default/images/java.png); }
	.php-file-tree LI.ext-jpg { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-jpeg { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-js { list-style-image: url(styles/default/images/script.png); }
	.php-file-tree LI.ext-lasso { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-log { list-style-image: url(styles/default/images/txt.png); }
	.php-file-tree LI.ext-m4p { list-style-image: url(styles/default/images/music.png); }
	.php-file-tree LI.ext-mov { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-mp3 { list-style-image: url(styles/default/images/music.png); }
	.php-file-tree LI.ext-mp4 { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-mpg { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-mpeg { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-ogg { list-style-image: url(styles/default/images/music.png); }
	.php-file-tree LI.ext-pcx { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-pdf { list-style-image: url(styles/default/images/pdf.png); }
	.php-file-tree LI.ext-php { list-style-image: url(styles/default/images/php.png); }
	.php-file-tree LI.ext-png { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-ppt { list-style-image: url(styles/default/images/ppt.png); }
	.php-file-tree LI.ext-pptx { list-style-image: url(styles/default/images/pptx.png); }
	.php-file-tree LI.ext-psd { list-style-image: url(styles/default/images/psd.png); }
	.php-file-tree LI.ext-pl { list-style-image: url(styles/default/images/script.png); }
	.php-file-tree LI.ext-py { list-style-image: url(styles/default/images/script.png); }
	.php-file-tree LI.ext-rb { list-style-image: url(styles/default/images/ruby.png); }
	.php-file-tree LI.ext-rbx { list-style-image: url(styles/default/images/ruby.png); }
	.php-file-tree LI.ext-rhtml { list-style-image: url(styles/default/images/ruby.png); }
	.php-file-tree LI.ext-rpm { list-style-image: url(styles/default/images/linux.png); }
	.php-file-tree LI.ext-ruby { list-style-image: url(styles/default/images/ruby.png); }
	.php-file-tree LI.ext-sql { list-style-image: url(styles/default/images/db.png); }
	.php-file-tree LI.ext-swf { list-style-image: url(styles/default/images/flash.png); }
	.php-file-tree LI.ext-tif { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-tiff { list-style-image: url(styles/default/images/picture.png); }
	.php-file-tree LI.ext-txt { list-style-image: url(styles/default/images/txt.png); }
	.php-file-tree LI.ext-vb { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-wav { list-style-image: url(styles/default/images/music.png); }
	.php-file-tree LI.ext-wmv { list-style-image: url(styles/default/images/film.png); }
	.php-file-tree LI.ext-xls { list-style-image: url(styles/default/images/xls.png); }
	.php-file-tree LI.ext-xlsx { list-style-image: url(styles/default/images/xlsx.png); }
	.php-file-tree LI.ext-xml { list-style-image: url(styles/default/images/code.png); }
	.php-file-tree LI.ext-zip { list-style-image: url(styles/default/images/zip.png); }
	/* You can add millions of these... */


</style>
<script type="text/javascript">
$(window).load(function() {
    $(".loader").fadeOut("slow");
});
</script>
<div class="loader"></div>
<div id="page-wrapper" style="margin-top:6%;margin-left:5.5%; border:none;width:70%">
	<div class="row">
    <div class="col-lg-12">
		<div class="panel panel-default">
        <div class="panel-heading" style="font-size:20px">
            <span style="margin-left: 10%">Documents</span>
            <?php
           
            if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN )
            {
            ?>
            <input type="button" name="AddDoc" class="btn btn-primary" onclick="window.location.href='Documents.php'" style="float: right;" value="Add New"/>
            <?php
        	}
        	?>
        </div>
        
            <!-- <div class="panel panel-default"> -->
                
                <!-- /.panel-heading -->
                <div class="panel-body">
                	<table><tr>
                		
                    	<?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      				{?>
	      					<td style="vertical-align: middle;">
	      					<span style="vertical-align: middle;">Choose Unit</span>
                    	
                    <select name="UnitLedger" id="UnitLedger">		
                    	<option>Please Select</option>
                    	<option>All</option>
				
                    <?php 

					$memberIDS = $objUtility->getMemberIDs($_SESSION['default_year_end_date']);	
					 echo $objUtility->comboboxEx("select led.id as id,concat_ws(' - ',led.ledger_name,mem.owner_name) as ledger_name from ledger as led JOIN unit as unittable on led.id=unittable.unit_id JOIN member_main as mem  on mem.unit=unittable.unit_id where receipt='1' and led.society_id=".$_SESSION['society_id']." and led.categoryid=".DUE_FROM_MEMBERS." and  mem.member_id IN (".$memberIDS.") ORDER BY unittable.sort_order ASC", false); 
					?>
					
                    
                    </select>

                    <input type="button" id="Go" name="Go" value="Go" style="width: 50px" onclick="ShowDocsForSelectedUnit()" />
                    </td>
<td style="width: 50%">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills" style="visibility: hidden;">
                        <li <?php if(!isset($_REQUEST["Mode"]) || $_REQUEST["Mode"] == "1"){echo 'class="active"';} ?> ><a href="GDrive_view.php?Mode=1">Units Wise</a>
                        </li>
                        <?php
                        $BShow = 0; 
                        if($_SESSION['role'] != ROLE_MEMBER && $BShow == 1)
                        {
                        ?>
                        <li  <?php if($_REQUEST["Mode"] == "2"){echo 'class="active"';} ?> > <a href="GDrive_view.php?Mode=2">Documents Wise</a>
                        </li>
                        <?php
                    	}
                    	?>
                    </ul>
                    </td>
                    <?php } ?>
                    <!-- Tab panes -->
                   <!--  <div class="tab-content">
                        <div class="tab-pane fade in active" id="home-pills">
                            <h4>Home Tab</h4>
                            <p>home</p>
                        </div>
                        <div class="tab-pane fade" id="profile-pills">
                            <h4>Profile Tab</h4>
                            <p>Profile.</p>
                        </div>
                    </div> -->
                </tr>
            </table>
        </div>
                <!-- /.panel-body -->
            <!-- </div> -->
            <!-- /.panel -->
        
 <div class="panel-body">
<!--<div  style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div style="font-size:20px;margin-left: 10%" >
         Documents
    </div>
    
    <form id="gdrive" method="POST">   
     <div class="panel-body">                        
        <div class="table-responsive"> -->

<?php
	$objUtility = new utility($m_dbConn);
	$bTrace = 0;
	if($bTrace)
	{
		print_r($m_dbConn);
	}
	$res = $objUtility->GetGDriveDetails();
	if($bTrace)
	{
		print_r($res);
	}
	$rootid = $res[0]['GDrive_W2S_ID'];

	$UnitID = $_SESSION["unit_id"];
	if(isset($_REQUEST["UID"]))
	{
		$UnitID = $_REQUEST["UID"];
		//echo "new UID".$UnitID;
	}
	//$UnitID = "";
	//echo "id:".$UnitID;
	$UnitNo = "";
	if($UnitID > 0)
	{
		$sqlUnit = $objUtility->GetUnitDesc($UnitID);
		//$resUnit = $m_dbConn->select($sqlUnit);
		if($bTrace)
		{
			//print_r($resUnit);
		}

		$UnitNo = $sqlUnit[0]["unit_no"];
	}
	//echo "rootid:";
	$ObjGDrive = new GDrive($m_dbConn, $UnitNo, "", $bTrace);
	
	//echo "Initialize done";
	echo $ObjGDrive->ShowGDriveTree($rootid, 2,  $_REQUEST["Mode"], $UnitID);
	//$objDocumentView = new CDocumentsUserView($m_dbConn);
	//$arNotices = $objDocumentView->GetNotices($UnitID);
?>
<!-- </form>
          
			
		
        </div>
       -->    
</div> 

</div>
</div></div>
</div>
<script type="text/javascript">
	function ShowDocsForSelectedUnit(selectedId)
	{
		//alert(document.getElementById("UnitLedger").value);
		var sMode = "<?php echo $sViewMode ?>";
		//alert(sMode);
		window.location.href = "GDrive_view.php?Mode=" + sMode + "&UID=" + document.getElementById("UnitLedger").value;
	}
	function OpenDocument(URL)
	{
		//alert(URL);
		var form = document.createElement("form");
	    var element1 = document.createElement("input"); 
    	
	    form.setAttribute("target", "_blank");
	    form.method = "POST";
	    form.action = "W2S_DocViewer.php";   

	    element1.value=URL;
	    element1.name="path";
	    form.appendChild(element1);  

	    document.body.appendChild(form);

	    form.submit();
	}
	function ExpandSubFolder(value)
	{
		alert("expand");
		/*var arElements = value.split("_");
		for(var iCntr = 0;  iCntr < arElements.length ; iCntr++)
		{
			var UnitNo = arElements[0];
			var FolderName = arElements[1];
			var listNode = document.getElementById(UnitNo + "_" + FolderName);
			//var listNode = document.getElementById(value);
			if (!listNode.hasChildNodes()) 
			{
				console.log("has nodes");
		    	//var newNode = entry.appendChild(document.createTextNode("Notices"));
		    	//listNode.appendChild(newNode);
	    	}
	    	else
	    	{
	    		//NodeList.prototype.forEach = Array.prototype.forEach
				//var children = listNode.childNodes;
				//children.forEach(function(item)
				//{
				//	console.log(item.innerHTML);
					//if(typeof(item.innerHTML) != 'undefined')
				//}
			}
		}*/

	}
	function ExpandTree(value)
	{
			//alert(value);
		fetchNotices(value);
	}
	function fetchNotices(value)
	{
		var listNode = document.getElementById(value);
		if (!listNode.hasChildNodes()) 
		{
	    	var newNode = entry.appendChild(document.createTextNode("Notices"));
	    	listNode.appendChild(newNode);
    	}
    	else
    	{
    		NodeList.prototype.forEach = Array.prototype.forEach
			var children = listNode.childNodes;
			children.forEach(function(item){
				if(typeof(item.innerHTML) != 'undefined')
				{
			    	//console.log(item.innerHTML);
			    	//console.log(item.id);
			    	/*var iIndex = item.innerHTML.indexOf("_");
			    	if(iIndex > 0)
			    	{
						var arFolderElement = item.innerHTML;
						//console.log(arFolderElement.getElementById("101_Notice").id);
						console.log(arFolderElement);
			    		var arFolderDetails = item.innerHTML.split("_");
			    		for(var iCounter = 0;  iCounter < arFolderDetails.length ; iCounter++)
			    		{
			    			//console.log(arFolderDetails[iCounter]);

			    		}
			    	}
			    	else*/
			    	{
				    	if(item.innerHTML == value)
				    	{
				    		//alert(value);
				    		
							var UnitID = "14";
							//var PeriodID = document.getElementById('PeriodID').value;
							var objData = {"method" : 'fetch_UnitDocs',"unit_id" : UnitID}; 
							$.ajax({
									url : "ajax/ajaxdocument.php",
									type : "POST",
									dataType: 'json',
									data: objData ,
									success : function(data)
									{	
										//alert(data[1]);
										//alert(data.length);
										for(var iCntr = 0;  iCntr < data.length ; iCntr++)
										{


										//var arData = data.split("@@@");
										//alert(arData[1]);
										//var arFolders = JSON.parse(data);
										//alert(JSON.parse(data));
										//var jsonFolders = JSON.parse(arFolders);
										//var arFolder = arFolders.split(",");
										//alert(arFolder.length);	
										//console.log(arFolder[0]);
										//location.reload(true);
										//window.location.href = "Maintenance_bill.php?UnitID="+ UnitID + '&PeriodID='+ PeriodID+ '&BT='+IsSuppBill;
										var FName = value + "_" + data[iCntr];
		    							$("#"+value).append('<ul class=\"php-file-tree\"><li id='+ value + "_" + data[iCntr] +' class=\"pft-directory\" onclick="ExpandSubFolder(test);"><a href="#"><span >'+ data[iCntr] +'</span></a></li><ul>');
		    							}
									},
										
									fail: function()
									{
										//hideLoader();
									},
									
									error: function(XMLHttpRequest, textStatus, errorThrown) 
									{
										//hideLoader();
									}
								});
				    		//var newNode = item.appendChild(document.createTextNode("Notices"));
		    				//item.createTextNode(newNode);
				    	}
			    	}
				}
			});
    	}
	}
	$(document).ready(function() {
		init_php_file_tree();
		var role = '<?php echo $_SESSION["role"] ?>';
		if(role == 'Member' || role == 'Admin Member')
		{
			$("#sub").css('display', 'block');
		}
		document.getElementById("UnitLedger").value = "<?php echo $sUnitID ?>"; 
	
		$("#404").click( function () {
         // Filter on the column (the index) of this element
         //alert("test");
       } );
	});

</script>
<?php include_once "includes/foot.php"; ?>
