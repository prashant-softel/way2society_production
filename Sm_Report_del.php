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

//****Making Object of SM_Report contructor class 
$ObjSMReport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);


			echo "<BR><BR>". $Sql;
		  	$StaffDeatils = $this->smConn->select($Sql);


echo "------------------------------------>".DB_DATABASE_SM; //$_REQUEST['Sid'];
//die;


$societyName = $ObjSMReport->GetSocietyName($_SESSION['society_id']);
$sname=$societyName[0]['society_name'];
//***declaring the variable for hold the type of report
$ReportName = '';
$VisitCount = 0;
//echo "------------------------------------>".$_REQUEST['Sid'];
//**** rq request check whether user requested for report or not
if(isset($_REQUEST['rq']))
{
	echo "<BR>Test 1";
	//*** if user request for report then check which type of report requested??
	if(isset($_REQUEST['Staff']))
	{
		echo "<BR>Test Staff ";
		//*** if it is staff then store and fetch data from class and set the values
		
		$staffID = 0;	
		$fromdate=$_POST['from_date'];
		$todate=$_POST['to_date'];
		
		if(isset($_REQUEST['Sid']) && $_REQUEST['Sid'] <> '')
		{
			//*** Set the visitor id for requested visitor
			$staffID = $_REQUEST['Sid'];
			
			
		}
		$staffID = $_REQUEST['Sid'];
		$var=0;
		$ReportName = "StaffImages";
		echo "<BR>GetStaff Details ";
		$GetDetails = $ObjSMReport->GetStaffDeails($staffID,$_POST['from_date'],$_POST['to_date'],$_POST['cat']);
		var_dump($GetDetails);
		if($GetDetails[0]['cat']=="All")
		{
			$var=1;
		}
		
		//var_dump($GetDetails);
	}
	else if(isset($_REQUEST['Visitor']))
	{
		//**** if it is Visitor then then it check whether user want single visitor report or whole report
		//*** First time whole data return and after that user can see the indiviual visitor report
		
		$visitorID = 0;		
		if(isset($_REQUEST['Vid']) && $_REQUEST['Vid'] <> '' && $_REQUEST['Vid'] === 0)
		{
			//*** Set the visitor id for requested visitor
			$visitorID = $_REQUEST['Vid'];
		}
		$visitorID = $_REQUEST['Vid'];
		$ReportName = "VisitorImage";
		
		//**** Now we fetch the report from class for visitor
		if($_REQUEST['from_date']<>'' && $_REQUEST['to_date']<>'' && $_REQUEST['purpose_id']<>'')
		{
			$GetDetails = $ObjSMReport->GetVisitorPurposeDetails($visitorID,$_REQUEST['from_date'],$_REQUEST['to_date'],$_REQUEST['purpose_id']);
		}
		else{
			$GetDetails = $ObjSMReport->GetVisitorDetails($visitorID,$_POST['from_date'],$_POST['to_date']);
		}
	}	
	
	if(array_key_exists("visitor_ID",$GetDetails[0]))
	{
		$VisitCount = sizeof($GetDetails);
	}
	if(array_key_exists("staff_id",$GetDetails[0]))
	{
		$staffcount = sizeof($GetDetails);
		
	}
	
}



?>
<head>
<style>
	#staff
	{
		width: 100%;
		height: 40%;
		overflow: scroll;
	}
	th
	{text-align: center}
