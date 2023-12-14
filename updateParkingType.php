<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Update Parking Type</title>
</head>

<?php include_once("includes/head_s.php");

include_once("classes/parkingType.class.php");
include_once "classes/view_member_profile.class.php" ;
$obj_ParkingType = new parkingType($m_dbConn);
$obj_viewMemberProfile = new view_member_profile($m_dbConn);
$details = $obj_ParkingType->getAllParkingDetails();
/*echo "<pre>";
print_r($details);
echo "</pre>";*/
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
	<script type="text/javascript" src="js/populateData.js"></script>
    <script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	function cancel()
	{
		window.location.href = "updateParkingType.php";
	}
	</script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">
<br><div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Update Parking Type</div>

<center>
       <form name="updateParkingType" id="updateParkingType" method="post" action="process/updateParkingType.process.php">
       <div width="100%" style="text-align:center;padding-right:5%;margin-left:15%">
       		<br>
        	<input type="submit" value="Submit" class="btn btn-primary" style="width:100px;padding: 2px 7px;"/>&nbsp;&nbsp;
            <input type="button" value="Cancel" class="btn btn-primary" style="width:100px;padding: 2px 7px;" onClick="cancel()"/>
            <span style="float:right"><a href="viewParkingType.php"><input type="button" value="Set Parking Types" class="btn btn-primary" style="width:150px;padding: 2px 7px;"/></a></span>
       </div>
<br>
<br>

<style type="text/css">
 
  /*table.cruises { 
    font-family: verdana, arial, helvetica, sans-serif;
    font-size: 11px;
    cellspacing: 0; 
    border-collapse: collapse; 
    width: 535px;   0 
    }*/
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
<?php
	if($details <> "" && sizeof($details) > 1)
	{
		?>
        <?php
		
			if(isset($_POST['ShowData']))
			{
		?>
				<div align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></div>
		<?php
			}	
		?>
        <br>
        <table width="80%" style="text-align:center;" class="table table-bordered table-hover table-striped" id="heading">
			<thead>
			<tr>
				<th style="width:7%;text-align:center">Unit</th>
				<th style="width:18%;text-align:center">Owner Name</th>
				<th style="width:7%;text-align:center">Vehicle</th>
                <th style="width:15%;text-align:center">Registration No.</th>
				<th style="width:7%;text-align:center">Make</th>
				<th style="width:7%;text-align:center">Model</th>
				<th style="width:15%;text-align:center">Parking Type</th>
			</tr>
			</thead>
         </table>

        <div class="scrollableContainer">
        <div class = "scrollingArea">
			<table width="100%" style="text-align:center;" class="table table-bordered table-hover table-striped" id="updateInterest-table">
			<tbody>
			<?php 
				if($details <> "" && sizeof($details) > 1)
				{
					for($i = 0; $i < sizeof($details); $i++)
					{
						$parkingId = $details[$i]['ParkingId'];
						$unit = $details[$i]['Unit'];
						$ownerName = $details[$i]['vehicle_owner'];
						$make = $details[$i]['Make'];
						$model = $details[$i]['Model'];
						$regNo = $details[$i]['RegNo'];
						$ParkingType = $details[$i]['ParkingType'];
						$type =  $details[$i]['Type'];
						$member_id = $details[$i]['member_id'];
			?>
			<tr>
				<input type="hidden" name="parkingId<?php echo $i;?>" id="parkingId<?php echo $i;?>" value="<?php echo $details[$i]['ParkingId']?>" />			<input type = "hidden" name="type<?php echo $i;?>" id = "type<?php echo $i;?>" value = "<?php echo $type;?>" />
				<td style="width:7%;"><?php echo $unit;?></td>
				<td style="width:18%;">
				<?php 
				if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || ($_SESSION['role'] == ROLE_ADMIN_MEMBER && $_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1))
            	{
				?>
            		<a href="view_member_profile.php?scm&id=<?php echo $member_id;?>&tik_id=<?php echo time();?>&m&view"><?php echo $ownerName;?>
                    </a>
            	<?php 
				}
				else
				{
					echo $ownerName;
				}
				?>
                </td>
				<td style="width:7%;"><?php echo $type;?></td>
				<td style="width:15%;"><?php echo $regNo;?></td>
				<td style="width:5%;"><?php echo $make;?></td>
				<td style="width:5%;"><?php echo $model;	?></td>
				<td style="width:14%;">
                	<select id="parkingType<?php echo $i;?>" name="parkingType<?php echo $i;?>" style="width:80%">
                    	<?php echo $obj_viewMemberProfile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$details[$i]['ParkingType']);?>
                    </select>
                </td>
			 </tr>
			<?php 	}
				} ?>
			<tr>
				<td colspan="10">        
					<input type="hidden" name="Count" value=" <?php echo $i ?>"  />
				</td>
			</tr>
			</tbody>
		</table>
        </div>
        </div>
		<?php
	}
	else
	{
		?>
			<div style="color:#FF0000;">No Records To Display</div>
		<?php
	}
	?>
</center>
</form>
</div>
</div>
</body>
<?php
if(IsReadonlyPage() == true)
{?>
<script>
	$("#updateInterest-table").css( 'pointer-events', 'none' );
</script>
<?php }?>
<?php include_once "includes/foot.php"; ?>