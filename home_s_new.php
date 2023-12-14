 <?php include_once "ses_set_s.php"; 
?>
<?php include_once("includes/head_s.php");
include_once("classes/home_s_new.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
//include_once("classes/include/dbop.class.php");
$m_dbConnRoot =new dbop(true);
$m_dbConn = new dbop();
$obj_AdminPanel = new CAdminPanel($m_dbConn,$m_dbConnRoot);
$obj_utility = new utility($m_dbConn);

//$resultDuesAmount =$obj_AdminPanel->MemberDues();


?>
<html>
<head>  

<meta name="viewport" content="width=device-width, initial-scale=1">
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
#mySidenav a {
    position: fixed;
    right: -145px;
    transition: 0.3s;
    padding: 11px;
    width: 200px;
    text-decoration: none;
    font-size: 20px;
    color: white;
    border-radius: 10px 0 0 10px;
	margin-top: 145px;
	
}

#mySidenav a:hover {
    right: 0;
}

#about {
    top: 20px;
	text-align:right;
    background-color: #4CAF50;
	vertical-align: middle;
}

#blog {
    top: 80px;
	text-align:right;
    background-color: #2196F3;
}

#projects {
    top: 140px;
	text-align:right;
    background-color: #f44336;
}

#contact {
    top: 200px;
	text-align:right;
    background-color: #555
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
    



</head>
<body>


<br><br>
<div style="width:100%;" >
<center>
<table style="width:85%; width:75vw;background-color: rgb(242, 251, 252);border-radius: 10px; border: 1px solid #bce8f1;">
<tr>
<td><br></td>
</tr>
<tr>
<td style="width:25%;">
<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary" style="border-color: #ccc;">
                        <div class="panel-heading" style="background-color: #f8f8f8;border-color: #ccc;">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>
                                 <i class="fa fa-plus-square" style="font-size:10px;font-size:2.75vw;    margin-left: 10px;color: black;"></i>                                </td>
                                <td style="text-align:left;font-size:150%;vertical-align:middle;font-size:1.20vw; color:black">SMS Counter&nbsp;&nbsp;&nbsp;
                                </td>
                               </tr>
                                <tr>
                                	<td colspan="3">
                                	<br>
                                	</td>
                                </tr>
                                <?php $smsTotal = $obj_AdminPanel->SMSCounter(); ?>
                                <tr>
                                <td colspan="3" align="center">
                                	<span style="font-size: 1.0vw;margin-top: -5px;float: left;margin-left: 18%;    margin-bottom: 8px;color:black;"><a href="SMSCounterHistory.php" style="color: black;text-decoration: none;">Total Send SMS : <?php echo $smsTotal; ?></a></span></td>
                            		
                                </tr>
                                </table>
                                
                            
					</div>
                   </div>
				</div>     
</td>

<td style="width:25%;">
<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary" style="border-color: #ccc;">
                        <div class="panel-heading" style="background-color: #f8f8f8;border-color: #ccc;">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>
                                 <i class="fa fa-paper-plane" style="font-size:10px;font-size:2.75vw;    margin-left: 10px;color: black;"></i>                                </td>
                                <td style="text-align:left;font-size:150%;vertical-align:middle;font-size:1.20vw; color:black">Send Notification <br><span style="font-size: .8vw;">(SMS/Mobile Notification)</span>
                                </td>
                               </tr>
                                <tr>
                                	<td colspan="3">
                                	<br>
                                	</td>
                                </tr>
                                <tr>
                                <td colspan="3" align="center">
                                	<span style="font-size: 1.0vw;margin-top: -5px;float: left;margin-left: 18%;    margin-bottom: 8px;color:black;"><a href="sendGeneralMsgs.php" style="color: black;text-decoration: none;">Send SMS to Members </a></span></td>
                            		
                                </tr>
                                </table>
                                
                            
					</div>
                   </div>
				</div>     
</td>

<td style="width:25%;">
<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary" style="border-color: #ccc;">
                        <div class="panel-heading" style="background-color: #f8f8f8;border-color: #ccc;">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>
                                 <i class="fa  fa-file-text-o" style="font-size:10px;font-size:2.75vw;    margin-left: 10px;color: black;"></i>                                </td>
                                <td style="text-align:left;font-size:150%;vertical-align:middle;font-size:1.20vw; color:black">Send Notice <br><span style="font-size: .8vw;">(Prepare NOC / Notice)</span>
                                </td>
                               </tr>
                                <tr>
                                	<td colspan="3">
                                	<br>
                                	</td>
                                </tr>
                                <tr>
                                <td colspan="3" align="center">
                                	<span style="font-size: 1.0vw;margin-top: -5px;float: left;margin-left: 18%;    margin-bottom: 8px;color:black;"><a href="document_maker.php?View=ADMIN" style="color: black;text-decoration: none;">Send Notice to Members </a></span></td>
                            		
                                </tr>
                                </table>
                                
                            
					</div>
                   </div>
				</div>     
