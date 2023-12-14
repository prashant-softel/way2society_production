<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Security Manager Dashbord</title>
</head>
 <?php //include_once "ses_set_s.php"; 
 error_reporting(1);
?>
<?php 
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
//include_once("classes/utility.class.php");
include_once("classes/reportdashboard.class.php");

//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);

//****Making Object of SM_Report contructor class 

//$obj_utility = new utility($dbConn);

$obj_rep_dash=new Report_Dash($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$restotalflats=$obj_rep_dash->TotalFlat();

$new_today_date = date('Y-m-d');
$getpcountcard=$obj_rep_dash->getAllPurposeCard($new_today_date);
//var_dump($getpcountcard);
//$yesterday=date('Y-m-d',strtotime("-1 days"));
//echo "==========>".sizeof($staffcat);
$staffcat=$obj_rep_dash->StaffCategory($new_today_date,$new_today_date,4);

//echo "<BR>SP Attendance";
//var_dump($staffcat);
$staffselcat=$obj_rep_dash->StaffselCategory();

$visitordata=$obj_rep_dash->getVisitors($new_today_date,$new_today_date,1);
//echo "<BR>visitor scroll data";
//var_dump($visitordata);

//$OnDutyStaffList  =$obj_rep_dash->getStaffDetails("2020-06-07", "2020-06-07");

$OnDutyStaffList = $obj_rep_dash->StaffAttDetails($new_today_date,$new_today_date,"all",0,3);
//echo "<BR>On duty staff list";
//var_dump($OnDutyStaffList );

 $today_date = date('F jS Y');
 $page_for_daterange="/beta_import/ReportDashboard.php";
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
	background:#000099;
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
										
										url = 'ReportDashboard.php?startdate=' + start.format('YYYY-MM-DD') + '&enddate=' + end.format('YYYY-MM-DD');
										
										//var m=document.getElementById("cat").value;	
										
											//url+='&catval='+m;
										
										
										window.location.href = url;
										//document.getElementById("cat_label").style.display="block";
										//document.getElementById("cat").style.display="block";
										
                                      }
					
									  );
									  
									  
							
    });
			  
  </script>
  <script>
  // if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>''){?>
  /*function mytestFunction() {
 	 var k=document.getElementById("cat").value;
  	//alert(k);
	var sdate=document.getElementById("strtdate").value;
	var edate=document.getElementById("enddate").value;
	
	var url='<?php// echo $page_for_daterange?>';
	url += '?startdate=' +sdate+ '&enddate='+edate;
							
		url+='&catval='+k;

	
	window.location.href = url;
	
	
  }*/
  <?php// }?>
 
    //var n=document.getElementById("catvalue").value;
	//alert(n)
  	//document.getElementById("cat").value=n;
   
  </script>
</head>
<body>

	



<center>

<table style="width:100%;display:none;width:60vw" id="table1">

<!-- First card  Pie chart-->
	<tr>
        <td style="width:33%">
			<div class="col-lg-3 col-md-6" style="width:100%">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="row">
                                    <?php 
									//echo "<BR>test<BR>";

									$totalstaff = 0;
									$dataPoint_pieStaff  = array();
									//var_dump(sizeof($staffcat));
									if(sizeof($staffcat)>0){
										for($i=0;$i<sizeof($staffcat);$i++){?>
                                            <?php 
								if($staffcat[$i]['cat_incount']+$staffcat[$i]['cat_outstaff'] != 0)
								{
								$arpTemp = array("label"=> $staffcat[$i]['cat'],"y"=> $staffcat[$i]['cat_incount']+$staffcat[$i]['cat_outstaff']);				
								//echo "<BR> cat: ".$staffcat[$i]['cat'];
								//echo "<BR> count: ".$staffcat[$i]['cat_incount']+$staffcat[$i]['cat_outstaff'];
								$totalstaff = $totalstaff + $staffcat[$i]['cat_incount']+$staffcat[$i]['cat_outstaff'];
								array_push($dataPoint_pieStaff, $arpTemp);
								}
								//else echo "yoyo";
								//$arpTemp = array( "y"=> $getpcount[0]['ptotal'],"label"=> $Allpurpose[$i]['purpose_name']);
								
											}
//var_dump($dataPoint_pieStaff);
											}
										   ?>
 	                        	
