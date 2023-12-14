<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Notification</title>
</head>
<?php include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/notification.class.php");
include_once("classes/home_s.class.php");
$obj_notify = new notification($m_dbConn);
$lblPeriodID = "";

$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];

if(!isset($_REQUEST['period_id']) || $_REQUEST['period_id'] == '')
{
	$_REQUEST['period_id'] = DEFAULT_PERIOD;
}
?>
 
<html>
<head>
<meta charset="utf-8">
<title>Bill Read TimeStamp Reports</title>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript" src="js/notification10112018.js?20102022"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
	<script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
	
	function ViewClick() 
	{
		
	}
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	function ViewPDF()
	{

	}
	
	function queryResult()
	{
		//alert("complete");
	}
	
	</script>
    <script>
function fetch()
{
	
document.getElementById('fetchValue').value='1';
document.genbill.submit();
}
function NotifyChange(btnVal)
	{ 
		 var NotifyValue = document.getElementById("newNotify").value;
		 var FetchValue = document.getElementById("fetchValue").value;
		  
		  if(btnVal ==1)
		  {
			  document.getElementById('bill_detail_table').style.display="none";
			  document.getElementById('Notify').style.display="table-row";
			  //document.getElementById('Notify').style.display="block";	
			}
			else if(btnVal ==2)
			{
			document.getElementById('Notify').style.display="none";	
			document.getElementById('bill_detail_table').style.display="table-row";
			//document.getElementById('bill_detail_table').style.display="block";
			}
			else{
					if(NotifyValue == 0 && FetchValue==0)
					{
						//alert("test1");
						 document.getElementById("NewnotifyButton").style.display="block";
						 document.getElementById("ViewBillReport").style.display="block";
						 document.getElementById('Notify').style.display="none";
						 document.getElementById('bill_detail_table').style.display="none";
						 //document.getElementById("newNotify").value = 1;
						 //document.getElementById("fetchValue").value = 1;
					}
					else
					{
						//alert("test2");
						document.getElementById('Notify').style.display="table-row";
						document.getElementById("NewnotifyButton").style.display="block";
						document.getElementById('bill_detail_table').style.display="table-row"; 
						document.getElementById('ViewBillReport').style.display="block";
						document.getElementById("newNotify").value =0;
						document.getElementById("fetchValue").value =0;
					}
			}
	}

</script>
<style type="text/css">
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
	 table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
    padding-top: 6em;
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 600px; 
    overflow: auto;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
	img 
	{
    	max-width: none !important;
	}
  </style>
</head>
<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br><br>
<div class="panel panel-info" id="panel" style="display:none">
	<div class="panel-heading" id="pageheader">Notification</div>