</td>


<td style="width:25%;">
<?php 
$AllActiveUsers =$obj_AdminPanel->getActiveUsers();
$totalUsers =sizeof($AllActiveUsers);
$activeUsers = 0;
$inactiveUsers = 0;

for($i=0;$i < sizeof($AllActiveUsers) ; $i++)
{
	if($AllActiveUsers[$i]['status'] == 2)
	{
		$activeUsers++;
	}
	else if($AllActiveUsers[$i]['status'] == 1 || $AllActiveUsers[$i]['status'] == 3)
	{
		$inactiveUsers++;
	}
}
//echo $activeUsers;
//echo $inactiveUsers;



?>
<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary" style="border-color: #ccc;">
                        <div class="panel-heading" style="background-color: #f8f8f8;border-color: #ccc;">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>
                                <!-- <span class="glyphicon glyphicon-comment"></span>-->
                                 <i class="fa fa-users" style="font-size:10px;font-size:2.75vw;margin-left: 10px;color: black;"></i>                                </td>
                                <td style="text-align:left;font-size:150%;vertical-align:middle;font-size:1.20vw; color:black">USER LIST&nbsp;&nbsp;&nbsp;
                                </td>
                               </tr>
                                <tr>
                                	<td colspan="3">
                                	<div style="width: 55%;float: left;font-size: 1vw; margin-left:5px;color:black">Active Users</div>
                                	<div style="width: 10%;float: left;text-align: center;font-size: 1vw;color:black">:</div>
                                	<div style="width: 30%;float: left;text-align: right;padding-right: 10px;font-size: 1vw;color:black"><?php echo $activeUsers ;?></div>
                                	</td>
                                </tr>
                                <tr>
                                <td colspan="3">
                                	<div style="width: 55%;float: left;font-size: 1vw; margin-left:5px; color:black;">Inactive Users</div>
                                	<div style="width: 10%;float: left;text-align: center;font-size: 1vw; color:black;">:</div>
                                	<div style="width: 30%;float: left;text-align: right;padding-right: 10px;font-size: 1vw; color:black;"><?php echo $inactiveUsers ;?></div>
                               	</td>
                            		
                                </tr>
                                </table>
                                
                            
					</div>
                   </div>
					<?php  
					 if($_SESSION['role'] == ROLE_SUPER_ADMIN)
					 {
						$urls='add_member_id.php?as'; 
					 }
					 else
					 {
						$urls='#'; 
					 }
					?>
                    <!--<a href="<?php //echo $urls ?>"><div class="panel-footer">
                   		<span class="pull-left" style="font-size:15x;font-size:1.00vw;font-weight: bold;"">Total Users : </span>
                        <span class="pull-right" style="font-size: 15px;font-weight: bold;"><?php //echo $totalUsers ;?></span>
                        <div class="clearfix"></div></div>
              </div>-->
     </div>     
</td>

</tr>
</table>
</center>

</div>


<center>
<div style="width:100%">
</div>
<br>
<br>

