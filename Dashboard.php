<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Dashboard</title>
</head>



<?php 
$IsthisMemberDashBoard = true; // just check this page is dashboard 
include_once("includes/head_s.php");
// include_once("RightPanel.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/upload_index.class.php");
include_once("classes/servicerequest.class.php");

if($_SESSION['role'] == ROLE_MEMBER && $_SESSION['View'] == 'ADMIN')
{
	//header("refresh:0;url=initialize.php?imp" );
	header('Location: initialize.php');
}

$obj_AdminPanel = new CAdminPanel($m_dbConn);
$objServiceRequest = new servicerequest($m_dbConn);
$objServiceRequest->getRenovationId();					 
	$sql_query = "Select billreg.DueDate as DueDate, period.Type as Month, yr.YearDescription as Year,bill.UnitID as BillUnitID,bill.PeriodID as BillPeriodID,bill.CurrentBillAmount,bill.TotalBillPayable from billdetails as bill JOIN period as period ON bill.PeriodID = period.id JOIN year as yr ON yr.YearID=period.YearID JOIN billregister as billreg ON bill.PeriodID = billreg.PeriodID where bill.UnitID='" . $_SESSION["unit_id"] .  "' order by bill.PeriodID DESC limit 0,1"; 
	$MemberDues = $obj_AdminPanel->m_dbConn->select($sql_query);
	
	$getnotices=$obj_AdminPanel->GetNoticeDetails();
	$NoticesCounter = count($getnotices);

?>
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
			font-size:16px;
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
			font-size:12px;
			text-align:center;
			height:81px;
		}
	.main_footer{
			background:#990000;
			border-bottom-left-radius:12px;
			border-bottom-right-radius:12px;
			color:#00F;
			font-size:12px;
			font-weight:bolder;
			text-align:center;
			height:30px;
			display:table;
			width:100%;
		}
		
	.main_footer, .link{
			color:#FFF;
		}
	body {
    -webkit-text-size-adjust:auto;
}
.album_Image
{
	width:100%;
	
}
.album
{
	width: 68px;
    box-shadow: 0 0 5px #ccc;
    padding: 1px;
    margin: 5px;
    float: left;
}
 .canvasjs-chart-credit
	 {
		position: unset !important; 
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
		
		
		window.location.href = "home_s.php?View=ADMIN";
		
		//location.reload(true);
	}
	</script>
    
<table style="font-size:14px;margin-left:8%;width:60%;font-size:1.75vw;width:45vw;visibility:hidden"><tr><td>
        <marquee>
                	 
					 <?php
					$strheader = "" .$_SESSION['name'].", your Maintenance bill's next payment of Rs ".$MemberDues[0]["TotalBillPayable"]." is due on ".date("d-m-Y", strtotime($MemberDues[0]["DueDate"]));
					 
					?> 
                    </marquee>
        </td>
                    <td></td></tr></table>
                    

