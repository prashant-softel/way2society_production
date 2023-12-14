 <?php //include_once "ses_set_s.php"; 
 error_reporting(0);
?>
<?php 
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
include_once("classes/reportdashboard.class.php");

//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);

//****Making Object of SM_Report contructor class 

$obj_utility = new utility($dbConn);
$obj_rep_dash=new Report_Dash($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$restotalflats=$obj_rep_dash->TotalFlat();
$new_today_date = date('Y-m-d');
//$yesterday=date('Y-m-d',strtotime("-1 days"));
//echo "==========>".sizeof($staffcat);
$getpcountcard=$obj_rep_dash->getAllPurposeCard($new_today_date);
//print_r($getpcountcard);
//$yesterday=date('Y-m-d',strtotime("-1 days"));
//echo "==========>".sizeof($staffcat);
$staffcat=$obj_rep_dash->StaffCategory($new_today_date);

$staffselcat=$obj_rep_dash->StaffselCategory();
//print_r($staffcat);
 $today_date = date('F jS Y');
 $page_for_daterange="/beta_import/StaffGReport.php";
 //echo "--------------->".$_REQUEST['startdate'];
 //echo "--------------->".$_REQUEST['enddate'];
 // Declare an empty array 
$array = array(); 
if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>'')
{
	// Use strtotime function 
	$Date1=$_REQUEST['startdate'];
	$Date2=$_REQUEST['enddate'];
	
	$Variable1 = strtotime($Date1); 
	$Variable2 = strtotime($Date2); 
	$cal1=date('d/m/y',$Variable1);
	$cal2=date('d/m/y',$Variable2);
  
  $daterange1=date('m/d/Y',$Variable1);
   $daterange2=date('m/d/Y',$Variable2);
	// Use for loop to store dates into array 
	// 86400 sec = 24 hrs = 60*60*24 = 1 day 
	for ($currentDate = $Variable1; $currentDate <= $Variable2;  
                                $currentDate += (86400)) { 
		$Store = date('Y-m-d', $currentDate); 
		$VStore=date('d/m',$currentDate);
		//'Y-m-d'
		//$array[] = $Store; 
		array_push($array,array("store"=>$Store,"viewdt"=>$VStore));
	} 
  
	// Display the dates in array format 
	//print_r($array); 
}
/*$getstaffcount=$obj_rep_dash->StaffCountattdetails($_REQUEST['startdate'],$_REQUEST['enddate'],$_REQUEST['catval'],$staffID);
for($i=0;$i<sizeof($getstaffcount);$i++)
{
		echo "<br>Check absent:".$staffabs=sizeof($array)-$getstaffcount[$i]['scount'];
}*/

//print_r($array);
//echo"===================>".(sizeof($array));
$Allpurpose=$obj_rep_dash->getAllPurpose();
//echo "------------------------------------>".$_REQUEST['Sid'];
//print_r($Allpurpose);
$staffID = 0;	
if(isset($_REQUEST['Sid']) && $_REQUEST['Sid'] <> '')
		{
			//*** Set the visitor id for requested visitor
			$staffID = $_REQUEST['Sid'];
			
			
		}
//$getstaffcount=$obj_rep_dash->StaffCountattdetails($_REQUEST['startdate'],$_REQUEST['enddate'],$_REQUEST['catval'],$staffID);
//print_r($getstaffcount);


/*for($i=0;$i<sizeof($array);$i++){	
	$getstaffcount=$obj_rep_dash->StaffCountdetails($array[$i]['store'],$_REQUEST['catval'],$staffID);
}
//echo "check=================>".print_r($getstaffcount);
*/