<table style="width:100%;display:none;width:75vw" id="table1">
	<tr>
    	<td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>                                                                    
                                <i class="fa fa-exclamation-circle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                                </td>
                                <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                               WAITING FOR APPROVAL  &nbsp;&nbsp;&nbsp;
                                </td>
                                </tr>
                                </table>
                            
                         	<div class="col-xs-9 text-right" style="width:100%">                                    
                            
							<?php
							//$TotalPending = 0;
							$PendindProvider =$obj_AdminPanel->getCountPendingPovider();
							$PendingTenant = $obj_AdminPanel->getCountPendingTenant();
							$PendingClassified = $obj_AdminPanel->getCountPendingTClassified();
							$TotalPending =$PendindProvider[0]['PendingProviders'] + $PendingTenant[0]['TenantCount'] + $PendingClassified[0]['ClassifiedCount'] + 0;
			    
									 ?>
                                   <table style="width:100%;">
                                   
                                       <div  class="huge" style="font-size:30px">
                                       <tr><td style="width:60%;text-align:left;font-size:1.00vw;"><a href="service_prd_reg_view.php?srm&View=MEMBER" style="color: white;text-decoration: none;">Service Provider</a></td><td style="width:5%;">:</td>
    <td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw"><a href="service_prd_reg_view.php?srm&View=MEMBER" style="color: white;text-decoration: none;"><?php echo $PendindProvider[0]['PendingProviders'];?></a></td></tr>
                                       
                                       <tr><td style="width:60%;text-align:left;font-size:1.00vw;"><a href="show_tenant.php?TenantList=4" style="color: white;text-decoration: none;">Lease</a></td><td style="width:5%;">:</td>
                                       <td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw"><a href="show_tenant.php?TenantList=4" style="color: white;text-decoration: none;"><?php echo $PendingTenant[0]['TenantCount'];?></a></td></tr>
                                       
                                       
                                       <tr><td style="width:60%;text-align:left;font-size:1.00vw;"><a href="classified.php?View=MEMBER" style="color: white;text-decoration: none;">Classified</a></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw"><a href="classified.php?View=MEMBER" style="color: white;text-decoration: none;"><?php echo $PendingClassified[0]['ClassifiedCount'];?></a></td></tr>
                                       
                                       <tr><td colspan="3"><br></td></tr>
                                       <!--<tr><td style="width:60%;text-align:left;font-size:1.00vw;">Album</td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw">0</td></tr>-->
                                       </div>
                                      
									</table>
								
                                 
                            </div>
                           </div>
                		</div>
                    	
                        
                    
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:15x;font-size:1.00vw;font-weight: bold;"">Total Pending List : </span>
                                <span class="pull-right" style="font-size: 15px;font-weight: bold;"><?php echo $TotalPending;?></span>
                                <div class="clearfix"></div>
                            </div>
                      
					</div>
				</div>
			</div>
        </td>
        <td style="width:33%">
        <div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-green" style="border-color:#5CB85C">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa  fa-edit fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    &nbsp;&nbsp;&nbsp;SERVICE REQUEST &nbsp;&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                     <div class="col-xs-9 text-right" style="width:100%">
                     
                    <table style="width:100%;height: 85px;">
                    <tr><td height="" style="text-align:left;font-size:1.00vw">
                    <marquee HEIGHT=82px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>
                     <?php
                   $ServiceRequestList =$obj_AdminPanel->getServiceRequestList();
				
					for($i=0 ; $i < sizeof($ServiceRequestList);$i++ )
					{ 
						 echo '[' .$ServiceRequestList[$i]['unit_no'].' ] - '.substr($ServiceRequestList[$i]['summery'], 0,30) . '<br/>';
                       
				    }?>
                    </marquee></td></tr></table>
				</div>
              
                
                 </div>
              </div>
                <a href="servicerequest.php?type=open&View=MEMBER">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        <td style="width:33%">
		<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-red" style="border-color:#D9524F">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-bell fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;;font-size:1.25vw">
                    &nbsp;&nbsp;REMINDERS/ALERT &nbsp;&nbsp;&nbsp
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">                   	 
                  
                        		<table style="width:100%;">
                                
                        	<?php
								$LeaseReminder =$obj_AdminPanel->LeaseExpiryReminder();
								$TotalLease = count($LeaseReminder);
								
								$FDReminder =$obj_AdminPanel->FDmaturityReminder();
								$TotalFD = count($FDReminder);
								
								$NoticeReminder =$obj_AdminPanel->UpcomingNoticesReminder();
								$TotalNotice = count($NoticeReminder);
								$TotalCount = $TotalLease + $TotalNotice + $TotalFD + 0;
								?>
                                <!--<tr><td style="width:60%;text-align:left;font-size:1.00vw">Upcoming Payment </td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw">0</td></tr>-->
                                
                                        <tr><td style="width:60%;text-align:left;font-size:1.00vw"><a href="show_tenant.php?TenantList=3" style="color: white;text-decoration: none;">Lease Expiry</a> </td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><a href="show_tenant.php?TenantList=3" style="color: white;text-decoration: none;"><?php  echo $TotalLease;?></a></td></tr>
								
								
								
								
								
                                 <tr><td style="width:60%;text-align:left;font-size:1.00vw"><a href="notices.php?in=0&View=MEMBER" style="color: white;text-decoration: none;">Upcoming Notices</a> </td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><a href="notices.php?in=0&View=MEMBER" style="color: white;text-decoration: none;"><?php echo $TotalNotice;?></a> </td></tr>
                             
                               
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><a href="FixedDeposit.php" style="color: white;text-decoration: none;">FD maturity</a> </td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><a href="FixedDeposit.php" style="color: white;text-decoration: none;"><?php echo $TotalFD;?></a> </td></tr>
                               
                               <tr><td colspan="3"><br></td></tr>
                            </table>
							
                    </div>
                 </div>
              </div>
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:15x;font-size:1.00vw;font-weight: bold;">Total Reminders</span>
                        <span class="pull-right" style="font-size: 15px;font-weight: bold;"><?php echo $TotalCount ;?></span>
                        <div class="clearfix"></div>
                    </div>
               
              </div>
        </div>
    </tr>
    <tr></tr>