<a href="StaffGReport.php?startdate=<?php echo $last_month;?>&enddate=<?php echo $today_datestaff;?>&catval=all" target="_blank">
							<table style="width:100%">
							<tr>
								<td><i class="fa fa fa-inr fa-5x" style="font-size:10px;font-size:3.75vw"></i></td>
								<td style="text-align:center;font-size:150%;vertical-align:middle;font-size:1.25vw;width:100%">
						 Service Providers Checked-in: <?php echo $totalstaff;?>&nbsp;&nbsp;
								</td>
							</tr>
							</table>
							</a>
							<div class="col-xs-9 text-right" style="width:100%"> 
								<table style="width:100%;">
								<tr>
									<td style="width:60%;text-align:left;font-size:1.00vw" >
										<div class="panel panel-default"> 
											<a href="#" id="myBtn"><div id="chartContainer2" style="height: 105px; width: 100%;" class="zoom"></div></a> 
								   
								
										</div>
									</td>
								</tr>
                                </table>
						  
							</div>
						</div>
					</div>
                
				</div>
			</div>
        </td>


        <td style="width:33%">
			<div class="col-lg-3 col-md-6" style="width:100%">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<div class="row">
							
							<?php 
							$totalvis = 0;
							$dataPoint_pie  = array();
							for($i=0;$i< sizeof($Allpurpose);$i++)
							{
								$getpcount=$obj_rep_dash->getAllPurposeCount($Allpurpose[$i]['purpose_id'],$new_today_date,$new_today_date,5);
								
								if($getpcount[0]['ptotal'] != 0){
										$arpTemp = array("label"=> $Allpurpose[$i]['purpose_name'],"y"=> $getpcount[0]['ptotal']);
								//$arpTemp = array( "y"=> $getpcount[0]['ptotal'],"label"=> $Allpurpose[$i]['purpose_name']);
								$totalvis = $totalvis + $getpcount[0]['ptotal'];
								array_push($dataPoint_pie, $arpTemp);
									}
								
							}
							//print_r($dataPointpurpose);
							?>
 
	                         <a href="Sm_Report.php?Visitor" target="_blank">
                                                     
							<table style="width:100%">
							<tr>
								<td><i class="fa fa fa-inr fa-5x" style="font-size:10px;font-size:3.75vw"></i></td>
								<td style="text-align:center;font-size:150%;vertical-align:middle;font-size:1.25vw;width:100%">
                                Visitor Count Today: <?php echo $totalvis;?>&nbsp;&nbsp;
								</td>
							</tr>
							</table>
							</a>
							<div class="col-xs-9 text-right" style="width:100%"> 
								<table style="width:100%;">
								<tr>
									<td style="width:60%;text-align:left;font-size:1.00vw" >
										<div class="panel panel-default"> 
											<a href="#" id="myBtn"><div id="chartContainer1" style="height: 105px; width: 100%;" class="zoom"></div></a> 
								   
								
										</div>
									</td>
								</tr>
                                </table>
						  
							</div>
						</div>
					</div>
                
				</div>
			</div>
        </td>

	</tr>    
    
    <!-- First card -->
    	<td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
				<div class="panel panel-red" style="border-color:#D9524F">
					<div class="panel-heading">
						<div class="row">
                            <table style="width:100%">
							<tr>
                                <td><i class="fa fa-group" style="font-size:10px;font-size:3.75vw"></i></td>
                                <td style="text-align:center;font-size:150%;vertical-align:middle;font-size:1.25vw;width:100%">
                               On Duty Service Providers: <?php echo sizeof($OnDutyStaffList);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </td>
							</tr>
							</table>
							<div class="col-xs-9 text-right" style="width:100%">   
                            <marquee HEIGHT=78px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>                                 
							
							
                                <table style="width:100%;">
									<div  class="huge" style="font-size:30px">
                                    <?php 
									
									if(sizeof($OnDutyStaffList)>0){
										for($i=0;$i<sizeof($OnDutyStaffList);$i++){?>
                                       <tr>
                                     
											<td style="font-weight:bold; width:60%;text-align:left;font-size:1.00vw;"><?php echo $OnDutyStaffList[$i]['staffname'];?></td>
                                            
                                            
											<td style="width:5%;">:</td>
											<td style="width:45%; text-align:right;;font-size:1.00vw"><?php echo $OnDutyStaffList[$i]['cat_name'];?></td>
                                            <?php 
											}
											}
										   else{?>
                                            <td style="width:60%;text-align:left;color:#ffffff;font-size:1.3vw; float: left;
    margin-left: 20%;">No Service Providers At Present</td>
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
					
                    
        			
                    
                    <?php if($_REQUEST['startdate']<>'' && $_REQUEST['enddate']<>''){
						$today_datestaff = date('Y-m-d');
						$last_month = date("Y-m-d", strtotime("-1 month"));

						?>
                    
                         <a href="StaffGReport.php?startdate=<?php echo $last_month;?>&enddate=<?php echo $today_datestaff;?>&catval=all" target="_blank">
                        <div class="panel-footer">
                            <span class="pull-left" style="font-size:10x;font-size:1.00vw">Staff Details</span>
                            <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                             <div class="clearfix"></div>
                        </div>
                    </a>
                   <?php }else{?>
                   	<div class="panel-footer">
                            <span class="pull-left" style="font-size:10x;font-size:1.00vw;color:black">Staff Details</span>
                            <span class="pull-right" style="color:black"><i class="fa fa-2x fa-arrow-circle-up"></i></span>
                             <div class="clearfix"></div>
                        </div>
                   <?php }?>

                       
                    
                      
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
                                <td style="text-align:center;font-size:150%;vertical-align:middle;font-size:1.25vw;width:100%">
                               	Visitors Inside: <?php echo sizeof($visitordata);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
                                </td>
							</tr>
							</table>
                            
							<div class="col-xs-9 text-right" style="width:100%">  
                             <marquee HEIGHT=78px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>                                   
								<table style="width:100%;">
									<div  class="huge" style="font-size:30px">
                                    <?php if(sizeof($visitordata)>0){
									
										for($i=0;$i<sizeof($visitordata);$i++){?>
                                       <tr>
                                       
											<td style="width:68%;text-align:left;font-size:1.00vw;font-weight:bold;"><?php echo $visitordata[$i]['visitor_name'];?><br/><div style ="font-weight:400;">(<?php echo $visitordata[$i]['purpose_name'];?>)</div></td>
											<td style="width:2%;">:</td>
											<td style="font-weight:bold; width:30%; text-align:right;;font-size:1.00vw"><?php echo $visitordata[$i]['indate'];?></td></tr>
                                            <?php 
                                  
									   
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
<!--Changed uptil here-->

</div>
</table>
<table style="width:100%;display:none;width:75vw" id="table2">
<tr><div id="dropdown" style="margin-left:10%;width:100%;">

<td style="text-align:center">   <label id="cat_label" style="margin-left:20%"><b>PLEASE SELECT DATE</b></label>
<div class="col-sm-6 pull-right no-padder m-t-xs">
                     
           <div class="date-picker" style="margin-right: 10px" >
         <div id="daterange" class="date-ranger">
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


<!--<select name="cat" id="cat" onchange="mytestFunction()"  style="width:250px;font-size: 15px;margin-left:10%;height:10%;" >
             <?php //$qry1 = "SELECT `cat_id`, `cat`, `status` FROM `cat` WHERE `status`='Y' ORDER BY `cat`.`cat` ASC";
			//echo $obj_rep_dash->combobox($qry1,0); ?>
			</select>-->
          <!--  <select name="cat" id="cat" onchange="mytestFunction()"  style="width:250px;font-size: 15px;margin-left:10%;height:10%;">
             <option value="all">All Categories</option>
			 <?php// for($i=0;$i<sizeof($staffcat);$i++){?>
              <?php// if($staffcat[$i]['cat_count']<>0){?>
             <option value="<?php// echo $staffcat[$i]['cat_id'];?>"><?php// echo $staffcat[$i]['cat'];?></option>
             <?php// } ?>
             <?php// } ?>
			</select>-->
            
            <input type="hidden" id="strtdate" name="strtdate" value="<?php echo $_REQUEST['startdate'];?>"/>
            <input type="hidden" id="datergstart" name="datergstart" value="<?php echo $daterange1;?>"/>
            <input type="hidden" id="enddate" name="enddate" value="<?php echo $_REQUEST['enddate'];?>"/>
            <input type="hidden" id="datergend" name="datergend" value="<?php echo $daterange2;?>"/>
             <input type="hidden" id="catvalue" name="enddate" value="<?php echo $getCatName[0]['cat_name'];?>"/>
            
           </td> </div>

</tr>
<tr><td><br/></td></tr>
<tr><td><br/></td></tr>
<tr><td>
<?php 
							
							$dataPoint  = array();
							for($i=0;$i< sizeof($array);$i++)
							{
								$getcount=$obj_rep_dash->getVisitorCount($array[$i]['store']);
								$arTemp = array("label"=> $array[$i]['viewdt'], "y"=> $getcount[0]['total']);
								array_push($dataPoint, $arTemp);
							}
							//echo $dataPoint[0]["y"];
							?>
  <div id="chartContainerline" style="height: 250px; width: 100%;margin-left:10%"></div>
  </td></tr>
  <tr><td><br/></td></tr>
<tr><td>
<?php 
							
							$dataPointpurpose  = array();
							for($i=0;$i< sizeof($Allpurpose);$i++)
							{
								$getpcount=$obj_rep_dash->getAllPurposeCount($Allpurpose[$i]['purpose_id'],$_REQUEST['startdate'],$_REQUEST['enddate']);
								$arpTemp = array("label"=> $Allpurpose[$i]['purpose_name'],"x"=>$Allpurpose[$i]['purpose_id'] ,"y"=> $getpcount[0]['ptotal']);
								//$arpTemp = array( "y"=> $getpcount[0]['ptotal'],"label"=> $Allpurpose[$i]['purpose_name']);
								array_push($dataPointpurpose, $arpTemp);
								
							}
							//print_r($dataPointpurpose);
							?>
  <div id="chartContainerbroad" style="height: 250px; width: 100%;margin-left:10%"></div>
  </td></tr>
<tr><td><br/></td></tr>
 <?php //if($_REQUEST['catval']<>''){?>

<tr><td>

<?php /*
							
							$dataPointstaff  = array();
							for($i=0;$i< sizeof($array);$i++)
							{
								$getstaffcount=$obj_rep_dash->StaffCountdetails($array[$i]['store'],$_REQUEST['catval'],$staffID);
								$arTemp = array("label"=> $array[$i]['viewdt'], "y"=> $getstaffcount[0]['allstaff']);
								array_push($dataPointstaff, $arTemp);
							}
							//echo $dataPoint[0]["y"];*/
							?>
  <!--<div id="chartContainerstaffall" style="height: 250px; width: 100%;margin-left:10%"></div>-->
  </td></tr>
<tr><td><br/></td></tr>

 
<tr><td>
<?php /*
							
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
								
							*/
							//echo $dataPoint[0]["y"];
							//print_r($dataPointstaffatt);
							//print_r($dataPointstaffabs);
							?>
      <?php /*if(sizeof($dataPointstaffatt)<>0){?>
  <div id="chartContainerstaffname" style="height: 300px; width: 100%;margin-left:10%"></div>
  <?php }*/?>
  </td></tr>
  <?php //}?>
<tr><td><br/></td></tr>
</table>
<!--- ------------------------   Chart Popup ------------------------ -->

<!--    ---------------First Popup  --------------- -->
<div id="myModal" class="modal">

  <div class="modal-content">
    <span class="close">&times;</span>
    <p style="font-size: 17px;text-align: center;font-weight: bold;color: blue;">Visitor Stats</p>
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

			var chart2 = new CanvasJS.Chart("chartContainer2", {
				theme: "theme2",
				animationEnabled: true,
				title: {
							//text: "Staff per category"
						 },
						legend: {
			maxWidth: 350,
			itemWidth: 220
		},
				data: [{
						type: "pie",
						indexLabel: "{label} ({y})",
						
						//indexLabelPlacement: "inside",
						
						showInLegend: true,
						//legendText: "{label}",
						legendText: "{indexlabel}",
						dataPoints: <?php echo json_encode($dataPoint_pieStaff , JSON_NUMERIC_CHECK); ?>
						}]
				});
						chart2.render();




			var chart1 = new CanvasJS.Chart("chartContainer1", {
				theme: "theme2",
				animationEnabled: true,
				title: {
							//text: "Visitors per category"
						 },
						legend: {
			maxWidth: 350,
			itemWidth: 220
		},
				data: [{
						type: "pie",
						indexLabel: "{label} ({y})",
						
						//indexLabelPlacement: "inside",
						
						showInLegend: true,
						//legendText: "{label}",
						legendText: "{indexlabel}",
						dataPoints: <?php echo json_encode($dataPoint_pie , JSON_NUMERIC_CHECK); ?>
						}]
				});
						chart1.render();

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
						dataPoints: <?php echo json_encode($dataPoint_pie , JSON_NUMERIC_CHECK); ?>
						}]
				});
			chart3.render();	
 