$getCatName=$obj_rep_dash->getCatName($_REQUEST['catval']);
//print_r($getCatName);
?>
<html>
<head>  

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>
		.main_block{
			width:33%;
			border:0px solid #000;
			text-align:center;
			vertical-align:top;
			border-radius:15px;
			height:175px;
		}
	.main_div{
			background-color:#FFFFFF;
			border-radius:15px;
			width:80%;
			border:1px solid #333;
			margin:auto;
			min-height:100%;
			height:175px;
			box-shadow: 8px 8px 7px #888888;
		}
	.main_head{
			/*background:#990000;*/
			border-top-left-radius:15px;
			border-top-right-radius:15px;
			color:#000;
			/*font-size:16px;*/
			font-weight:bold;
			padding:3px;
			padding-right:10px;
			text-align:right;
		 	height:10px;
			text-decoration:underline;
		}
	.main_data{
			background:none;
			color:#000;
			/*font-size:12px;*/
			text-align:center;
			height:81px;
		}
	.main_footer{
			background:#990000;
			border-bottom-left-radius:12px;
			border-bottom-right-radius:12px;
			color:#00F;

/*			font-size:12px;*/
			font-weight:bolder;
			text-align:center;
			height:30px;
			display:table;
			width:100%;
		}
		
	.main_footer, a{
			
		}
	 .Details
	 {
		 color:#FFF;
	 }
	 .canvasjs-chart-credit
	 {
		position: unset !important; 
	 }
	.date-ranger{
		display:block;
	width:12em;
	height:2em;
	background:#003366;
	color:#333;
	line-height:2;
	text-align:center;
	text-decoration:none;
	font-weight:200;
		}
	</style>
    <script>
    function ShowMemberView(SelectedTab)
	{
		window.location.href = "Dashboard.php?View=MEMBER";
		//location.reload(true);
		
	}
	
	function ShowAdminView(SelectedTab)
	{
		//alert("test");
		
		
		window.location.href = "home_s.php?View=ADMIN";
//		//location.reload(true);
	}
	</script>
    
 <script type="text/javascript" src="js/datepicker2/moment.min.js"></script>
  <script type="text/javascript" src="js/datepicker2/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="csss/daterangepicker.css" />
  <script type="text/javascript" src="js/canvasjs.min.js"></script>

   <script type="text/javascript">
    $(document).ready(function()
    {
		<?php if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>'')
{?>
		var sdaterg1=document.getElementById("datergstart").value;
		var edaterg1=document.getElementById("datergend").value;
<?php }?>
      $('#daterange').daterangepicker({
		  							<?php if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>''){?>
										startDate: sdaterg1,
                                        endDate: edaterg1,
									<?php }else{ ?>
                                        startDate: moment().subtract('days', 29),
                                        endDate: moment(),
									<?php } ?>
                                        minDate: '01/01/2012',
                                        maxDate: '12/31/2060',
                                        showDropdowns: true,
                                        showWeekNumbers: true,
                                        timePicker: false,
                                        timePickerIncrement: 1,
                                        timePicker12Hour: false,
                                        opens: 'left',
                                        buttonClasses: ['btn btn-default'],
                                        applyClass: 'btn-small btn-primary',
                                        cancelClass: 'btn-small',
                                        format: 'DD/MM/YYYY',
                                        separator: ' to ',
                                        locale: {
                                                  applyLabel: 'Submit',
                                                  cancelLabel: 'Clear',
                                                  fromLabel: 'From',
                                                  toLabel: 'To',
                                                  customRangeLabel: 'Custom',
                                                  daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                                                  monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                                  firstDay: 1
                                                },
                                        ranges: {
                                                 'Today': [moment(), moment()],
                                                 'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                                                 'Last 7 Days': [moment().subtract('days', 6), moment()],
                                                 'This Month': [moment().startOf('month'), moment().endOf('YYYY-MM-DD')],
                                                 'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                                                },
                                        opens: 'left',
                                        showDropdowns: true
                                      },
                                      function(start, end)
                                      {
                                        //var url=window.location.href;
										
										url = 'StaffGReport.php?startdate=' + start.format('YYYY-MM-DD') + '&enddate=' + end.format('YYYY-MM-DD');
										
										var m=document.getElementById("cat").value;	
										
											url+='&catval='+m;
										
										
										window.location.href = url;
										//document.getElementById("cat_label").style.display="block";
										//document.getElementById("cat").style.display="block";
										
                                      }
					
									  );
									  
									  
							
    });
			  
  </script>
  <script>
  <?php if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>'')
{?>
  function mytestFunction() {
 	 var k=document.getElementById("cat").value;
  	//alert(k);
	var sdate=document.getElementById("strtdate").value;
	var edate=document.getElementById("enddate").value;
	
	
	url = 'StaffGReport.php?startdate=' +sdate+ '&enddate='+edate;
							
		url+='&catval='+k;

	
	window.location.href = url;
	
	
  }
  <?php }?>
 
    //var n=document.getElementById("catvalue").value;
	//alert(n)
  	//document.getElementById("cat").value=n;
   
  </script>