</table>

<table style="width:100%;display:none;width:75vw" id="table2">
	<tr>
        <td style="width:33%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <?php 
					$arBankDetails = $obj_AdminPanel->GetExpenseSummaryDetailedDashboard();
					//print_r($arBankDetails);
					$dataPoint  = array();
					for($i=0;$i< sizeof($arBankDetails);$i++)
					{
						
							$arTemp = array("label"=> $arBankDetails[$i]['LedgerName'], "y"=> $arBankDetails[$i]['DebitAmount']);
							array_push($dataPoint, $arTemp);
							
						
					}
					
					?>
					
                    <a href="ExpenseDetails.php" style="color: white;text-decoration: none;"><table style="width:100%">
                    <tr>
                    <td>                                                                    
                  <i class="fa fa fa-inr fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                     EXPENDITURE&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    </a>
                    <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;">
                    <tr><td style="width:60%;text-align:left;font-size:1.00vw"><div class="panel panel-default"> 
                            
                            <div id="chartContainer1" style="height: 105px; width: 100%;"></div>
                               
                            
                             </div></td></tr>
                           
                           
                           
                            </table>
                      
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
					$PaymentDetails =$obj_AdminPanel->getRecieptReportCurrentPeriod();
					//print_r($PaymentDetails);
					$dataPoints = array( 
						array("y" => $PaymentDetails[0]['TotalBillAmount'] , "label" => "Total" ),
						array("y" => $PaymentDetails[0]['TotalRecievedAmount'] , "label" => "Received" ),
						array("y" => $PaymentDetails[0]['TotalRejectedAmount'], "label" => "Rejected" )
						
						);
					
					?>
                
                      <!--<a href="bill_receipt_report.php?wing_id=0&year_id=<?php //echo $_SESSION['default_year'];?>&period_id=<?php //echo $_SESSION['default_period'];?>&bill_method=<?php //echo BILL_TYPE_REGULAR; ?>"-->
                     <a href="bill_receipt_report.php?Dashboard=1" style="color: white;text-decoration: none;"><table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-bank fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    PAYMENT&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table></a>
                    
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
    <td> 
    </td>   
   <!--<td style="width:33%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-arrow-up fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    MEMBER'S&nbsp;DUES&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;"> 
                   <tr><td style="width:60%;text-align:left;font-size:1.00vw">
                   <marquee HEIGHT=92px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>
                   
                	<?php
  						//$resultDuesAmount =$obj_AdminPanel->MemberDues();
						//$GetUnitArray =$obj_AdminPanel->getAllUnits();
							//$EncodeUnitArray;
							//$EncodeUrl;
							//if(sizeof(GetUnitArray) > 0)
							//{
								//$EncodeUnitArray = json_encode($GetUnitArray);
								//$EncodeUrl = urlencode($EncodeUnitArray);
							//}
						
						//for($i=0; $i < sizeof($resultDuesAmount) ; $i++)
						//{
							
							
							//$Url = $Url = "member_ledger_report.php?&uid=".$resultDuesAmount[$i]['unit_id']."&Cluster=".$EncodeUrl;
							
							?>
                        	<a href="#" onClick="window.open('<?php //echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color: white;text-decoration: none;"><div style="width:63%; float:left;"><?php //echo substr($resultDuesAmount[$i]['MemberName'],0,15); ?>...</div>
                            <div style="width:5%; float:left;"> : </div>
                           	<div style="width:32%; float:right;text-align: right;"><?php //echo $resultDuesAmount[$i]['Amount']; ?></div></a>
                           <br>
                              
                   <?php //} 
				   ?>  
                     </marquee></td></tr></table>
                          
                      
				      </div>
                 </div>
              </div>
                <a href="dues_advance_frm_member_report.php?&sid=<?php //echo $_SESSION['society_id']; ?>">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>    
    </td>-->
    </tr>
</table>