</style>
	
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
<script type="text/javascript">
<?php if($_REQUEST['from_date']<>'' && $_REQUEST['to_date']<>''){?>
window.onload = function () {
	document.getElementById('from_date').value=document.getElementById('startdte').value;
	document.getElementById('to_date').value=document.getElementById('enddte').value;

	
}
<?php } ?>
</script>
<script>
function PrintPage() 
{
var originalContents = document.body.innerHTML;
document.getElementById('soc_name').style.display ='block';
var printContents = document.getElementById('staffattendance').innerHTML;
document.body.innerHTML = printContents;
window.print();
window.location="Sm_Report.php?Staff&rq";
}
function Expoort()
{
		 var myBlob =  new Blob( [$("#staffattendance").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "StaffAttendance.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
}
function PrintStaffReport()
{
	
var originalContents = document.body.innerHTML;
document.getElementById('soc_name').style.display ='block';
document.getElementById('example_length').style.display ='none';
document.getElementById('example_filter').style.display ='none';
document.getElementById('ToolTables_example_4').style.display ='none';
ar = document.getElementsByTagName("a");
for (i = 0; i < ar.length; ++i)
   ar[i].style.display = "none";
var printContents = document.getElementById('Staffinfo').innerHTML;
document.body.innerHTML = printContents;
window.print();
document.body.innerHTML= originalContents;	
}
function ValidateDate()
	{
		var fromDate = document.getElementById('from_date').value;
		var toDate = document.getElementById('to_date').value;		
		var isFromDateValid = jsdateValidator('from_date',fromDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		var isToDateValid = jsdateValidator('to_date',toDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		if(isFromDateValid == false || isToDateValid == false)
		{
			return false;	
		}
		return true;
	}
	
 $(function()
		{
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({ 
			dateFormat: "dd-mm-yy", 
			showOn: "both", 
			buttonImage: "images/calendar.gif", 
			buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
		})})
</script>

<style>
  #profile_img:hover{
	transform: scale(4);
	  }
</style>
</head>
<body>
<div class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:95%">
 
    <div class="panel-heading text-center" style="font-size:20px">
    <?php if(isset($_REQUEST['Staff']))
	{ ?>Staff Report
		
	<?php } else{?>
    Visitor Report
    <?php }?>
    </div>
	
   
     <div class="panel-body">   
                         
        <div class="table-responsive">
        <?php if(isset($_REQUEST['Vid'])){?>
        <br/>
        <div style="font-size:15px;">                    
        <form>
        <center>
        	<table width="100%"> 
            	<tr align="center">
            		<td style="width:25%">
                    <?php if(is_file("SecuirityApp/".$ReportName."/".$GetDetails[0]['entry_image']))
					{?>
						<img src="<?php echo "SecuirityApp/".$ReportName."/".$GetDetails[0]['entry_image'];?>" class="img-rectangle" id="img" alt="img" style="height:60%;">
					<?php }else{ ?>
                    <img src="images/noimage.png" class="img-rectangle" id="img" alt="img" style="height:15%;">
                    <?php }?>
                    </td>
            		<td style="text-align:left">
                    	<table style="width:50%">
            			<tr>
            				<td style="font-weight:500px">Name</td>
                            <td>:</td>
                            <td><?php echo $GetDetails[0]['VName'];?></td>
           		       </tr>
                       <tr>
                      		<td>Contact No</td>
                            <td>:</td>
                            <td><?php echo $GetDetails[0]['Contact'];?></td>
                       </tr>
                       
                       <?php if($GetDetails[0]['vehicle'] <> ''){?>
                        <tr>
                      		<td>Vehicle No</td>
                            <td>:</td>
                            <td><?php echo $GetDetails[0]['vehicle'];?></td>
                       </tr>
                       <?php }?>
                        <tr>
                      		<td>Visit Count</td>
                            <td>:</td>
                            <td><?php echo $VisitCount;?></td>
                       </tr>
                       </table>
                     </td>  
                  </tr>    
            </table>
            </center>
        </form>
        </div>
        <?php }?>
 
        <?php if(!isset($_REQUEST['Sid'])){?>
        <br><br>
         <form name="filter" id="filter" <?php if (isset($_REQUEST['Staff'])){?>action="Sm_Report.php?Staff&rq" <?php }
		 else{ if(isset($_REQUEST['Vid'])){?> action="Sm_Report.php?Vid=<?php echo $_REQUEST['Vid'];?>&Visitor&rq" <?php }else{?>action="Sm_Report.php?Visitor&rq" <?php  }}?> method="post" onSubmit="return ValidateDate() CheckData();">
			 	<center><table style="width:85%; border:1px solid black; background-color:transparent; " >
    <tr> <td colspan="8"><br/> </td></tr>
    	<tr>
			
      
            <td style="padding-left: 50px;font-size: 15px;"><label> From :</label></td>                      
			<td><input type="hidden" name="startdte" id="startdte" value="<?php echo $_REQUEST['from_date'];?>"><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:100px;font-size: 15px" value = "<?php
				$month=date("m");
				$year=date("Y");
				$fromdate='01-'.$month.'-'.$year;
				echo getDisplayFormatDate($fromdate)?>"/>
			
			</td>
			
			 <td style="font-size: 15px;width:5%"><label> To :</label></td>                     
			<td ><input type="hidden" name="enddte" id="enddte" value="<?php echo $_REQUEST['to_date'];?>"><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:100px;font-size: 15px" value="<?php
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
			echo getDisplayFormatDate($toDate)?>"/></td>
			<?php if(isset($_REQUEST['Staff'])) { ?>
			 <td style="width:10%;font-size: 15px;"><label> Category :</label></td>     
			<td>
           	<select name="cat" id="cat" style="width:210px;font-size: 15px;" >
             <?php $qry1 = "SELECT `cat_id`, `cat`, `status` FROM `cat` WHERE `status`='Y' ORDER BY `cat`.`cat` ASC";
			echo $ObjSMReport->combobox($qry1,0); ?>
			</select>
            </td>
			<?php } ?>
            <td style="padding-right: 15px;font-size: 15px;"><input type="submit" name="submit" value="Submit" class="btn btn-primary" /> </td>
			
     	</tr>        
        
		<tr><td colspan="3"><br/></td>
		</tr>
		<tr>
        
        <?php if(isset($_REQUEST['Staff'])){?>
		<td colspan="7">
        <table>
        <tr>
        	
		<td style="font-size: 15px; font-weight:bold; padding-left:47px;">
         Report View :
         </td>
          <td style="font-size: 12px; font-weight:bold;">
         <input type="radio" id="staffreport" name="radio" value="listview" checked="checked" style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">List View</label><br/>
         </td>
        <td style="font-size: 12px; font-weight:bold;">
         <input type="radio" id="staffreport1" name="radio" value="detailview"  style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Attendance View	<span style="color:red;font-size:10px">&nbsp;&nbsp;(Kindly select date of difference with count less than 30 or 31)</span>
</label><br/>
         </td>
       
        </tr>
        </table>
        </td>
         <td></td>
		</tr>
         <br/>
         <tr>
        <td colspan="1"></td>
        
         </tr>
           <tr>
        <td colspan="2"></td>
        
         </tr>
       <?php }?>
		</tr>
		<tr>
			<td colspan="8"><br/></td>
		</tr>
    </table></center>
	</form>
    <BR><BR>
	<?php } ?>
    
    <?php if(isset($_REQUEST['rq']))
	{
	echo "<BR><BR>Test 2"; die;
		//var_dump($_POST['radio']);
		if($_POST['radio']=="listview" || $_POST['radio']==null)
		{
			
			?>
			<div class="panel-body">  
    <?php if(isset($_REQUEST['Sid'])){?>
    
        <div id="BtnExport" style="text-align:center" >
        <input type="button" class="btn btn-primary" value="Print Report" onClick="PrintStaffReport()">
        </div>
        <br><br>
			
        <div  id="Staffinfo">   
      		  <div id="soc_name" style="display: none">
					<br/>
					<p style="text-align: center;font-size: 30px"><?php echo $sname ?></p>
					<p style="text-align: center;font-size: 20px">Staff Report</p>
				
				</div>   
                 <div style="font-size:15px;">                                 
                 <form>
        		<table style="width:100%"> 
            	<tr align="center">
            		<td style="width:25%">
                    <?php if(is_file("SecuirityApp/".$ReportName."/".$GetDetails[0]['entry_image']))
					{?>
						<img src="<?php echo "SecuirityApp/".$ReportName."/".$GetDetails[0]['entry_image'];?>" class="img-rectangle" id="img" alt="img" style="height:60%;">
					<?php }else{ ?>
                    <img src="images/noimage.png" class="img-rectangle" id="img" alt="img" style="height:15%;">
                    <?php }?>
                    </td>
            		<td style="text-align:left">
                    	<table style="width:50%">
            			<tr>
            				<td style="font-weight:500px">Name</td>
                            <td>:</td>
                            <td><?php echo $GetDetails[0]['Staff_name'];?></td>
           		       </tr>
                      <tr>
                      		<td>Visit Count</td>
                            <td>:</td>
                            <td><?php echo $staffcount;?></td>
                       </tr>
                       </table>
                     </td>  
                  </tr>    
            </table>
        </form>
      	</div>
        <?php }?>
                      
        <div class="table table-responsive condensed" id="Stafflistreport">
		<br/>          
                
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>              
                   <tr>
                     <th>Sr No.</th>
                    <?php if((!isset($_REQUEST['Vid']))  )
					{?>
					<th style="width:10%">Images</th>
					<?php }?>
                    <?php  if(isset($_REQUEST['Staff']) && (!isset($_REQUEST['Sid'])))
						 {?>
                 	<th>Staff Name</th>    
					<?php }
							else{
							if(!isset($_REQUEST['Vid']) && isset($_REQUEST['Visitor']))
							{?>
								<th>Visitor Name</th>
					    			<th>Mobile Status</th>
					   <?php }?>
					   <?php if(isset($_REQUEST['Visitor'])){?>
                     <th>Visitor Unit</th>
                     <th>Owner Name</th>
		     <th>Approval Status</th>
                     <th>Purpose</th>
                     <th>Company Name</th>
						  <?php }?>
					<?php }?>
                    <?php  if($var==1)
						 {?>
                 	<th>Category</th>
                    <?php } ?>  
                     <th>In Date</th>
                     <th>In Time</th>
                     <th>In Gate</th>
                     <th>Out Date</th>
                     <th>Out Time</th>
                     <th>Out Gate</th>  
                     <th>Total Time</th>
                   </tr>
                </thead>
               	<tbody>
				<?php
					if(isset($_REQUEST['Vid']))
					{
						if(!array_key_exists("visitor_ID",$GetDetails[0]))
						{
							$GetDetails =array();
						}	
					}
						
					$Cnt = 0;
					for($i = 0 ; $i < sizeof($GetDetails); $i++)
					{
						$Cnt++; 
						$INDate = explode('-',$GetDetails[$i]['inTimeStamp']); 
						$INTime = explode(' ',$INDate[2]);
						$InYear = $INDate[0];
						$InMonth = $INDate[1];
						$InDate = $INTime[0];
						$Intime = $INTime[1];
						$OUTDate = explode('-',$GetDetails[$i]['outTimeStamp']); 
						$OUT_Time = explode(' ',$OUTDate[2]);
						$OutYear = $OUTDate[0];
						$OutMonth = $OUTDate[1];
						$OutDate = $OUT_Time[0];
						$Out_time = $OUT_Time[1];
						if($OutDate=="00"){$OutDate="";}
						if($Out_time=="00:00:00"){$Out_time="";}
						if($OutMonth=="00"){$OutMonth="";}
						if($OutYear=="0000"){$OutYear="";}
						$imgPath = "SecuirityApp/".$ReportName."/".$GetDetails[$i]['entry_image']; ?>
                 		  <?php if($GetDetails[$i]['approvalstatus'] =="Denied") {?>
					<tr style="color: red;">
						<?php }else{?>
						<tr>
						<?php }?>
						
                    	 <td style="padding-left:42px;"><?php echo $Cnt;?></td>
                         <?php if(!isset($_REQUEST['Vid']))
						 {?>
							 <?php if(($GetDetails[$i]['entry_image'] <> '' || $GetDetails[$i]['entry_image'] <> NULL) && is_file($imgPath)){ ?>
                                    
                             <td style="padding-left:42px;" class="outer-image-hover"><img src="<?php echo $imgPath;?>" class="img-circle" id="profile_img" alt="img" style="width:31px"></td>           
                                <?php }else {?>
                                
                             <td style="padding-left:42px;" class="outer-image-hover"><img src="images/noimage.png" id="profile_img" class="img-circle" alt="img" style=" width:31px"></td>
                             
                            <?php }
						}
						 if(isset($_REQUEST['Staff']) && (!isset($_REQUEST['Sid'])))
						 {?>
                        <td style="padding-left:86px;"><a href="Sm_Report.php?Sid=<?php echo $GetDetails[$i]['staff_id'];?>&Staff&rq" target="_blank"><?php echo $GetDetails[$i]['Staff_name']; ?></a></td>                         
                         <?php }
						 else{
								 if((!isset($_REQUEST['Vid'])) && (isset($_REQUEST['Visitor'])) )
								 {?>
                         <td><?php if(!isset($_REQUEST['Vid'])){?><a href="Sm_Report.php?Vid=<?php echo $GetDetails[$i]['visitor_ID']?>&Visitor&rq" target="_blank"><?php echo $GetDetails[$i]['VName']; ?></a>
						<td><?php echo $GetDetails[$i]['motpstatus'];?></td><?php }else{ echo  $GetDetails[$i]['VName'];}?></td>
                         <?php } ?>
						<?php if(isset($_REQUEST['Visitor'])){?>
                         <td><?php echo $GetDetails[$i]['unit_no'];?></td>
                         <td><?php echo $GetDetails[$i]['Owner_name'];?></td>
						 <td><?php echo $GetDetails[$i]['approvalstatus']?></td>
                         <td><?php echo $GetDetails[$i]['purpose'];?></td>
						<td><?php echo $GetDetails[$i]['Company'] ?></td>
				<?php } ?>
                         <?php }?>
                         
                         <?php  if($var==1)
						 {
							 $categ=$ObjSMReport->cat($GetDetails[$i]['cat_id']);
						?>
                 	 <td style="padding-left:42px;"><?php echo $categ[0]['cat'];?></td>
                    <?php } ?>
                         <td style="padding-left:42px;"><?php echo $InDate.'-'.$InMonth.'-'.$InYear ;?></td>
                         <td style="padding-left:42px;"><?php echo $Intime;?></td>
                         <td style="padding-left:42px;"><?php echo $GetDetails[$i]['Entry_Gate'];?></td>
						<?php if($GetDetails[$i]['approvalstatus'] !="Denied"){
						if($OutDate=="" && $OutMonth=="" && $OutYear=="")
						{?> <td></td>
						<?php
						}
							 else
							 {?>
				
                         <td style="padding-left:42px;"><?php echo $OutDate.'-'.$OutMonth.'-'.$OutYear ;?></td>
				<?php }?>
                         <td style="padding-left:42px;"><?php echo $Out_time;?></td>
				<?php if($GetDetails[$i]['Exit_Gate']=="0")
							 {?> <td></td>
				<?php } else {?>
                         <td style="padding-left:42px;"><?php echo $GetDetails[$i]['Exit_Gate'];?></td>
				<?php } ?>
						<?php if($GetDetails[$i]['TotalTime']=="Wrong Entry" && $GetDetails[$i]['Exit_Gate']=="0" && $GetDetails[$i]['approvalstatus'] !="Denied" )
						 {?> <td style="padding-left:42px;"><?php echo "Still Inside"?></td>
						<?php } else {?>
                         <td style="padding-left:42px;"><?php echo $GetDetails[$i]['TotalTime']; }?></td>
						<?php } else { ?>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
				<?php } ?>
                   </tr>
				<?php } ?>	
      			</tbody>
            </table>
           
        </div>
         
         
         
         
    </div>
    
    </div>

            <?php }
		if($_POST['radio']=="detailview")
		{
			
            $GetDetails = $ObjSMReport->GetStaffDetails($_POST['from_date'],$_POST['cat'],$_POST['to_date']);
		if($_POST['cat']=="")
			{
			?>
		<script>alert("Kindly select category!");</script>
		
		<?php 
		}
		else if($GetDetails=="")
			{
			?>
		<script>alert("No Data Available in table");</script>
		
		<?php 
		}
		else
		{
	if($GetDetails=="0")
	{	?>
		<script>alert("Kindly select date of difference with count less than 30 or 31!");</script>
		
		<?php }
		else{?>
    <div class="panel-body">  
		<input type="button" id="btnExport" value="Export To Excel" class="btn btn-primary" onClick="Expoort()" style="display: block;float: right">
		<input type="button" id="btnpdf" value="Print/Export To PDF" class="btn btn-primary"  style="display: block;float: right;margin-right: 10px" onClick="PrintPage()">
		<br/>
		<br/>
        <div>
		<br/> 
			<br/>
            <?php
			for($i = 0 ; $i < sizeof($GetDetails); $i++)
					{
				$count=$GetDetails[$i]['count'];
							$start=$GetDetails[$i]['start'];
				$count=$count+1;
							$month=$GetDetails[$i]['month'];
							$end=$GetDetails[$i]['end'];
							if($start=="01"){$start="1";}
							if($start=="02"){$start="2";}
							if($start=="03"){$start="3";}
							if($start=="04"){$start="4";}
							if($start=="05"){$start="5";}
							if($start=="06"){$start="6";}
							if($start=="07"){$start="7";}
							if($start=="08"){$start="8";}
							if($start=="09"){$start="9";}
				$date1=$GetDetails[$i]['fromdate'];
				$date2=$GetDetails[$i]['toDate'];
					 } ?>
			<div id="staffattendance" style="width: 100%;">
				<div id="soc_name" style="display: none">
					<br/>
					<p style="text-align: center;font-size: 30px"><?php echo $sname ?></p>
					<p style="text-align: center;font-size: 20px">Staff Attendance Report</p>
				
				</div>
			
			<h5 align="center">From : <?php echo $date1 ?>  To : <?php echo $date2 ?></h5>
				<div id="staff" style="width:100%">
				<h6  style="float:right;text-align: right" >Name of in-charge:______Name of Area Officer:______</h6>
				
				 <table style="width:100%; border:1px solid black; background-color:transparent;" cellspacing="0">
				
               <thead>    
				   
                   <tr style="border:1px solid black" >
					 <th style="border:1px solid black" >Sr. No.</th>
                     <th style="border:1px solid black">Staff Name</th>
					   <?php
						$c=0;
						$test;
						if($start >= $end)	
						{
							if(($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12"))
						   {
							   $test=31;
						   }
	   		 			   if(($month=="02")){$test=28;}
			  			   if(($month == "04" || $month=="6"|| $month=="09"|| $month=="11"))
						   {
							  $test=30;
						   }
							for($j=$start;$j<=$test;$j++)
							{
								
							if($j=="1" || $j=="2"||$j=="3" || $j=="4"||$j=="5" || $j=="6"||$j=="7" || $j=="8"||$j=="9")
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo "0".$j?>&nbsp;</th>
						<?php 
							}
							else
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo $j ?>&nbsp;</th>
						
					   <?php
							}
								
							}
							for($j=1;$j<=$end;$j++)
							{
								
							if($j=="1" || $j=="2"||$j=="3" || $j=="4"||$j=="5" || $j=="6"||$j=="7" || $j=="8"||$j=="9")
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo "0".$j  ?>&nbsp;</th>
						<?php 
							}
							else
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo $j?>&nbsp;</th>
						
					   <?php
							}
								
							}
							
						}
						else
						{	
						for($j=$start;$j<=$end;$j++)
						{
						$c++;
							if($c <=$count)
							{
							if(($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12") && ($j>31))
						   {
							   $j=1;
						   }
	   		 			   if(($month=="02")&& ($j>28)){$j=1;}
			  			   if(($month == "04" || $month=="06"|| $month=="09"|| $month=="11") && ($j > 30))
						   {
							   $j=1;
						   }
							if($j=="1" || $j=="2"||$j=="3" || $j=="4"||$j=="5" || $j=="6"||$j=="7" || $j=="8"||$j=="9")
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo "0".$j ?>&nbsp;</th>
						<?php 
							}
							else
							{
								?>
					   <th style="border:1px solid black ;" >&nbsp;<?php echo $j ?>&nbsp;</th>
						
					   <?php
							}
							}
							else
							{
								break;
							}
						}
						}
						?>
					   <th style="border:1px solid black">&nbsp;Present&nbsp;</th>
					   <th style="border:1px solid black">&nbsp;Absent&nbsp;</th>
					   <th style="border:1px solid black">&nbsp;Total&nbsp;</th>
					 </thead>  
					</tr> 
                    <?php
							for($i = 0 ; $i < sizeof($GetDetails); $i++)
							{
								
						?>
				   <tr style="border:1px solid black">
					   <td style="border:1px solid black" align="center" ><?php echo $i+1 ?></td>
						<td style="border:1px solid black" ><?php echo $GetDetails[$i]['Staff_Name'] ?></td>
					<?php
						if($start >= $end)	
						{
						if(($month == "01" || $month=="03"|| $month=="05"|| $month=="07"|| $month=="08" || $month=="10"|| $month=="12"))
						   {
							   $test=31;
						   }
	   		 			   if(($month=="02")){$test=28;}
			  			   if(($month == "04" || $month=="6"|| $month=="09"|| $month=="11"))
						   {
							  $test=30;
						   }
							for($j=$start;$j<=$test;$j++)
							{
							if($GetDetails[$i]['Attendance'][$j]=="A")
							{
						?><td style="border:1px solid black;color:red" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   <?php
							}
								else
								{
									?>
					   <td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   
					   <?php
								}
							}
							for($j=1;$j<=$end;$j++)
							{ if($GetDetails[$i]['Attendance'][$j]=="A")
							{
						?><td style="border:1px solid black;color:red" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   <?php
							}
								else
								{
									?>
					   <td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   
					   <?php
								}							
							}
						}
								else
								{
									for($j=$start;$j<=$end;$j++)
									{
							if($GetDetails[$i]['Attendance'][$j]=="A")
							{
						?><td style="border:1px solid black;color:red" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   <?php
							}
								else
								{
									?>
					   <td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['Attendance'][$j] ?></td>
					   
					   <?php
								}
								}
								}
						?>
					   <td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['present'] ?></td>
					   <td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['absent'] ?></td>
					   	<td style="border:1px solid black" align="center"><?php echo $GetDetails[$i]['count'] ?></td>
					   </tr>
						<?php } ?>
			       
			</table>
            <br/>
				<h6 style="float: right;text-align: right">Verified By : _____________ Signature : _____________ Siganture of in charge : _____________</h6>
                <br/>
		</div>
        <?php }
            	}
		}
		?>
		
		
		<?php } ?>
 

</div>
<script>
<?php if(isset($_POST['from_date']) && isset($_POST['to_date']))
{?>
	document.getElementById('from_date').value = "<?php echo $_POST['from_date']; ?>";
	document.getElementById('to_date').value = "<?php echo $_POST['to_date']; ?>";
<?php } ?>
<?php if(isset($_POST['cat'])){?>
	document.getElementById('cat').value = "<?php echo $_POST['cat']; ?>";
<?php } ?>

</script>
</div>
</div>
</body>
<?php include_once "includes/foot.php"; ?>		
   