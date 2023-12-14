<?php
//******Including the required File for Round Master
    include_once "ses_set_s.php"; 
	include_once("includes/head_s.php");
    include_once("classes/include/dbop.class.php");
	include_once("classes/roundmaster.class.php");
//*****Making different object to connect different databases
	$dbConn = new dbop();
	$dbConnRoot = new dbop(true);
	$smConn = new dbop(false,false,true,false);
	$smConnRoot = new dbop(false,false,false,true);
	
	$smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
    $getround_master_report = $smreport ->getschedule_list_report(); //calling the function through object
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
<title>W2S - Security management </title>
<script type="text/javascript" src="js/roundmaster.js"></script>
<script language="javascript" type="application/javascript">
function go_error()
{
	$(document).ready(function()
	{
		$("#error").fadeIn("slow");
	});
    setTimeout('hide_error()',8000);	
}
function hide_error()
{
	$(document).ready(function()
	{
		$("#error").fadeOut("slow");
	});
}
</script>
<style>
input[type=checkbox]
{
	margin: 1px 1px 5px !important; 
}
</style>
</head>

<html>
<head>
</head>

  
  
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

<div style="text-align:center" class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:95%">
 

<div class="panel-heading text-center" style="font-size:20px" >Round Master</div>
	<center>
		<form name="roundmaster" id="roundmaster" method="post"  action="process/roundmaster.process.php"  onSubmit="return val();"> 
 			<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>">
			
            
            <table align='center' >
			<?php
			if(isset($msg))
			{
				if(isset($_POST['ShowData']))
				{
				?>
					<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
			<?php
				}
				else
				{
				?>
					<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
				<?php
				}
			}
			else
			{
			?>
				<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
			<?php
			}
			?>
		
        	<tr><td><br></td></tr>
			<tr>
                <td valign="middle"><?php echo $star;?></td>
                <td> <b>Master Names</b></td>
                <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
                <td><input type="text" name="master_names" id="master_names" ></td>
			</tr>
			<tr>
                <td valign="middle"><?php //echo $star;?></td>
                <td><b>Description</b></td>
                <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
                <td><input type="text" name="desc" id="desc" ></td>
            </tr>
            <tr>
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top"><b>Checkpost Names</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td valign="top">
                <div style="overflow-y:scroll;overflow-x:hidden;width:220px; height:110px; border:solid #CCCCCC 2px;">
				<?php echo $combo_state = $smreport->combobox1("SELECT `id`, `checkpost_name` FROM `checkpost_master` ","checkpost_id[]","checkpost_id"); ?>
                </div>
            </td>
		</tr>
        <tr><td><br></td></tr>
          

			<tr>
    			<td colspan="4" style="text-align:center;"></br></br>
        				<input type="submit" name="insert" id="insert" value="Insert" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;">
                        </td>
    		</td>
  </tr>
</table>



<table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;">Name</th>
                        <th style="text-align:center;">Description</th>
                        <th style="text-align:center;">No of Checkpost</th>
                        <th style="text-align:center;">Edit</th>
                        <th style="text-align:center;">Delete</th>
                    </tr>
                </thead>
            <tbody>
            <?php	
            	foreach($getround_master_report as $k => $v)
           		 {
					  ?>
					  <tr align="center">	                
             		<td><?php echo $getround_master_report[$k]['Name'];?></td>
                   <td>
				   <?php if($getround_master_report[$k]['Description'] <>  '') 
				       {
						   echo $getround_master_report[$k]['Description'];
					   }
					   else
					   { 
					   		echo "-"; 
					   }
                  ?></td>
                   <td><?php echo $getround_master_report[$k]['checkpostCount'];?></td>
                    <td  valign="middle" align="center"> <a href="round_master.php?id=<?php echo $getround_master_report[$k]['id'];?>&edit" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
                    <td  valign="middle" align="center"> <a href="round_master.php?deleteid=<?php echo $getround_master_report[$k]['id'];?>&del" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> 		   
                			
				<?php }?>
           </tbody>
        </table>
        </div>

<?php 
if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
{ 
    ?>
    <script>
        get_checkpost_master_edt(<?php echo $_REQUEST['id']; ?>)
    </script>
<?php }
if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
{ ?>
    <script>
        get_checkpost_master_del(<?php echo $_REQUEST['deleteid']; ?>)
    </script>
<?php } ?>


<?php include_once "includes/foot.php"; ?>


 
</script>