</head>
<body>

	
<!--<select id="colors" style="margin-left : 350px">
  <option value="1">Red</option>
  <option value="2">Blue</option>
  <option value="3">Green</option>
  <option value="4">Yellow</option>
  <option value="5">Orange</option>
</select>-->


<center>

<table style="width:100%;display:none;width:60vw" id="table1">
	<tr>
    <!-- First card -->
    	<td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
				<div class="panel panel-red" style="border-color:#D9524F">
					<div class="panel-heading">
						<div class="row">
                            <table style="width:100%">
							<tr>
                                <td><i class="fa fa-group" style="font-size:10px;font-size:3.75vw"></i></td>
                                <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                               STAFF ON DUTY  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </td>
							</tr>
							</table>
							<div class="col-xs-9 text-right" style="width:100%">   
                            <marquee HEIGHT=78px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>                                 
							
							
                                <table style="width:100%;">
									<div  class="huge" style="font-size:30px">
                                    <?php if(sizeof($staffcat)>0){
										for($i=0;$i<sizeof($staffcat);$i++){?>
                                       <tr>
                                     
											<td style="width:60%;text-align:left;font-size:1.00vw;"><?php echo $staffcat[$i]['cat'];?></td>
											<td style="width:5%;">:</td>
											<td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw">IN(<?php echo $staffcat[$i]['cat_incount'];?>)/OUT(<?php echo $staffcat[$i]['cat_outstaff'];?>)</td>
                                            <?php 
											}
											}
										   else{?>
                                            <td style="width:60%;text-align:left;color:#ffffff;font-size:1.3vw; float: left;
    margin-left: 20%;">No Staff At Present</td>
	<?php }?>	<tr>
										<td colspan="3"><br></td>
										</tr>
                                       <!--<tr><td style="width:60%;text-align:left;font-size:1.00vw;">Album</td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw">0</td></tr>-->
                                    </div>
                                      
								</table>
								
                                 </marquee>
                            </div>
                        </div>
                	</div>
					
                    
                   
                	<div class="panel-footer">
                            <span class="pull-left" style="font-size:10x;font-size:1.00vw;color:#d9534f">Staff Details</span>
                            
                             <div class="clearfix"></div>
                        </div>
                       
                    
                      
				</div>
			</div>
        </td>
        
        
        
         <!-- Second  card in row 1 -->
        <td style="width:33%">
			<div class="col-lg-3 col-md-6" style="width:100%">
				<div class="panel panel-green" style="border-color: #ccc;">
					<div class="panel-heading">
						<div class="row">
                            <table style="width:100%">
							<tr>
                                <td><i class="fa fa-home" style="font-size:10px;font-size:3.75vw"></i> </td>
                                <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                               	Visitors In &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
                                </td>
							</tr>
							</table>
                            
							<div class="col-xs-9 text-right" style="width:100%">  
                             <marquee HEIGHT=78px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>                                   
								<table style="width:100%;">
									<div  class="huge" style="font-size:30px">
                                   <?php if(sizeof($getpcountcard)>0){
									
										for($i=0;$i<sizeof($getpcountcard);$i++){?>
                                       <tr>
                                       <?php 
									   if($getpcountcard[$i]['Ctotpeople']<>0){
									   ?>
                                       
											<td style="width:60%;text-align:left;font-size:1.00vw;"><?php echo $getpcountcard[$i]['purpose_name'];?></td>
											<td style="width:5%;">:</td>
											<td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw">IN(<?php echo $getpcountcard[$i]['CpeopleInside'];?>)/Total(<?php echo $getpcountcard[$i]['Ctotpeople'];?>)</td>
                                            <?php 
                                  
									   }
											}
									}else{?>
                                            <td style="width:60%;text-align:left;color:#ffffff;font-size:1.3vw; float: left;
    margin-left: 20%;">No Visitors At Present</td>
	<?php }?>
									         
										<tr>
										<td colspan="3"><br></td>
										</tr>
                                       <!--<tr><td style="width:60%;text-align:left;font-size:1.00vw;">Album</td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw">0</td></tr>-->
                                    </div>
                                      
								</table>
   </marquee>
							</div>
                        </div>
                	</div>
                   <div class="panel-footer">
                            <span class="pull-left" style="font-size:10x;font-size:1.00vw;color:#5cb85c">Visitor Details </span>
                             <div class="clearfix"></div>
                        </div>
                   
				</div>
			</div>
		</td>
        
      
         </tr>
  
