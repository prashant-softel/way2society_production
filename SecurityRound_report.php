<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Security Managment Report</title>
</head>

<?php 
//******Including the required File for SM Report 
include_once("includes/head_s.php");    
include_once("classes/include/dbop.class.php");
include_once("classes/SM_report.class.php");

//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$ObjSMReport1 = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);


if($_REQUEST['from_date']<>'' && $_REQUEST['to_date']<>'')
		{
			
			$GetDetails1 = $ObjSMReport1->GetSecurityRoundDetails($_POST['from_date'],$_POST['to_date']);
			//$GetDetails1 = $ObjSMReport1->GetSecurityRoundDeails();
			$fromdate=getDisplayFormatDate($_POST['from_date']);
			$toDate =getDisplayFormatDate($_POST['to_date']);
		}
else{
				$month=date("m");
				$year=date("Y");
				$fromdate='01-'.$month.'-'.$year;
				$fromdate=  getDisplayFormatDate($fromdate);
                $tdate=30;
			if($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12")
						   {
							   $tdate="31";
						   }
	   		if($month=="02")
							{
								$tdate="28";
							}
			$toDate=$tdate.'-'.$month.'-'.$year;
			$toDate= getDisplayFormatDate($toDate);
				
}

?>
<script>
    $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
			minDate: -20, 
			maxDate: "+1M +10D",
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate
			//maxDate: maxGlobalCurrentYearEndDate
			//changeMonth: true,
			//changeYear: true,
			//showButtonPanel: true, closeText: 'Clear', 
        })});
    </script>
	
<style>
</style>
<div style="text-align:center" class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:95%">
 <div class="panel-heading text-center" style="font-size:20px" >
   Security Round Report
</div>
<center>
<form name="schedulemaster" id="schedulemaster" method="post" >
<button type="button" class="btn btn-primary btn-circle"  style="float:left;" id="btnBack" onClick="window.location.href='schedule_list_report.php?type=0'"><i class="fa  fa-arrow-left"></i></button>

<table>

</table>			 	
<table style="margin-top: 20px;">

<tr style="height:50px;"> 
	<td style="padding-left: 50px;font-size: 15px; "><label> From :</label></td>
	<td><input type="hidden" name="startdte" id="startdte" value="<?php echo $fromdate;?>">
		<input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:100px;font-size: 15px;" value = "<?php echo $fromdate?>"/>
	</td>
	<td style="font-size: 15px;width:5%"><label> To :</label></td>                     
	<td ><input type="hidden" name="enddte" id="enddte" value="<?php echo $toDate;?>">
		<input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:100px;font-size: 15px" value="<?php echo $toDate	?>"/>
	</td>
	<td style="padding-right: 15px;font-size: 15px; "><input type="submit" name="submit" value="Submit" class="btn btn-primary"/> </td> 
</tr>
</table>
						</form>
</center>
<div class="table-responsive">
		<br/>          
             <table id="example" class="display" cellspacing="0" width="100%">
                <thead>              
                   <tr>
				    <th style="text-align: center">Schedule Name</th>
					<th style="text-align: center">Frequency</th>
					<th style="text-align: center">Schedule Round Time</th>
					<th style="text-align: center">Create By</th>
                     <th style="text-align: center">Security Round Time</th>
                   </tr>
                </thead>
				<tbody>
					<?php	
            	foreach($GetDetails1 as $k => $v)
           		 {
					?>
					<tr align="center">	                
                    <td><?php echo $GetDetails1[$k]['schedule_name'];?></td>
                    <td><?php echo $GetDetails1[$k]['frequency'];?></td>
                    <td><?php echo $GetDetails1[$k]['rtime'];?></td>
					<td><?php echo $GetDetails1[$k]['create_by'];?></td>
					<td><?php echo $GetDetails1[$k]['round_time'];?></td>

				<?php	}	?>
                   </tbody>
					</table>
				
</div>		
</div>     
<?php include_once "includes/foot.php"; ?>