<center>
        

            <div class="row" style="width:70%;float:left">
            <table style="width:100%">
            <?php
			//echo $_SESSION["unit_id"]; 
			if($_SESSION["unit_id"] != "0")
			{
			?>
            <tr>
            <td width="10%">
            </td>
            <td style="width:45%">

                <div class="col-lg-3 col-md-6" style="width:100%;display:none;" id="div1">
                    <div class="panel panel-primary" onClick="window.location.href='MaintenanceBill_m.php'"  style="width:100%;cursor:pointer">
                        <div class="panel-heading" style="width:100%;">
                            <div class="row" style="width:100%;">
                                <div class="col-xs-3" style="width:100%;">
                                <table style="width:100%">
                                <tr>
                                <td>
                                                                    
                                    <i class="fa fa-file-text-o fa-5x" style="font-size:10px;font-size:2.40vw"></i>
                                    </td>
                                    <td style="font-size:16px;vertical-align:middle;font-size:1.60vw">
                                    Last&nbsp;Bill&nbsp;
                                    <hr style="background-color: #eee;height: 2px;margin-top: 5px;margin-bottom: 10px;">
                                    </td>
                                    
                                    </table>
                                </div>
                                <div class="col-xs-9 text-right" style="width:100%;font-size:1.40vw">
                                    
                                    

									<div style="font-size:15px;font-size:1.10vw"><?php echo $MemberDues[0]["Month"]. " ".$MemberDues[0]["Year"]; ?></div>
									<div style="font-size:15px;font-size:1.10vw"><?php echo  "Due&nbsp;By&nbsp;: " .date("d-m-Y", strtotime($MemberDues[0]["DueDate"])); ?></div>
                                    <div class="huge" style="font-size:30px;font-size:1.40vw">Rs. <?php echo number_format($MemberDues[0]["TotalBillPayable"],2); ?>
                                    </div> 
									
                                    <div style="font-size:15px"><a href="neft.php?SID=<?php echo base64_encode( $_SESSION["society_id"])?>&UID=<?php echo base64_encode($_SESSION['unit_id'])?>" style="color:#FFFFFF">Update Payment Details</a></div>
                                </div>
                            </div>
                        </div>
                        <a href="MaintenanceBill_m.php">
                            <div class="panel-footer" style="width:100%;">
                                <span class="pull-left" style="font-size:10px;font-size:1.00vw">View Bill Details</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                </td>
                <td style="width:45%">    
                <div class="col-lg-3 col-md-6"  style="width:100%;display:none;" id="div2">
                    <div class="panel panel-primary"  onClick="window.location.href='payments_m.php'" style="width:100%;cursor:pointer">
                        <div class="panel-heading" style="width:100%;">
                            <div class="row" style="width:100%;">
                                <div class="col-xs-3" style="width:100%;">
                                <table style="width:100%">
                                <tr>
                                <?php 
								$sql_query = "select ChequeDate,Amount,PaidBy,ChequeNumber,DepositID from chequeentrydetails where PaidBy='" . $_SESSION["unit_id"] . "' order by ChequeDate DESC limit 0,1";
								$MemberChqReceipts = $obj_AdminPanel->m_dbConn->select($sql_query);
								$DepositID = $MemberChqReceipts[0]["DepositID"];
								$TrnxDt = "";
								$TrnxType = "";
								if($DepositID == DEPOSIT_NEFT || $DepositID == DEPOSIT_ONLINE)
								{
									$TrnxDt = "Transaction Date";
									$TrnxType = "Transaction ID";
								}
								else if($DepositID == DEPOSIT_CASH)
								{
									$TrnxDt = "Cash Date";
									$TrnxType = "-";
								}
								else
								{
									$TrnxDt = "Cheque Date";
									$TrnxType = " Cheque Number";
								}
								?> 
                                <td>
                                    <i class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.40vw"></i>
                                    </td>
                                    <td style="text-align:left;font-size:150%;vertical-align:middle;font-size:1.60vw">
                                   Last&nbsp;Payment
                                     <hr style="background-color: #eee;height: 2px;margin-top: 5px;margin-bottom: 10px;">
                                    </td>
                                    </tr>
                                    </table>
                                </div>
                                <div class="col-xs-9 text-right" style="width:100%;font-size:1.75vw">
                                    <div style="font-size:15px;font-size:1.10vw"><?php echo $TrnxDt ?> : <?php echo ($MemberChqReceipts[0]["ChequeDate"] <> '') ? date("d-m-Y", strtotime($MemberChqReceipts[0]["ChequeDate"])) : ''; ?></div>
                                    <?php 
									if($DepositID != DEPOSIT_CASH)
									{
									?>	
                                    <div style="font-size:15px;font-size:1.10vw"><?php echo $TrnxType ?> : <?php echo $MemberChqReceipts[0]["ChequeNumber"]; ?></div>
                                    <?php
									}
									else
									{
									?>
                                    <div style="font-size:15px;color: #5cb85c">:</div>
                                    <?php
									}
									?>
                                    <div class="huge" style="font-size:30px;font-size:1.40vw">Rs.<?php echo number_format( $MemberChqReceipts[0]["Amount"],2); ?></div>
                                    
                                    <div style="font-size:15px;color: #5cb85c">:</div>
                                </div>
                            </div>
                        </div>
                        <a href="payments_m.php">
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:10px;font-size:1.00vw">View Payment Details</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>         
                </td>            
            </tr>
            
            <tr><td width="10%">
            </td>
            <td width="45%">
           <div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                    <?php 
					
					$getBillSummery=$obj_AdminPanel->BillSummary();
					$dataPoints1  = array();
					for($i=0;$i< sizeof($getBillSummery);$i++)
					{
						
						$arTemp = array("label" =>$getBillSummery[$i]['Month'], "y" => $getBillSummery[$i]['CurrentBillAmount']);
				 
					array_push($dataPoints1, $arTemp);
					}?>
					
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                  <i class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.40vw;margin-left: 15px;"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.60vw">
                    Bill Summary&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                    <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;">
                    <tr><td style="width:60%;text-align:left;font-size:1.00vw" ><div class="panel panel-default"> 
                            
                        <div id="chartContainer1" style="height: 105px; width: 100%;" class="zoom"></div>
                               
                            
                             </div></td></tr>
                           
                           
                           
                            </table>
                      
				      </div>
				      </div>
                 </div>
                
        </div>
        </td>
        <td style="width:45%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                    <?php 
					
					$getPaymentSummary=$obj_AdminPanel->PaymentSummary();
					$dataPoints  = array();
					for($i=0;$i< sizeof($getPaymentSummary);$i++)
					{
						
						$arTemp = array("label" =>$getPaymentSummary[$i]['ChequeDate'], "y" => $getPaymentSummary[$i]['Amount']);
				 
					array_push($dataPoints, $arTemp);
					}?>
					
                   <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-file-text-o fa-5x" style="font-size:10px;font-size:2.40vw;margin-left: 15px;"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.60vw">
                   Payment Summary&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;">
                    <tr><td style="width:60%;text-align:left;font-size:1.00vw"><div class="panel panel-default"> 
                            
                          <div id="chartContainer" style="height: 105px; width: 100%;"></div>
                               
                            
                             </div></td></tr>
                           
                           
                           
                            </table>
                      
				      </div>
                 </div>
              </div>
              </div>
        </div>
        </td>
        </tr>
        <?php
			}
			?>
            <tr>
            <td width="10%">
            </td>
            <td style="width:45%">    
                <div class="col-lg-3 col-md-6"  style="width:100%;display:none;height:100%" id="div3">
                    <div class="panel panel-yellow"  onClick="window.location.href='notices.php'"  style="cursor:pointer">
                        <div class="panel-heading">
                            <div class="row" style="width:100%">
                                <div class="col-xs-3" style="width:100%">
                                <table style="width:100%">
                                <tr>
                                <td>
                                    <i class="fa fa-bullhorn fa-5x" style="font-size:10px;font-size:2.40vw"></i>
                                    </td>
                                    <td style="text-align:left;font-size:20px;vertical-align:middle;font-size:1.60vw">
                                    Notices
                                     <hr style="background-color: #eee;height: 2px;margin-top: 5px;margin-bottom: 10px;">
                                    </td>
                                    </tr>
                                    </table>
                                </div>
                                <div class="col-xs-9 text-right" style="width:98%;margin-left: 10px;    margin-top: 8px;">
                                    <div style="font-size:150%;font-size:1.50vw; text-align:right;font-size: 1.20vw;">                                    
									<?php 
										for($i = 0; $i < sizeof($getnotices); $i++)
										{
											//if($getnotices[$i]['description'] != "Notice Uploaded")
											if($getnotices[$i]['note'] == "")
											{ 
												if(strlen($getnotices[$i]['subject']) > 15)
												{
											echo "<a href='ViewNotice.php?id=".$getnotices[$i]['id']."'  target='_blank' style='color:white;'>". substr($getnotices[$i]['subject'],0,15)."...</a> <br />";
												}
												else
												{
													echo "<a href='ViewNotice.php?id=".$getnotices[$i]['id']."'  target='_blank' style='color:white;'>".$getnotices[$i]['subject']."</a> <br />";
												}
											}
											else
											{
												if(strlen($getnotices[$i]['subject']) > 15)
												{
												echo "<a href='http://way2society.com/Notices/".$getnotices[$i]['note']. "' class='links' style='color:white;'>". substr($getnotices[$i]['subject'],0,15)."...</a> <br />";
												}
												else
												{
													echo "<a href='http://way2society.com/Notices/".$getnotices[$i]['note']. "' class='links' style='color:white;'>".$getnotices[$i]['subject']."</a> <br />";
												}
											}
										}
										if($NoticesCounter == 0)
										{
											echo "<div>No Notice To Display.</div>";
											echo "<div style='visibility: hidden;'>No Notice To Display.</div>";
											echo "<div style='visibility: hidden;'>No Notice To Display.</div>";
										}
										else if($NoticesCounter == 1)
										{
											echo "<div style='visibility: hidden;'>No Notice To Display.</div>";
											echo "<div style='visibility: hidden;'>No Notice To Display.</div>";
											}
										else if($NoticesCounter == 2)
										{
											echo "<div style='visibility: hidden;'>No Notice To Display.</div>";
											}
									?></div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <a href="notices.php?in=0<?php //echo $getnotices[0]['id'];?>">
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:10px;font-size:1.00vw">View All Notices</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>         
                </td>
            <td width="45%">
            <div class="col-lg-3 col-md-6"  style="width:100%;display:none;min-height:300%" id="div4">
                    <div class="panel panel-red"   onClick="window.location.href='Gallery.php'"   style="cursor:pointer">
                        <div class="panel-heading">
                            <div class="row"  style="width:100%">
                                <div class="col-xs-3" style="width:100%">
                                
                                    <table style="width:100%">
                                <tr>
                                <td>
                                    <i class="fa fa-camera-retro fa-5x" style="font-size:10px;font-size:2.40vw"></i>
                                    </td>
                                    <td style="text-align:left;font-size:16px;vertical-align:middle;font-size:1.60vw">
                                    Photo&nbsp;Gallery
                                    <hr style="background-color: #eee;height: 2px;margin-top: 5px;margin-bottom: 10px;">
                                    </td>
                                    </tr>
                                    </table>
                                </div>
                                <div class="col-xs-9" style="width:100%;margin-left: 38px;">
                                    
                                     <?php
									 $m_dbConn = new dbop();
									 $m_dbConnRoot = new dbop(true);
									$obj_show=new show_album($m_dbConn,$m_dbConnRoot);
									$album_id=$_GET['id'];
									$queryFolder2 = "SELECT a.`id`, a.`name`, a.`folder` FROM `album` as a JOIN `soc_group` as g ON a.group_id = g.group_id where g.society_id = '" . $_SESSION['society_id'] . "' ORDER BY id DESC LIMIT 3";
									
  									$resFolder2 = $m_dbConnRoot->select($queryFolder2);
									if(sizeof($resFolder2) > 0)
									{
										for($i=0;$i<sizeof($resFolder2);$i++)
										{										
										
											$foldername = $resFolder2[$i]['folder'];
											$query="SELECT `url`,`id` FROM `photos` WHERE `album_id`='".$resFolder2[$i]['id']."' limit 1";
											$res = $m_dbConnRoot->select($query);
										
											if(sizeof($res) > 0)
											{
												for($j=0;$j<sizeof($res);$j++)
												{
													$photo_id=$res[$j]['id'];
													$url=$res[$j]['url']; 
													?>
													<div style="width:70px; height:70px;"; class="album" >
														<center><img id="img1" class="album_Image" src="<?php echo 'uploads/' .$foldername.'/'.$url; ?>" style= "vertical-align: middle;text-align:center;border:none;height: 65px; width: auto;" /></center>
													</div>
													<?php 
												}
											}
											else
											{
												?><div style="width:65px; height:65px;"; class="album" >
													<img id="img1" class="album_Image" src="uploads/noimage.png" style= "vertical-align: middle;text-align:center;border:none;height:65px; " /></div>
												<?php
											}
										}
									}
									else
									{
										?><div style="width:28%"; class="album" >
											<img id="img1" class="album_Image" src="uploads/noimage.png" style= "vertical-align: middle;text-align:center;border:none;height:80px;" /></div>
										<?php
										
									}?>
                                    
                                </div>
                            </div>
                        </div>
                        <a href="Gallery.php">
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:10px;font-size:1.00vw">View All Galaries</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </td>
            </tr>
             </table>
			<!--<table style="width:100%">
            <tr><td width="10%">
            </td>
            <td width="45%">
           <div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <?php 
                 //  $dataPoints1 = array( 
					//			  array("y" => 3373.64, "label" => "March" ),
						//		  array("y" => 2435.94, "label" => "April" ),
							//	  array("y" => 1039.99, "label" => "May" ),
							//	  array("y" => 765.215, "label" => "June" ),
								//  array("y" => 612.453, "label" => "July" )
									//);
 							?>
					
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                  <i class="fa fa-edit fa-5x" style="font-size:10px;font-size:2.40vw;margin-left: 15px;"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.60vw">
                    Payment Summery&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                    <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;">
                    <tr><td style="width:60%;text-align:left;font-size:1.00vw" ><div class="panel panel-default"> 
                            
                        <div id="chartContainer1" style="height: 105px; width: 100%;" class="zoom"></div>
                               
                            
                             </div></td></tr>
                           
                           
                           
                            </table>
                      
				      </div>
				      </div>
                 </div>
                
        </div>
        </td>
        <td style="width:45%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <?php 
					//$dataPoints = array( 
						//		  array("y" => 3373.64, "label" => "March" ),
							//	  array("y" => 2435.94, "label" => "April" ),
								//  array("y" => 1039.99, "label" => "May" ),
								  //array("y" => 765.215, "label" => "June" ),
								  //array("y" => 612.453, "label" => "July" )
									//);
 
					
					?>
                   <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-file-text-o fa-5x" style="font-size:10px;font-size:2.40vw;margin-left: 15px;"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.60vw">
                   Bill Summery&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;">
                    <tr><td style="width:60%;text-align:left;font-size:1.00vw"><div class="panel panel-default"> 
                            
                          <div id="chartContainer" style="height: 105px; width: 100%;"></div>
                               
                            
                             </div></td></tr>
                           
                           
                           
                            </table>
                      
				      </div>
                 </div>
              </div>
              </div>
        </div>
        </td>
            </tr>
            </table>-->
            </div>
            
   
</center>

<br>
<br><br>
<br><br>
<br><br>
<br><br>
<br><br>
<br>
<script type="text/javascript" src="js/canvasjs.min.js"></script>
<script>
window.onload = function() {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	theme: "light2",
	
	data: [{
		type: "column",
		///yValueFormatString: "#,##0.## tonnes",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

 var chart1 = new CanvasJS.Chart("chartContainer1", {
	animationEnabled: true,
	theme: "light2",
	
	data: [{
		type: "column",
		//yValueFormatString: "#,##0.## tonnes",
		dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
	}]
});
chart1.render();
}
			</script>
<?php include_once "includes/foot.php"; ?>

<script>

function myFunction() 
{
	$("#div1").fadeIn(2000);
	$("#div2").fadeIn(2000);
	$("#div3").fadeIn(2000);
	$("#div4").fadeIn(2000);
}
myFunction();
</script>