</table>


</div>
</table>
<table style="width:100%;display:none;width:75vw" id="table2">
<tr><div id="dropdown" style="margin-left:10%;width:100%;">

<td style="text-align:center">

<label id="cat_label" style="margin-left:50%"><b>PLEASE SELECT STAFF CATEGORY</b></label>

<!--<select name="cat" id="cat" onchange="mytestFunction()"  style="width:250px;font-size: 15px;margin-left:10%;height:10%;" >
             <?php //$qry1 = "SELECT `cat_id`, `cat`, `status` FROM `cat` WHERE `status`='Y' ORDER BY `cat`.`cat` ASC";
			//echo $obj_rep_dash->combobox($qry1,0); ?>
			</select>-->
     <td>       <select name="cat" id="cat" onchange="mytestFunction()"  style="width:250px;font-size: 15px;margin-left:10%;height:10%;">
             <option value="all">All Categories</option>
			 <?php for($i=0;$i<sizeof($staffselcat);$i++){?>
              <?php if($staffselcat[$i]['cat_count']<>0){?>
             <option value="<?php echo $staffselcat[$i]['cat_id'];?>"><?php echo $staffselcat[$i]['cat'];?></option>
             <?php } ?>
             <?php } ?>
			</select>
            
            <div class="col-sm-6 pull-right no-padder m-t-xs">
                       
           <div class="date-picker" "margin-right:50%>
         <div id="daterange" class="date-ranger" style="background-color:#1f64a9">
     <?php if($_REQUEST['startdate'] =='' && $_REQUEST['enddate'] ==''){?>
              <i class="glyphicon glyphicon-calendar fa fa-calendar calender-icons" style="color:#FFF"></i><span style="color:#FFF"><?php echo $today_date; ?></span> <b class="caret" style="color:#FFF"></b>
              <?php }
			    else if($_REQUEST['startdate'] ==$_REQUEST['enddate']){?>
              <i class="glyphicon glyphicon-calendar fa fa-calendar calender-icons" style="color:#FFF"></i><span style="color:#FFF"><?php echo $cal2; ?></span> <b class="caret" style="color:#FFF"></b>
              <?php }
              else{?>
              <i class="glyphicon glyphicon-calendar fa fa-calendar calender-icons" style="color:#FFF"></i><span style="color:#FFF"><?php echo $cal1;echo "-".$cal2; ?></span> <b class="caret" style="color:#FFF"></b>
              <?php } ?>
            </div>
          </div>
        </div>

            </td></tr></table>
            <input type="hidden" id="strtdate" name="strtdate" value="<?php echo $_REQUEST['startdate'];?>"/>
            <input type="hidden" id="datergstart" name="datergstart" value="<?php echo $daterange1;?>"/>
            <input type="hidden" id="enddate" name="enddate" value="<?php echo $_REQUEST['enddate'];?>"/>
            <input type="hidden" id="datergend" name="datergend" value="<?php echo $daterange2;?>"/>
             <input type="hidden" id="catvalue" name="enddate" value="<?php echo $getCatName[0]['cat_name'];?>"/>
            
           </td> </div>

