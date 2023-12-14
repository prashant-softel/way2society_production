<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add Expected Visitor</title>
</head>


<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/SM_report.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$ObjSMReport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsVisitor.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	 //$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			window.location.href='suspend.php';
		}
    //});
	
	</script>
<script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
  </script>
</head>
<?php if((isset($_POST['ShowData']) && $_POST['ShowData']<> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else
{
	?>
    <body>
<?php } ?>
<br>

<div class="panel panel-info" style="margin-top:4%;margin-left:1%; width:76%">
  <div class="panel-heading" style="font-size:20px;text-align:center;">
     Add Expected Visitor
    </div>
    <br/>
    <br />
    <div class="panel-body">                        
    <div class="table-responsive">
    <center>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
    <form name="expectedvisitor" id="expectedvisitor" method="post" action="process/visitor.process.php" enctype="multipart/form-data" onSubmit="return val();">
<table align='center'>
<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Contact No :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td> <input type="text" id="contactNo" name="contact"></td>
	</tr>
		 <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>First Name</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" id="firstName" name="firstName"></td>
	</tr>  
     <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Last Name</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" id="LastName" name="LastName"></td>
	</tr> 
    <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Expected Date</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" id="ExpDate" name="ExpDate" class="basics" size="10" style="width:80px;"></td>
	</tr>  
    <!-- <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td><b>Expected Time</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <select name="hr" id="hr" style="width:50px;">
            <option value="">HH</option>
            <?php //for($i=1;$i<=12;$i++)
			//{
				//if(strlen($i)==1)
				//{
				//	echo "<option value=0".$i.">0".$i."</option>";
				//}
				//else
				//{
					//echo "<option value=".$i.">".$i."</option>";
				//}
			//}
			?>
            </select>
           
            <select name="mn" id="mn" style="width:50px;">
            <option value="">MM</option>
            <?php //for($ii=0;$ii<=59;$ii++)
			//{
				//if(strlen($ii)==1)
				//{
					//echo "<option value=0".$ii.">0".$ii."</option>";
				//}
				//else
				//{
					//echo "<option value=".$ii.">".$ii."</option>";
				//}
			//}
			?>
            </select>
            
            <select name="ampm" id="ampm" style="width:50px;">
            <option value="AM">AM</option>
            <option value="PM">PM</option>
            </select>
            </td>
            </tr> -->
    <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Purpose</b></th>
        <td>&nbsp; : &nbsp;</td>
       <!-- <td><input type="text" id="purpose" name="purpose"></td>-->
        <td colspan="4"><select name="purpose"  id="purpose">
                    <?php echo $purpose = $ObjSMReport->combobox1("select purpose_id, purpose_name from `purpose`",'0');
                    ?>
                  </select>
                </td>
	</tr> 
    <tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Comments</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><textarea id="note" rows="4" cols="35"></textarea></td>
	</tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
		<td colspan="10" align="center"><input type="submit" name="insert" id="insert" class="btn btn-primary" value="Submit" style="width: 90px; height: 30px; background-color: #337ab7; color:#FFF"; ></td>
    </tr>
    <tr><td><br></td></tr>       
</table>
</form>
</center>
 </div>
 </div>
 </div>
 </body>
 
    <?php include_once "includes/foot.php"; ?>