<center>
<div id="maintenance_bill">
<form name="genbill" id="genbill" method="post" action="notification.php" <?php echo $val;?>>
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<input type="hidden" name="SentEmailManually" id="SentEmailManually" value="1">
<input type="hidden" name="wwid" value="<?php echo $_GET['wwid'];?>">
	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Record Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <br><br>
  <?php 
  if(isset($_REQUEST['wing_id']) && isset($_REQUEST['period_id']))
 {?>
 <table><tr><td> 
<button type="button" class="btn btn-primary" onClick="NotifyChange('1')" id="NewnotifyButton" style="float:left">Email & SMS Notification</button>
</td>
<td>
<button type="button" class="btn btn-primary" onClick="NotifyChange('2')" id="ViewBillReport" style="float:left">View Email Read Report</button>
</td></tr></table>
<?php 
}?>
 <br><br>
      <input type="hidden" id="newNotify" name="newNotify" value="0"  onClick="NotifyChange();"/>
    <input type="hidden" name="society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>" />
    <table align='center' id="Notify">
		<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>	        
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="wing_id" id="wing_id" style="width:142px;" onChange="get_unit(this.value);">
                    <?php echo $combo_wing = $obj_notify->combobox("select wing_id,wing from wing where status='Y' and society_id = '" . DEFAULT_SOCIETY . "'", $_REQUEST['wing_id'], 'All', '0'); ?>
				</select>
            </td>
		</tr>
        
		<tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Unit No. ( Flat No )</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="unit_id" id="unit_id" style="width:142px;">
                	<?php echo $combo_unit = $obj_notify->combobox("select unit_id, unit_no from unit where wing_id = '" . $_REQUEST['wing_id'] . "'", $_REQUEST['unit_id'], "All", '0');
					?>
				</select>
            </td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" onChange="get_period(this.value );" >
                	<?php echo $combo_state = $obj_notify->combobox("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR,"" ); ?>
				</select>
            </td>
		</tr>        
        <tr align="left">
        	<td valign="middle"><?php if(isset($_GET['ws'])){echo $star;}?></td>
			<td>Bill For </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="period_id" id="period_id" style="width:142px;">
        	    	<?php //echo $combo_state = $obj_notify->combobox("select ID, Type from period  where  status='Y' and YearID = '" . DEFAULT_YEAR . "'", $_REQUEST['period_id']) ?>    			
                    </select>
            </td>
		</tr>
       <!-- <tr align="left">
        <td></td>
        <td>Supplementary Bills </td>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="checkbox" id="Supplementary_Bills" name="Supplementary_Bills"  style="width:14px;height:20px" value=""  onChange="ToggleSupplementary_bill()" />
                <span style="color:#F00;vertical-align:middle" id="supplementary_bill_span">&nbsp;&nbsp;Enable it to notify supplementary bill instead of regular bill. </span>
        </td>
        </tr>-->
        
          <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Type</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="bill_method" id="bill_method" value="<?php echo $_REQUEST['bill_method'];?>"  style="width:142px;" onChange="" >
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Regular Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>">Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
        <input type="hidden" id="fetchValue" name="fetchValue" value="0"  onClick="fetch();"/>
        <tr><td colspan="4">&nbsp;</td></tr>
		
        <tr>
			<td colspan="4" align="center">
        	    <input type="submit"  name="insert" id="insert" value="Fetch" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;"/>
                <!--<input type="button" value="View Bill Report" id="ViewBitt"  onClick="fetchButton();" class="btn btn-primary" style="color:#FFF;background-color:#337ab7;display:none;">-->
            </td>
		</tr>
        <tr><td><br><br></td></tr>
</table>
<?php 
if(isset($_REQUEST['wing_id']) && isset($_REQUEST['period_id']))
{?>
	<div style="width:70%;border:1px solid black;">
    <table id="bill_detail_table">
    <tr><td colspan="4">&nbsp;</td></tr>
     <tr>
            <td> &nbsp; Account Status  :</td>
            <td> 
            	<select name="ac_status" id="ac_status" style="width:142px;" >
                	<option value="0" <?php if($_REQUEST['ac_status'] == 0) { echo 'selected' ;} ?>>All</option>
                    <option value="1" <?php if($_REQUEST['ac_status'] == 1) { echo 'selected' ;} ?>>Active</option>
                    <option value="2" <?php if($_REQUEST['ac_status'] == 2) { echo 'selected' ;} ?>>Inactive</option>
                </select>
           	</td>
             <td> &nbsp; Bill View Status  :</td>
            <td>
            	<select name="read_type" id="read_type" style="width:142px;">
                	<option value="0"  <?php if($_REQUEST['read_type'] == 0) { echo 'selected' ;} ?>>All</option>
                    <option value="1" <?php if($_REQUEST['read_type'] == 1) { echo 'selected' ;} ?>>Read</option>
                    <option value="2" <?php if($_REQUEST['read_type'] == 2) { echo 'selected' ;} ?>>Unread</option>
                </select>
           	</td>
     	</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr align="center">
        <td colspan="4">
<input type="button" value="Search" id="billreport"  onClick="fetch();" class="btn btn-primary" style="color:#FFF;background-color:#337ab7;">
<!--<input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()" />-->
</td></tr>
<tr><td colspan="4">&nbsp;</td></tr>
</table>
</div>
<?php }?>

<script>
	get_period('', '<?php echo $_REQUEST['period_id']; ?>');
</script>
<!--<input type="button" value="View As PDF"  onclick="ViewPDF();"/>-->
</form>
</div>
<br>
<div id="status" style="color:#0033CC; font-weight:bold;"></div>
<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="unit_print.php?ssid=<?php echo $_GET['ssid'];?>&wwid=<?php echo $_GET['wwid'];?>&society_id=<?php if($_GET['society_id']<>""){echo $_GET['society_id'];}else{echo $_SESSION['society_id'];}?>&wing_id=<?php echo $_GET['wing_id'];?>&unit_no=<?php echo $_GET['unit_no'];?>&insert=<?php if($_GET['insert']<>""){echo $_GET['insert'];}else{echo 'Search';}?>&ShowData=&imp=" target="_blank"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

<?php
//echo 'Wing : ' . $_REQUEST['wing_id'] . ' Period : ' . $_REQUEST['period_id'];
if(isset($_REQUEST['wing_id']) && isset($_REQUEST['period_id']))
{	echo "<br>";
	//$Supplmtry_bill = $_REQUEST["Supplementary_Bills"] ? 1 : 0;
	$Supplmtry_bill = 0;
	
	if(isset($_REQUEST["bill_method"]) && $_REQUEST["bill_method"] == 1)
	{
		$Supplmtry_bill = 1;
	}
	  if($_REQUEST['fetchValue']==0)
 	 {
		$str1 = $obj_notify->pgnation_bill($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id'], $Supplmtry_bill );
 	 }
	  else
	  {
		    $str1 = $obj_notify->bill_view_report($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id'], $Supplmtry_bill );
		 }/*echo "<br>";
	echo $str = $obj_notify->display1($str1, $_REQUEST['period_id']);
	echo "<br>";
	$str1 = $obj_notify->pgnation_bill($_SESSION['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id']);
	echo "<br>";*/
echo "<br>";
}
	if($_REQUEST["bill_method"] == 1)
	{
		?>
		<script>
			/*document.getElementById("Supplementary_Bills").checked = true;
			document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Supplementary bill(s) will be notified instead of regular bill&nbsp;&nbsp;&nbsp;";
			document.getElementById("supplementary_bill_span").style.color = "#009900";
			document.getElementById("Supplementary_Bills").value = 1;*/
			document.getElementById("bill_method").value = "1"; 
		</script>
        <?php
	}
	else
	{
		?>
        <script>
			//document.getElementById("Supplementary_Bills").value = 0;
			document.getElementById("bill_method").value = "0"; 
		</script>
        <?php
	}
	?>
<script>
	function ToggleSupplementary_bill()
	{
		/*if(document.getElementById("Supplementary_Bills").checked == true)
		{
			document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Supplementary bill(s) will be notified instead of regular bill&nbsp;&nbsp;&nbsp;";
			document.getElementById("supplementary_bill_span").style.color = "#009900";
			document.getElementById("Supplementary_Bills").value = 1;
		}
		else
		{
			document.getElementById("supplementary_bill_span").innerHTML = "&nbsp;Enable it to generate supplementary bill instead of regular bill(s)";
			document.getElementById("supplementary_bill_span").style.color = "#F00";
			document.getElementById("Supplementary_Bills").value = 0;
		}*/
	}
    </script>

</center>
<script type="text/javascript" src="js/jsadd_member_id_20190810.js"></script>
<script>
NotifyChange();
 function SendActEmail(role,unit_id,society_id,code,email,name)
{
	if(email=='')
	{
		email = prompt("Please enter Email ID",  email);
	}
		$.ajax({
		url : "ajax/ajax_email.php",
		type : "POST",
		data: {"mode" : "email","role" : role,"unit_id" : unit_id,"society_id" : society_id,"code" : code,"email" : email,"name" : name} ,
		success : function(data)
		{	
			
			if(data != '') 
			{
				var sIndex = data.indexOf("Success");
				if(parseInt(sIndex) > 0)
				{
					alert("Email Send Successfully");
				}
				else
				{
					alert("Error while sending Email. Please retry.");
				}

			}
			else
			{
			}
		}
	});	
}

</script>
</div>
<br/><br /><br/><br /><br />

<?php include_once "includes/foot.php"; ?>