</tr>
<tr><td><br/></td></tr>
<tr><td><br/></td></tr>

 <?php if($_REQUEST['catval']<>''){?>

<tr><td>

<?php 
							
							$dataPointstaff  = array();
							for($i=0;$i< sizeof($array);$i++)
							{
								$getstaffcount=$obj_rep_dash->StaffCountdetails($array[$i]['store'],$_REQUEST['catval'],$staffID);
								$arTemp = array("label"=> $array[$i]['viewdt'], "y"=> $getstaffcount[0]['allstaff']);
								//print_r($arTemp);
								array_push($dataPointstaff, $arTemp);
							}
							//echo $dataPoint[0]["y"];
							?>
  <div id="chartContainerstaffall" style="height: 250px; width: 100%;margin-left:10%"></div>
  </td></tr>
<tr><td><br/></td></tr>

 
<tr><td>
<?php 
							
							$dataPointstaffatt  = array();
							$dataPointstaffabs  = array();
								$getstaffcount=$obj_rep_dash->StaffCountattdetails($_REQUEST['startdate'],$_REQUEST['enddate'],$_REQUEST['catval'],$staffID);
								for($i=0;$i<sizeof($getstaffcount);$i++){
									$arTemp = array("label"=> $getstaffcount[$i]['staffname'], "y"=> $getstaffcount[$i]['scount']);
									
									array_push($dataPointstaffatt, $arTemp);
								}
								
								
								for($i=0;$i<sizeof($getstaffcount);$i++){
									$staffabs=sizeof($array)-$getstaffcount[$i]['scount'];
									$arTemp2 = array("label"=> $getstaffcount[$i]['staffname'], "y"=> $staffabs);
									
									array_push($dataPointstaffabs, $arTemp2);
								}
								
							
							//echo $dataPoint[0]["y"];
							//print_r($dataPointstaffatt);
							//print_r($dataPointstaffabs);
							?>
      <?php if(sizeof($dataPointstaffatt)<>0){?>
  <div id="chartContainerstaffname" style="height: 300px; width: 100%;margin-left:10%"></div>
  <?php }?>
  </td></tr>
  <?php }?>
<tr><td><br/></td></tr>
</table>
<!--- ------------------------   Chart Popup ------------------------ -->

<!--    ---------------First Popup  --------------- -->
<div id="myModal" class="modal">

  <div class="modal-content">
    <span class="close">&times;</span>
    <p style="font-size: 17px;text-align: center;font-weight: bold;color: blue;">EXPENDITURE</p>
    <div class="panel panel-default"> 
 <div id="chartContainer3" style="height: 360px; width: 100%;"></div>
  </div>
  </div>
</div>

<!--    ---------------second Popup  --------------- -->
<div id="myModal1" class="modal">
 <div class="modal-content">
    <span class="close1">&times;</span>
    <p style="font-size: 17px;text-align: center;font-weight: bold;color: blue;">PAYMENTS</p>
    <div class="panel panel-default"> 
 <div id="chartContainer4" style="height: 360px; width: 100%;"></div>
  </div>
  </div>

</div>
<!--    ---------------Third Popup  --------------- -->
<div id="myModal2" class="modal">
 <div class="modal-content">
    <span class="close2">&times;</span>
    <p style="font-size: 17px;text-align: center;font-weight: bold;color: blue;">SERVICE REQUEST</p>
    <div class="panel panel-default"> 
 <div id="chartContainer5" style="height: 360px; width: 100%;"></div>
  </div>
  </div>

</div>
<script>
<?php if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>'')
{
 ?>
window.onload = function () {
<?php if($_REQUEST['catval']){?>
 document.getElementById('cat').value = "<?php echo $_REQUEST['catval'];?>";
 <?php }?>
 var catname=document.getElementById("catvalue").value;

<?php if($_REQUEST['catval']<>''){?>
var chart3 = new CanvasJS.Chart("chartContainerstaffall", {
	animationEnabled: true,
	theme: "light2",
	title: {
		text: "Staff Report"
	},
	axisX:{
		title: "Date",
		titleFontColor: "rgba(38, 146, 176, 0.8)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 13,
	},
	axisY: {
		title: "Number of Staff's Per day",
		titleFontColor: "rgba(38, 146, 176, 0.8)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 13,
	},
	data: [{
		type: "line",
		indexLabel: "{y}",
		dataPoints: <?php echo json_encode($dataPointstaff, JSON_NUMERIC_CHECK); ?>
	}]
});
chart3.render();


var chart4 = new CanvasJS.Chart("chartContainerstaffname", {
	animationEnabled: true,
	theme: "light2",
	title:{
		
		text:  catname+" Attendance Report"
	},
	legend:{
		cursor: "pointer",
		verticalAlign: "center",
		horizontalAlign: "right",
		itemclick: toggleDataSeries
	},
	axisX:{
		title: "Staff",
		titleFontColor: "rgb(106, 90, 205)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 12,
	},
	axisY: {
		title: "Staff Attendance",
		titleFontColor: "rgb(106, 90, 205)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 13,
	},
	data: [{
		type: "column",
		name: "present",
		color: "#4CAF50",
		indexLabel: "{y}",
		showInLegend: true,
		dataPoints: <?php echo json_encode($dataPointstaffatt, JSON_NUMERIC_CHECK); ?>
	},{
		type: "column",
		name: "absent",
		color: "rgba(255, 99, 71, 0.7)",
		indexLabel: "{y}",
		showInLegend: true,
		dataPoints: <?php echo json_encode($dataPointstaffabs, JSON_NUMERIC_CHECK); ?>
	}]
});
chart4.render();
 
function toggleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else{
		e.dataSeries.visible = true;
	}
	chart4.render();
}
<?php } ?>
 
}
<?php }?>
</script>


 <!--<div id="mySidenav" class="sidenav">
  <a href="IncomeDetails.php" id="about"><span class="fa fa-plus-square fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;INCOME</span></a>
  <a href="AssetSummary.php" id="blog"><span class="fa fa-cubes fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;ASSETS</span></a>
  <a href="LiabilitySummary.php" id="projects"><span class="fa fa-exclamation-triangle fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;LIABILITIES</span></a>
