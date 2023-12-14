<?php include_once("includes/head_s.php");

include_once("classes/updateInterest.class.php");
$obj_updateInterest = new updateInterest($m_dbConn);

$_SESSION['wwid'] = $_REQUEST['wwid'];

if(isset($_REQUEST['period_id']))
{
	$details = $obj_updateInterest->getDetails($_REQUEST['period_id'], $_REQUEST['bill_type']);
}

else
{
	$details = $obj_updateInterest->getDetails(0, 0);
}


//var_dump($details);
$period = $obj_updateInterest->getPeriod($details[0]['M_Period'],  $details[0]['BillType']);
?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
	<script type="text/javascript" src="js/populateData.js"></script>
    <script type="text/javascript" src="js/updateinterest_20190326.js"></script>	
    <script type="text/javascript" src="js/csv-read.js"></script>	

    <script language="javascript" type="application/javascript">
	
	function go_error()
    {
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error_period').innerHTML = '';
        document.getElementById('error_period').style.display = 'none';	
    }
	function goToProcess()
	{
		window.location = "process/updateInterest.process.php";
	}
	</script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<style type="text/css">
 
  /*table.cruises { 
    font-family: verdana, arial, helvetica, sans-serif;
    font-size: 11px;
    cellspacing: 0; 
    border-collapse: collapse; 
    width: 535px;    
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
    height: 500px; 
    overflow: auto; 
    }

  table.scrollable thead tr {
    left: -1px; top: 0;
    position: absolute;
    }
</style>

<div id="middle">
<br><div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Update GST</div>

<?php
		if($_SESSION['role'] == 'Super Admin')
		{
			?>
			<form name="updateInterestPeriod" id="updateInterestPeriod" method="post" action="updategst.php" >
			<table style="padding-top: 40px;width:100%">	
				<tr>
                	<td style="width:10%"></td>
                    <td style="width:5%"><b>Bill Year </b></td>
					<td style="width:18%">
                    	&nbsp; : &nbsp;
						<select name="year_id" id="year_id" style="width:125px;" onChange="get_period(this.value);">
							<?php echo $combo_state = $obj_updateInterest->combobox("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR ); ?>
						</select>
					</td>
                    <td style="width:7%"><b>Bill Period </b></td>
					<td style="width:18%">
                    	<span>&nbsp; : &nbsp;</span>
						<select name="period_id" id="period_id" style="width:125px;">
							<?php echo $combo_state = $obj_updateInterest->combobox("select ID, Type from period where status='Y' and YearID = '" . DEFAULT_YEAR . "'", $details[0]['M_Period']) ?>    			
						</select>
					</td>
                    <td style="width:5%"><b>Bill Type</b></td>
					<td style="width:18%">
                  		  <span>&nbsp; : &nbsp;</span>
						<select name="bill_type" id="bill_type" style="width:125px;">
							<option value=0>Maintenance Bill</option>
							<option value=1>Supplementary Bill</option>
						</select>
					</td>
					<script>
						<?php
							if(isset($_REQUEST['bill_type']))
							{
								?>
									document.getElementById("bill_type").value = <?php echo $_REQUEST['bill_type'] ?>;
								<?php
							} 
						?>
					</script>
					<td style="text-align:center;width:10%"><input type="submit" value="Fetch" class="btn btn-primary"/></td>
                    <td style="width:5%"></td>
				</tr>
			</table>
            <table width="100%">
            <tr>
                <td align="center">
                 <font color="red" style="size:11px;"><b id="error_period"></b></font>
                </td>
            </tr>
            </table>
            <center><hr width="85%" noshade style="height:1px"></center>
			</form>
			<?php
		}
		?>
        <?php
		if($_SESSION['role'] == 'Admin')
		{
			?>
			<form name="updateInterestPeriod" id="updateInterestPeriod" method="post" action="updateInterest.php" >
			<table  align="center" style="padding-top: 40px;width:100%">	
				<tr>
            <!--    <select name="year_id" id="year_id" style="width:142px; visibility:hidden" onChange="get_period(this.value);">
							<?php //echo $combo_state = $obj_updateInterest->combobox("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR ); ?>
						</select>
				   
				
						<select name="period_id" id="period_id" style="width:142px; visibility:hidden">
							<?php //echo $combo_state = $obj_updateInterest->combobox("select ID, Type from period where status='Y' and YearID = '" . DEFAULT_YEAR . "'", $details[0]['M_Period']) ?>    			
							</select>-->
                <td style="width:35%"></td>                
                <td style="width:5%"><b>Bill Type</b></td>
                <td style="width:20%">
                	<span>&nbsp; : &nbsp;</span>
                    <select name="bill_type" id="bill_type" style="width:142px;">
                        <option value=0>Maintenance Bill</option>
                        <option value=1>Supplementary Bill</option>
                    </select>
                </td>
               <td align="center" style="text-align:left;width:25%;"><input type="submit" value="Fetch" class="btn btn-primary"/></td>
                <td style="width:25%"></td>
           		<script>
						<?php
							if(isset($_REQUEST['bill_type']))
							{
								?>
									document.getElementById("bill_type").value = '<?php echo $_REQUEST['bill_type']?>';
								<?php
							} 
						?>
					</script> 
                              	
				</tr>
			</table>
            <br/>
            <table width="100%">
            <tr>
                <td align="center">
                 <font color="red" style="size:11px;"><b id="error_period"></b></font>
                </td>
            </tr>
             
            </table>
            <center><hr width="85%" noshade style="height:1px"></center>
			</form>
			<?php
		}
		?>
       <!--<form name="updateInterest" id="updateInterest" method="post" action="process/updateInterest.process.php">
        	<input type="submit" value="Submit" class="btn btn-primary"/> -->

<br>
		<table width="100%">
        <tr>
        <td align="center">
        <br/><b>Import GST amount from file</b><br/><br/>
		<input type="file" id="csvFileInput" onChange="handleFiles(this.files)"
            accept=".csv">
			<br>
            <span>GST Update Sample File : <a href="samplefile/GstUpdate.csv" download>Click here to Download</a> </span>
            <br><br>
            <form name="updateInterest" id="updateInterest" method="post" action="process/updateInterest.process.php">
        	
        </td>
        </tr>
        </table>
		
		<?php
		
			if(isset($_POST['ShowData']))
			{
		?>
				<div align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></div>
		<?php
			}	
		?>
   	 <br><br>
          <center>
		<?php 
			if($_SESSION['role'] == 'Super Admin' || $_SESSION['role'] == 'Admin')
			{
				echo $period ;	
			}
	   ?>
       </center>
       <center><input type="submit" value="Update GST" class="btn btn-primary"/> </center><br>	

<?php
	if($details <> "" && sizeof($details) > 1)
	{
		?>
        <br>
        <table width="80%" style="text-align:center;" class="table table-bordered table-hover table-striped" id="heading">
			 <hr style="margin:0px"/>
            <thead style="box-sizing: border-box;">
           	<tr>
            	<th style="width:7%;">Unit</th>
				<th style="width:7%;">BillSubTotal</th>
				<th style="width:10%;">AdjustmentCredit</th>
				<th style="width:8%;">BillInterest</th>
                <th style="width:8%;">CGST</th>
                <th style="width:8%;">SGST</th>
				<th style="width:10%;">CurrentBillAmount</th>
				<th style="width:10%;">PrincipalArrears</th>
				<th style="width:8%;">InterestArrears</th>
				<th style="width:8%;">TotalBillPayable</th>
				<th style="width:12%;">Note</th> 
                <th style="width:3%"></th>
			</tr>
			</thead>
         </table>

        <div class="scrollableContainer">
        <div class = "scrollingArea">
			<table width="100%" style="text-align:center;" class="table table-bordered table-hover table-striped" id="updateInterest-table">
			<tbody>
			<?php 
				$units = array();
				if($details <> "" && sizeof($details) > 1)
				{
					//var_dump($details);
					for($i = 0; $i < sizeof($details); $i++)
					{
						$unit = $details[$i]['Unit'];
						$units[$i] = $unit;
						$BillSubTotal = $details[$i]['BillSubTotal'];
						$AdjustmentCredit = $details[$i]['AdjustmentCredit'];
						$BillTax = $details[$i]['BillTax'];
						$BillInterest = $details[$i]['BillInterest'];
						$CurrentBillAmount = $details[$i]['CurrentBillAmount'];
						$PrincipalArrears = $details[$i]['PrincipalArrears'];
						$InterestArrears = $details[$i]['InterestArrears'];
						$Cgst = $details[$i]['CGST'];
						$Sgst = $details[$i]['SGST'];
						$TotalBillPayable = $details[$i]['TotalBillPayable'];
						$Note = $details[$i]['Note'];
						
			?>
			<tr>
				<input type="hidden" name= "billDetailsID<?php echo $i ?>" id="billDetailsID<?php echo $i ?>" value="<?php echo $details[$i]['ID']?>" />
				<td style="width:7%;"><?php echo $unit ?></td>
				<td style="width:7%;"><?php echo $BillSubTotal ?></td>
				<td style="width:10%;"><?php echo $AdjustmentCredit ?></td>
				<!--<td style="width:5%;"><?php //echo $BillTax ?></td>-->
				<td style="width:10%;"><?php echo $BillInterest ?></td>
                    <td style="width:5%;"><input type="text" name="CGST<?php echo $i ?>" id="CGST<?php echo $i ?>" value="<?php echo $Cgst ?>" style="width:65px;"  onchange="changeInput(<?php echo $i; ?>);"></td>                
                <td style="width:5%;"><input type="text" name="SGST<?php echo $i ?>" id="SGST<?php echo $i ?>" value="<?php echo $Sgst ?>" style="width:65px;"  onchange="changeInput(<?php echo $i; ?>);"></td>
				<td style="width:10%;"><?php echo $CurrentBillAmount ?></td>
				<td style="width:10%;"><?php echo $PrincipalArrears ?></td>
				<td style="width:10%;"><?php echo $InterestArrears ?></td>
				<td style="width:8%;"><?php echo $TotalBillPayable ?></td>
				<td style="width:12%;"><input type="text" name="Note<?php echo $i ?>" id="Note<?php echo $i ?>" value="<?php echo $Note ?>" style="width:120px;"/></td>
			 </tr>
			<?php 	}
				} ?>
			<tr>
				<td colspan="10">        
					<input type="hidden" name="Count" value=" <?php echo $i ?>"  />
				</td>
			</tr>
			<tr><br>
            	<!--Setting IsgstUpdatePage field to check which page is updating in csv-read.js -->
            	<input type="hidden" name="IsgstUpdatePage" id="IsgstUpdatePage" value="1"> 
				<input type="hidden" name="period_id" id="period_id" value="<?php echo $details[0]['M_Period']; ?>" />
				<input type="hidden" name="bill_type" id="bill_type" value="<?php echo $details[0]['BillType']; ?>" />
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
			<center><div style="color:#FF0000;">No Records To Display</div></center>
            <br/>
		<?php
	}
	?>
</>
</form>
</div>
</div>
</body>
<script>
parseUnits(<?php echo json_encode($units); ?>);
</script>
<?php
if(IsReadonlyPage() == true)
{?>
<script>
	$("#updateInterest-table").css( 'pointer-events', 'none' );
</script>
<?php }?>
<?php include_once "includes/foot.php"; ?>