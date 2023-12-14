<?php include_once "ses_set.php"; ?>
<?php
include_once("../includes/header.php");

include_once("../classes/mem_spouse_details.class.php");
$obj_mem_spouse_details=new mem_spouse_details();
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/pagination.css" >
	<script type="text/javascript" src="../js/ajax.js"></script>
	<script type="text/javascript" src="../js/jsmem_spouse_details.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',4000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	</script>
    
    <link rel="stylesheet" href="../css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="../javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="../javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="../javascript/ui.core.js"></script>
    <script type="text/javascript" src="../javascript/ui.datepicker_bday.js"></script>
    <script language="JavaScript" type="text/javascript" src="../js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "yy-mm-dd", 
            showOn: "both", 
            buttonImage: "../images/calendar.gif", 
            buttonImageOnly: true 
        })});
            
    </script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
<center><font color="#43729F" size="+1"><b>Member Spouse Details</b></font></center>

<form name="mem_spouse_details" id="mem_spouse_details" method="post" action="../process/mem_spouse_details.process.php">

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
		else
		{
			//$msg = '';	
		}
	?>
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

		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Select Member</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<?php
				if(isset($_REQUEST['idd']) && $_REQUEST['idd']<>"")
				{
				?>
                <select name="member_id" id="member_id" style="width:142px;">
                    <?php echo $combo_member_id = $obj_mem_spouse_details->combobox07("select mm.member_id,mm.owner_name from member_main as mm,mem_spouse_details as msd where mm.status='Y' and msd.status='Y' and mm.member_id!=msd.member_id group by mm.owner_name",$_REQUEST['idd']); ?>
                </select>
                <?php	
				}
				else
				{
				?>
                <select name="member_id" id="member_id" style="width:142px;">
                    <?php echo $combo_member_id = $obj_mem_spouse_details->combobox("select mm.member_id,mm.owner_name from member_main as mm,mem_spouse_details as msd where mm.status='Y' and msd.status='Y' and mm.member_id!=msd.member_id group by mm.owner_name"); ?>
                </select>
                <?php
				}
				?>
            </td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Spouse Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="spouse_name" id="spouse_name" /></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Occupation</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="spouse_desg" id="spouse_desg">
                    <?php echo $combo_spouse_desg=$obj_mem_spouse_details->combobox("select desg_id,desg from desg where status='Y'"); ?>
                </select>
            </td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Date of Birth</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="spouse_dob" id="spouse_dob" class="basics" size="10" readonly style="width:100px;"/></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Office Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="spouse_off_add" id="spouse_off_add" rows="4" cols="28"></textarea></td>
		</tr>
        
		<tr>
        	<td valign="top"><?php //echo $star;?></td>
			<td>Office No.</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="spouse_off_no" id="spouse_off_no" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"/></td>
		</tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Blood Group</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="spouse_bg" id="spouse_bg">
                    <?php echo $combo_spouse_bg = $obj_mem_spouse_details->combobox("select bg_id,bg from bg where status='Y'"); ?>
                </select>
           	</td>
		</tr>
        
		<tr><td colspan="4">&nbsp;</td></tr>
        
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            </td>
		</tr>
</table>
</form>

<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_mem_spouse_details->pgnation();
echo "<br>";
echo $str = $obj_mem_spouse_details->display1($str1);
echo "<br>";
$str1 = $obj_mem_spouse_details->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>

</body>
</html>