var chart = new CanvasJS.Chart("chartContainerline", {
	animationEnabled: true,
	theme: "light2",
	title: {
		text: "Visitor's Report"
	},
	axisX:{
		title: "Visit's",
		titleFontColor: "rgba(38, 146, 176, 0.8)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 13,
	},
	axisY: {
		title: "Number of Visitor's Per day",
		titleFontColor: "rgba(38, 146, 176, 0.8)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		labelFontSize: 13,
	},
	data: [{
		type: "line",
		indexLabel: "{y}",
		dataPoints: <?php echo json_encode($dataPoint, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
 
var chart1 = new CanvasJS.Chart("chartContainerbroad", {
	theme: "light2",
	animationEnabled: true,
	title:{
		text: "Visitor's Purpose Report ",
		
	},
	axisX:{
		title: "Purpose",
		titleFontColor: "rgba(206, 146, 176, 1.0)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		gridThickness: 1,
        tickLength: 10,
		labelFontSize: 13,

		},
	axisY: {
		title: "Number of Visitor's",
		titleFontColor: "rgba(206, 146, 176, 1.0)",
		titleFontSize: 15,
		titleFontWeight: "bold",
		gridThickness: 1,
        tickLength: 10,
		labelFontSize: 13,

	},
	
	data: [{
		//type: "bar",
		indexLabel: "{y}",
		type: "column",
		indexLabelPlacement: "inside",
		indexLabelFontWeight: "bolder",
		indexLabelFontColor: "white",
		click: onClick,
		dataPoints: <?php echo json_encode($dataPointpurpose, JSON_NUMERIC_CHECK); ?>
		
	}],
	/*options:{
    	onClick: graphClickEvent,
	}*/
	/*function graphClickEvent(e){
		alert(  e.dataSeries.type+ ", dataPoint { x:" + e.dataPoint.x + ", y: "+ e.dataPoint.y + " }" );   
	} */

	
});
chart1.render();
function onClick(e) {
		//alert(  e.dataSeries.type + ", dataPoint { x:" + e.dataPoint.label + ", y: "+ e.dataPoint.y +" }");
		var sdate=document.getElementById("strtdate").value;
		var edate=document.getElementById("enddate").value;
		var x=e.dataPoint.x;
		//var m=x+1;
		//alert(start_date);
		window.location.href = "Sm_Report.php?Visitor&rq&from_date="+sdate+"&to_date="+edate+"&purpose_id="+x;
		}


<?php /*if($_REQUEST['catval']<>''){?>
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
<?php }*/ ?>
 
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