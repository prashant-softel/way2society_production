<?php 
	include_once("classes/notice.class.php");
	include_once("classes/servicerequest.class.php");
	include_once("classes/events_self.class.php");
	include_once("classes/addclassified.class.php");
	include_once("classes/create_poll.class.php");
	include_once("classes/utility.class.php");
	$obj_utility=new utility($m_dbConn);
	
	$obj_notice = new notice($m_dbConn);
	$count=$obj_notice->getcount();
	$prevID = "";
	
	$obj_events_self = new events_self($m_dbConn,$m_dbConnRoot);
	$eventscount=$obj_events_self->getcount();
	include_once("classes/servicerequest.class.php");
	$obj_servicerequest = new servicerequest($m_dbConn);
	$requestcount=$obj_servicerequest->getRecordsRight();
    
	$obj_classified = new classified($m_dbConnRoot,$m_dbConn);
	$classifiedcount=$obj_classified->getClassified();
	
	$obj_create_poll = new create_poll($m_dbConnRoot,$m_dbConn);
	$VoteService=$obj_create_poll->getPollList();
	
	//print_r($VoteService);
	?>
	
<link href="dist/css/sb-admin-2.css" rel="stylesheet">
<div style="float:right;width:20%;" >
<div class="panel-head" style=" vertical-align:middle;background-color:#F7F7F7;margin-right:2%;margin-top:4%; height:20%;width:15vw" align="center">
					
                         <?php  
						 if($_SESSION['apply_paytm'] == 1)
						{
							$NEFTURL = "neft2.php?SID=".base64_encode($_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); 
						}
						else
						{
						 $NEFTURL = "neft.php?SID=".base64_encode( $_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); 
						}
						 ?>
                        
						 <div id="mySidenav1" class="sidenav1">
                        	 <a href="<?php echo $NEFTURL ?>" id= "neftSide" style="text-decoration:none;"><span class="fa fa-rupee fa-5x" style="font-size:10px;font-size:2.5vw;float:left;margin-left: 10px;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Record NEFT/IMPS </span>
                             
                              <a href="servicerequest.php" id="request"><span class="fa fa-reply fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Register a Complaint</span></a> 
                              
                          <a href="Gallery.php" id="gallery"><span class="fa fa-camera-retro fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;">&nbsp;&nbsp;&nbsp;Photo Gallery</span></a>
                          
                          <a href="GDrive_view.php?Mode=1"  id="documentside"><span class="fa fa-book fa-5x" style="font-size:10px;font-size:2.5vw;float:left;"></span><span style="float: left;"> &nbsp;&nbsp;&nbsp;Documents</span></a>
                         </div>
                    </div>                    
 <div class="panel panel-info" id="panel_widget" style="margin-top:6%;margin-right:2%;display:none;font-size:1.00vw;">
 						
                       <!-- <div class="panel-heading">
                         Quick Links
                        </div>
                        <div class="panel-body">
                            <p>
                            <a href="neft.php">Make Payment</a><br>
                            <a href="Complaints.php">Register a Complaint</a><br>
                            <a href="Gallery.php">Upload Pictures</a><br>
                            <a href="Ads.php">Post a Advertisement</a><br></p>
                        </div>-->
                      	
						<?php if($_SESSION['module']['service_request'] == "1")
						{
							?>
							<div class="panel-heading">
								<A href="servicerequest.php?type=open"><b>Service Requests</b></A>
							</div>
							<div class="panel-body">
								<!--<p>-->
									<?php 
									//print_r($count);
									$prevRequestNo = "";
									if($requestcount <> "")
									{
										//print_r($requestcount);
										for($i = 0; $i < sizeof($requestcount); $i++)
										{	
											if($prevRequestNo != $requestcount[$i]['request_no'])
											{
												$prevRequestNo = $requestcount[$i]['request_no'];
												$prevRequestNo = $requestcount[$i]['status'];
										?> <!--<span style="float: left;  width: 130px;">
												<a href="viewrequest.php?rq=<?php echo $requestcount[$i]['request_no'];?>"><?php echo substr($requestcount[$i]["summery"],0,15);?></a>
                                                </span>-->
												
												<?php /*?><?php if($requestcount[$i]['status']=="Assigned"){
													?>
                                                    <span style="color:#FFF033;"><?php echo $requestcount[$i]['status'];?></span><br>

												   <?php }
                                                   else if( $requestcount[$i]['status']=="In process")
												   {?>
													   <span style="color:#D1FF33"><?php echo $requestcount[$i]['status'];?></span><br>
													 <?php }
													 else if($requestcount[$i]['status']=="Waiting for details")
													 {?>
                                                     <span style="color:#FF339F"><?php echo $requestcount[$i]['status'];?></span><br>
                                                    <?php  }	
													 else if($requestcount[$i]['status']=="Resolved")
													{?>						
													  <span style="color:#33FF6E"><?php echo $requestcount[$i]['status'];?></span><br>
                                                      <?php }
													  else if($requestcount[$i]['status']=="Reopen")
													{  ?>
                                                     <span style="color:#5533FF"><?php echo $requestcount[$i]['status'];?></span><br>
                                                    <?php }<?php */?>
													<?php   if($requestcount[$i]['status']!="Closed")
													{?>
                                                    <table>
                                                    <tr>
                                                    <td><span style="float: left;  width: 130px;">
                                                    <!--<span style="color:black;float:right;margin-left: -4px; width:100px;">-->
                                                    <a href="viewrequest.php?rq=<?php echo $requestcount[$i]['request_no'];?>"><?php echo substr($requestcount[$i]["summery"],0,15);			                                                     ?></a></td>
                                                    <td><span style="color:black;float:right;margin-right: -4px; width:100px;">[&nbsp;<?php echo $requestcount[$i]['status'];?>&nbsp]					                                                    </span></td>
                                                    </tr>
                                                    </table>
                                                    <?php }?>
                                                    
                                                    
                                                    
                                                    
			<?php   }
										}
									
									}?>
								<!--</p>-->
							</div>
							<?php
						}
						?>
                      
                      	<?php if($_SESSION['module']['notice'] == "1")
						{
							?>
                        	<div class="panel-heading">
                         		<A href="notices.php?in=0"><b>Notices</b></A>
                        	</div>
                        	<div class="panel-body">
                            	<p>
									<?php 
									//print_r($count);
									if($count <> "")
									{
										
										foreach($count as $key=>$val)
										{
											//echo "sbfjks";
											//echo $count[$key]['id'];
										$show_notice=$obj_notice->FetchNotices($count[$key]['id']);
										
										if($prevID != $show_notice[0]['id'])
										{
											$prevID = $show_notice[0]['id'];	
									?>
										<span style="float: left;  width: 155px;">
										<a href="notices.php?in=<?php echo $count[$key]['id'];?>"><?php echo substr($show_notice[0]['subject'],0,22);?></a></span>
                                        <span style=" float:right;margin-right: -10px; width:80px;"><?php echo  getDisplayFormatDate( $show_notice[0]['exp_date'])?></span><br>
									<?php }
									}
									}?>
								</p>
							</div>
							<?php
						}
						?>
                
                    	<?php if($_SESSION['module']['event'] == "1")
						{
							?>
							<div class="panel-heading">
								<a href="events_view.php"><b>Events</b></a>
							</div>
							<div class="panel-body">
								<p>
								   <?php 
									//print_r($count);
									if($eventscount <> "")
									{	//print_r ($eventscount);	
										$count=0;					
										foreach($eventscount as $key=>$val)
										{
											
											$events = $obj_events->RightPanel($eventscount[$key]['events_id']);
										//print_r($events);
											//echo $events[$key]['end_date'];								
											$startDate = date('Y-m-d');
										//}//echo $startDate;
											if($events <> "")
											{  
												if( $events[0]['end_date'] >=$startDate )
												{ 
												?>  
                                                <span style="float: left;  width: 155px;">                              	
													<a href="events_view_details.php?id=<?php echo $events[0]['events_id'];?>"><?php echo substr($events[0]['events_title'],0,22);?></a></span>
                                                    <span style=" float:right;margin-right: -10px; width:80px;"><?php echo getDisplayFormatDate($events[0]['events_date'])?></span><br>									                                         
									<?php		}
											}
											} 
											//$count++;
										}
										
									//}
									?>    
								</p>                                                  
							</div>
                            <?php
						}
						?>
                            
                    	<?php //if($_SESSION['module']['classified'] == "1")
						{
							//echo "hello";
							?>
							<div class="panel-heading">
								<a href="classified.php"><b>Classified</b></a>
							</div>
							<div class="panel-body">
								<p>
								   <?php 
									//print_r($count);
									if(sizeof($classifiedcount) > 0)
									{							
										foreach($classifiedcount as $key=>$val)
										{								
											//$show_events=$obj_events_self->FetchEvents($eventscount[$key]['events_id']);
											//$classified = $obj_classified->view_classified($classifiedcount[$key]['id']);								
											//$startDate = date('Y-m-d');
											//if($classified <> "")
											{
												$days = (strtotime($val['exp_date']) - strtotime($startDate)) / (60 * 60 * 24);															
												if($days>=0)
												{  ?>                                	
													<a href="show_classified.php?id=<?php echo $val['id'];?>"><?php echo $val['ad_title'];?></a><br>									                                         
									<?php		}
											} 
										}
									}
									?>    
								</p>                                                  
							</div>
							<?php
						}
						?>
                        <?php //if($_SESSION['module']['classified'] == "1")
						{
							//echo "hello";
							?>
							<div class="panel-heading">
								<!--<span><a href="">Poll</a></span>-->
                                <?php 
                                		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN ))
												{ 	?>
                                                <span><a href="poll.php"><b>Poll</b></a></span>
                                <!--<span style="float:right"><a href="poll.php"><b>View All ...</b></a></span>-->
                                <?php }
								else{?>
                                 			<span><a href="polls.php"><b>Poll</b></a></span>
                                		<!-- <span style="float:right"><a href="polls.php"><b>View All ...</b></a></span>-->
										<?php }?>
							</div>
							<div class="panel-body">
								<p>
								   <?php 
									//print_r($count);
									if(sizeof($VoteService) > 0)
									{	
									//print_r($VoteService);
										$totalVote=0;					
										for($i=0;$i<sizeof($VoteService);$i++)
										{ 
										$Vote=$VoteService[$i]['counter'];
										$totalVote=$totalVote+$Vote;
										//echo $totalVote;
										
                                        
										if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN))
												{ 	?>					
											                         	
													<a href="view_polldetails.php?rq=<?php echo $obj_utility->encryptData($VoteService[$i]['poll_id']);?>"><?php echo substr($VoteService[$i]["question"],0,20);?></a><!--<span style="float:right; color:#03F; font-weight:bold">[<?php echo $VoteService[$i]['counter'];?>]&nbsp;Votes</span>--><br>									                                         
									
								<?php	}
                                else
								{?>
								<a href="poll_preview.php?rq=<?php echo $obj_utility->encryptData($VoteService[$i]['poll_id']);?>"><?php echo substr($VoteService[$i]["question"],0,20);?></a><span style="float:right; color:#03F; font-weight:bold">[<?php echo $VoteService[$i]['counter'];?>]&nbsp;Votes</span><br>	
									<?php
								}}?>    
								</p>   
                                <?php }?>	                                               
							</div>
							<?php
						}
						?>
                    </div>                        
                    
</div>