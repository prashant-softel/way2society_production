<?php if(!isset($_SESSION)) { session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php"); 
$dbConn=new dbop();
include_once("classes/utility.class.php"); 
include_once("classes/createMeeting.class.php");
include_once("classes/createMinutes.class.php"); 
$obj_utility=new utility($dbConn);
$obj_cMeeting=new createMeeting($dbConn);
$obj_cMinutes=new createMinutes($dbConn);
$mres=$obj_cMeeting->SelectgrpName();
$mId=$_REQUEST['mId'];  
$socId=$_SESSION['society_id'];
$socName=$obj_cMinutes->getSocietyName($socId);
/*echo "<pre>";
print_r($selectRes);
echo "</pre>";*/
$meetingRes = $obj_cMeeting->getMeetingByMeetingId($mId);
$meetingRes = json_decode($meetingRes,true);
/*echo "<pre>";
print_r($res);
echo "</pre>";*/
//echo "Society Name:".$socName;

//echo "mId:".$mId;         
?>
<html>
  <head>
    <title>viewMeeting</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/CreateMeeting.js"></script>
      <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
      <script type="text/javascript" src="js/jsevents20190504.js"></script>
      <script type="text/javascript">
		$( document ).ready(function()
		{
    		var mId=document.getElementById("mId").value;
			//var sName=document.getElementById("sName").value;
			$.ajax({
			url : "ajax/minutesOfMeeting.ajax.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"viewMinutes","mId":mId},
			success : function(data)
			{
				//alert ("data:"+data);
				document.getElementById("minutesDetails").innerHTML = data;	
			}
		});
		});
		function printDetails()
		{
			var divElements = document.getElementById('minutesDetails').innerHTML;
			//var headElements = document.getElementById('header').innerHTML;
        	var oldPage = document.body.innerHTML;
           	document.body.innerHTML = "<html><head><title></title></head><body><table><tr><td width='50%'>" + divElements + "</td></tr></table></body></html>";
        	//Print Page
        	window.print();
        	//Restore orignal HTML
        	document.body.innerHTML = oldPage;
		}
    </script>   
      <style>
      select.dropdown
      {
          position: relative;
          width: 100px;
          margin: 0 auto;
          padding: 10px 10px 10px 30px;
        appearance:button;
          /* Styles */
          background: #fff;
          border: 1px solid silver;
          /* cursor: pointer;*/
          outline: none;
        }
		/*@media print
		{
			.page-break	{ display: block; page-break-before: always; }
			 .no-print, .no-print *
        	{
          		display: none !important;
        	}
			footer 
			{
				page-break-after: always;
			}
        	div.tr, div.td , div.th 
        	{
          		page-break-inside: avoid;
        	}
			table.td
			{
        	text-align:center;
        	margin:auto;
        	margin-top:5px;
    		}
		}
	  /** Setting margins */       
		@media print and (width: 21cm) and (height: 29.7cm) {
     @page {
        margin: 3cm;
     }
}

/*assing myPagesClass to every div you want to print on single separate A4 page*/

      </style>
    </head>
  <body>
    <center>
      <form id="viewMeeting" name="viewMeeting" action="#" method="post" enctype="multipart/form-data">
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
              <div class="panel-heading" id="pageheader">View Meeting</div>
              <div style="text-align:right;padding-right: 50px;padding-top: 10px;"></div>
              <input type="hidden" id="maxrows" name="maxrows"/>
              <input type="hidden" id="mId" name="mId" value="<?php echo $mId; ?>"/>
        <div id = "printDiv">
        	<table width="55%">
            	<tr>
                	<td id="minutesDetails" name="minutesDetails">
                    </td>
                </tr>
            </table>
            <br>
            <input type="button" id="printButton" name="printButton" value="Print" onClick="printDetails()" class="btn btn-primary"/>
        	<br>
            <br>
        </div>
       </div>
     </form>
    </center>
   </body>
</html>     	 
<?php include_once "includes/foot.php"; ?>