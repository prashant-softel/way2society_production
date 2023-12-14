
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Manage Share Certificate</title>
</head>

<?php
include_once("includes/head_s.php");
include_once ("check_default.php");
include_once("classes/sharecertificate.class.php");
$obj_certificate = new Share_Certificate($m_dbConn);

if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Update')
{
	$errorMsgArray = $obj_certificate->updateShareCertificateDetails();
}

$unit_details = $obj_certificate->getUnitDetails();
?> 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>    	
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	function calcTotal(from, to, total) 
	{
		total.value = 0;
		if(	to.value > 0 || from.value > 0)
		{	
			total.value = to.value - from.value + 1;
		}
	}
	function valnominee()
		{
		var nomineename=document.getElementById('nominee_name<?php echo $i; ?>').value;
	
if(nomineename=="")
{
 document.getElementById('ErrorDiv4').style.display='block';
  document.getElementById('ErrorDiv4').innerHTML ="Please Enter Group Name";
  return false; 	 
}
	 
}
	
	</script>        
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } 


?>

<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Manage Share Certificate</div>
<center><br>
<form name="add_sharecertificate" id="add_sharecertificate" method="post" action="" onSubmit="return valnominee();">

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
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_REQUEST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
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
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>    
</table>
</form>

<br>
<br>
<style type="text/css">
  table.cruises td { 
    border-left: 1px solid #999; 
    border-top: 1px solid #999;  
    padding: 2px 4px;
    }
  table.cruises tr:first-child td {
    border-top: none;
  }
  table.cruises th { 
    border-left: 1px solid #999; 
    padding: 2px 4px;
    background: #6b6164;
    color: white;
    font-variant: small-caps;
    }
  table.cruises td { background: #eee; overflow: hidden; }
  
  div.scrollableContainer { 
    position: relative; 
    
	width:100%;
    margin: 0px; 
	border: 1px solid #999;   
   }
  div.scrollingArea { 
    height: 600px; 
    overflow: auto; 
    }

  table.scrollable thead tr {
    left: -1px; top: 0;
    position: absolute;
    }
</style>
<form name="sharecertificate" id="sharecertificate" method="post" action="add_sharecertificate.php">
	<input type="submit" value="Update" id="submit" name="submit" class="btn btn-primary" style="width:100px;padding: 2px 7px;margin-top:-5%">
    <br>
    <br>
<table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped">
<thead>
	<tr>
    	<th align="center" style="width:2%;"> Wing </th>
        <th align="center" style="width:7%;"> Unit No </th>
        <th align="center" style="width:12.5%;"> Member Name </th>
        <th align="center" style="width:12.5%;"> Share Certificate No. </th>   
        <th align="center" style="width:10%;"> From </th>
        <th align="center" style="width:10%;"> To </th>   
        <th align="center" style="width:10%;"> Total </th> 
        <th align="center" style="width:7%;"> Nomination Form </th>
        <th align="center" style="width:13.5%;"> Nominee Name </th>
        <th align="center" style="width:20%;"> GSTIN No </th>
    </tr>
</thead>
</table>
<div class="scrollableContainer">
<div class = "scrollingArea">
<table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped">
<tbody>
    	<?php
			for($i = 0; $i < sizeof($unit_details); $i++)
			{
		?>
	<tr>
    	<td style="width:7%;"> <?php echo $unit_details[$i]['wing']; ?> </td>
        <td style="width:8%;"> <?php echo $unit_details[$i]['unit_no']; ?> </td>
        <td style="width:8%;"> <?php echo $unit_details[$i]['owner_name']; ?> </td>
        <td style="width:13%;"> <input type="text" name="sharecerticateNo<?php echo $i ?>" id="sharecerticateNo<?php echo $i ?>" value="<?php echo $unit_details[$i]['share_certificate']; ?>" style="width:100px;background-color:#FF9;" />
         <input type="hidden" name="unitID<?php echo $i ?>" id="unitID<?php echo $i ?>" value="<?php echo $unit_details[$i]['unit_id'];?>" /> </td>
        <td style="width:10%;"> <input type="number" name="from<?php echo $i; ?>" id="from<?php echo $i; ?>" min="0" value="<?php echo $unit_details[$i]['share_certificate_from']; ?>" onChange="calcTotal(this.form.from<?php echo $i; ?>, this.form.to<?php echo $i; ?>,this.form.total<?php echo $i; ?>);" style="width:80px;background-color:#FF9;"/> </td>
        <td style="width:10%;"> <input type="number" name="to<?php echo $i; ?>" id="to<?php echo $i; ?>" min="0" value="<?php echo $unit_details[$i]['share_certificate_to']; ?>"  onChange="calcTotal(this.form.from<?php echo $i; ?>, this.form.to<?php echo $i; ?>,this.form.total<?php echo $i; ?>);" style="width:80px;background-color:#FF9;"/> </td>
        <?php $total = 0;
			if($unit_details[$i]['share_certificate_to'] > 0 || $unit_details[$i]['share_certificate_from'] > 0)
			{
				$total = $unit_details[$i]['share_certificate_to'] - $unit_details[$i]['share_certificate_from'] + 1;
			}
		?>
        <td style="width:12%;"> <input type="number" name="total<?php echo $i; ?>" id="total<?php echo $i; ?>" value="<?php echo $total; ?>" readonly style="width:60px;"/> </td>
        <td style="width:10%;"> <input type="checkbox" name="nomination<?php echo $i; ?>" id="nomination<?php echo $i; ?>" value="1" <?php if($unit_details[$i]['nomination'] == 1) { echo 'checked'; } ?>  onChange="toggleDisabled(this, '<?php echo $i; ?>')"/></td>
        
        <td style="width:10%;">
        <?php if($unit_details[$i]['nomination'] == 1)
		{
			?>
			<input type="text" name="nominee_name<?php echo $i; ?>" id="nominee_name<?php echo $i; ?>" value="<?php echo $unit_details[$i]['nominee_name']; ?>"  style="width:120px;"/><?php echo $errorMsgArray[$i];?>
        <?php }
		else{ 
		
		if($errorMsgArray[$i] <> '')
			{
			?>
            <input type="text" name="nominee_name<?php echo $i; ?>" id="nominee_name<?php echo $i; ?>" value="<?php echo $unit_details[$i]['nominee_name']; ?>"  style="width:120px; color:#F00" readonly /><?php echo $errorMsgArray[$i];?>
			<?php 
			}
			else
			{
			?>

		<input type="text" name="nominee_name<?php echo $i; ?>" id="nominee_name<?php echo $i; ?>" value="<?php echo $unit_details[$i]['nominee_name']; ?>"style="width:120px;"readonly/><?php //echo $errorMsgArray[$i];?>
		
		<?php }
		}?></td>
        
        <td style="width:14%;"><input type="text" name="owner_gstin_no<?php echo $i; ?>" id="owner_gstin_no<?php echo $i; ?>" value="<?php echo $unit_details[$i]['owner_gstin_no']; ?>" style="width:120px;" />
        </td>
    </tr>	
        <?php
			}		
		?>
    <input type="hidden" name="count" id="count" value="<?php echo $i;?>" />
</tbody>
</table>
</div>
</div>
</form>
</center>
</div>
</div>
<script>
function toggleDisabled(objCheck, iCounter) {
	//alert(objCheck.id);
	var id = objCheck.id
	var key = 'nominee_name' + iCounter;
	if(document.getElementById(id).checked == true)
	{
		document.getElementById(key).readOnly = false;
		//document.getElementById(key).value;	
	}
	else
	{
		document.getElementById(key).readOnly = true;
		//document.getElementById(key).value;
	}
}
  </script>
<?php include_once "includes/foot.php"; ?>