</a>
  <!--<a href="#" id="contact">Contact</a>-->
<!--</div>-->
<!--<script>
window.onload = function ()
{
	
 	var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	//title:{
							
		//},
		data: [{
				type: "column", 
				indexLabel: "{y}", //Shows y value on all Data Points
				indexLabelFontColor: "#5A5757",
				dataPoints: 
				}]
			});
			chart.render();
			var chart1 = new CanvasJS.Chart("chartContainer1", {
				theme: "theme2",
				animationEnabled: true,
				///title: {
							//text: "World Energy Consumption by Sector - 2012"
						// },
						legend: {
			maxWidth: 350,
			itemWidth: 120
		},
				data: [{
						type: "pie",
						indexLabel: "{label} ({y})",
						
						//indexLabelPlacement: "inside",
						
						showInLegend: true,
						//legendText: "{label}",
						legendText: "{indexlabel}",
						dataPoints: 						}]
				});
						chart1.render();			
			var chart2 = new CanvasJS.Chart("chartContainer2", {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			//title:{
							
				//},
		data: [{
				type: "column", 
				indexLabel: "{y}", //Shows y value on all Data Points
				indexLabelFontColor: "#5A5757",
				dataPoints:
				}]
			});
			chart2.render();
			var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	//title:{
							
		//},
		data: [{
				type: "column", 
				indexLabel: "{y}", //Shows y value on all Data Points
				indexLabelFontColor: "#5A5757",
				dataPoints: 
				}]
			});
			chart.render();
			// Open Popup windows 
			var chart3 = new CanvasJS.Chart("chartContainer3", {
				theme: "theme2",
				animationEnabled: true,
				///title: {
							//text: "World Energy Consumption by Sector - 2012"
						// },
						//legend: {
			//maxWidth: 450,
			//itemWidth: 120
		//	},
				data: [{
						type: "pie",
						indexLabel: "{label} ({y})",
						
						//indexLabelPlacement: "inside",
						
						showInLegend: true,
						//legendText: "{label}",
						indexLabel: "{label} - #percent%",
						legendText: "{indexlabel}",
						dataPoints: 
						}]
				});
			chart3.render();	
			
			var chart4 = new CanvasJS.Chart("chartContainer4", {
	animationEnabled: true,
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	//legend: {
		//	maxWidth: 450,
			//itemWidth: 120
			//},
		data: [{
				type: "column", 
				indexLabel: "{y}", //Shows y value on all Data Points
				indexLabelFontColor: "#5A5757",
				dataPoints: <?php// echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart4.render();
			
			var chart5 = new CanvasJS.Chart("chartContainer5", {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			//title:{
							
				//},
		data: [{
				type: "column", 
				indexLabel: "{y}", //Shows y value on all Data Points
				indexLabelFontColor: "#5A5757",
				dataPoints:
				}]
			});
			chart5.render();
 	}
</script>-->
<script type="text/javascript" src="js/home_s.js"></script>


<?php include_once "includes/foot.php"; ?>
<script>

function myFunction() 
{
	$("#table1").fadeIn(2000);
	$("#table2").fadeIn(2000);
	$("#table3").fadeIn(2000);
}
myFunction();
</script>