<table style="width:100%;display:none;width:75vw" id="table3">
	<tr>
    	<td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>                                                                    
                                <i class="fa fa-inr fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                                </td>
                                <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH&nbsp;&&nbsp;BANK&nbsp;&nbsp
                                </td>
                                </tr>
                                </table>
                            
                         	<div class="col-xs-9 text-right" style="width:100%">                                    
                            
							<?php
								$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance(3);
			    	$iCounter = 0;
                                if($arBankDetails <> '')
                                {
									 ?>
                                   <table style="width:100%;">
                                   <?php
                                   foreach($arBankDetails as $arData=>$arvalue)
                                   {
                                       $len = strlen($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]));
                                       
                                       $BankName =  ($len > 15) ? (substr($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]), 0, 15) . '...') : $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
                                       
                                       $receipts =$arvalue["receipts"];
                                       $payments = $arvalue["payments"]; 
                                       $BalAmount = $receipts - $payments;
                                       ?>
                                       <div  class="huge" style="font-size:30px"><tr><td style="width:60%;text-align:left;font-size:1.00vw;"><?php  echo $BankName ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                                       </div>
                                       <?php
									   
									   $iCounter++;
							   			if($iCounter >= 3)
							   			{
								   			break;
							   			}
										
                                    }
									  echo "</table>";
								}
									$ReqRows =  3 - $iCounter;
                                    ?>
									<div style="color:#337BB7">
								   <table style="width:100%;">
								   <tr>
								   <?php 
								   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
								   {
									?>
								   <td style="width:60%;text-align:left;color:#337BB7;font-size:1.00vw;">No Data
								   </td>
								   </tr>
								   <?php 
								   }
								   ?>
                                   </table></div>
                                    <?php
                              // }
							?>
                             </div>
                            </div>
                			</div>
                    	
                        
                        <a href="BankAccountDetails.php">
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
					</div>
				</div>
			</div>
        </td>
         <td style="width:33%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-arrow-up fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    MEMBER'S&nbsp;DUES&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%"> 
                   <table style="width:100%;"> 
                   <tr><td style="width:60%;text-align:left;font-size:1.00vw">
                   <marquee HEIGHT=65px  behavior=scroll  direction=up scrollamount=1 scrolldelay=60 onmouseover='this.stop()' onmouseout='this.start()'>
                   
                	<?php
  						$resultDuesAmount =$obj_AdminPanel->MemberDues();
						/*echo "<pre>";
						print_r($resultDuesAmount);
						echo "</pre>";*/
						
						$GetUnitArray =$obj_AdminPanel->getAllUnits();
							$EncodeUnitArray;
							$EncodeUrl;
							if(sizeof(GetUnitArray) > 0)
							{
								$EncodeUnitArray = json_encode($GetUnitArray);
								$EncodeUrl = urlencode($EncodeUnitArray);
							}
						
						//for($i=0; $i < sizeof($resultDuesAmount) ; $i++)
						foreach($resultDuesAmount as $value )
						{
							/*echo "<pre>";
							print_r($value);
							echo "</pre>";*/
							
							$Url = $Url = "member_ledger_report.php?&uid=".$value['unit_id']."&Cluster=".$EncodeUrl;
							
							?>
                        	<a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color: white;text-decoration: none;"><div style="width:63%; float:left;"><?php echo substr($value['MemberName'],0,15); ?>...</div>
                            <div style="width:5%; float:left;"> : </div>
                           	<div style="width:32%; float:right;text-align: right;"><?php echo $value['Amount']; ?></div></a>
                           <br>
                              
                   <?php } 
				   ?>  
                     </marquee></td></tr></table>
                          
                      
				      </div>
                 </div>
              </div>
                <a href="dues_advance_frm_member_report.php?&sid=<?php echo $_SESSION['society_id']; ?>">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>    
    
        <td>
        </td>
        
        <td class="main_block">
        	<div class="main_div" style="display:none;">
            	<div class="main_head">
                </div>
                <div class="main_data">
                </div>
         	</div>
        </td>
    </tr>
</table>
</center>

</table>
 <div id="mySidenav" class="sidenav">
  <a href="IncomeDetails.php" id="about"><span class="fa fa-plus-square fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;INCOME</span></a>
  <a href="AssetSummary.php" id="blog"><span class="fa fa-cubes fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;ASSETS</span></a>
  <a href="LiabilitySummary.php" id="projects"><span class="fa fa-exclamation-triangle fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;LIABILITIES</span></a>
</a>
  <!--<a href="#" id="contact">Contact</a>-->
</div>
<script>
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
				dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
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
						dataPoints: <?php echo json_encode($dataPoint , JSON_NUMERIC_CHECK); ?>
						}]
				});
						chart1.render();			
			
 	}
</script>
<script type="text/javascript" src="js/canvasjs.min.js"></script>


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

