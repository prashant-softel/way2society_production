<?php

include_once("adduser.class.php");
include_once("initialize.class.php");
include_once("include/dbop.class.php");
include_once("include/fetch_data.php");
include_once("utility.class.php");
include_once("voucher.class.php");
include_once("dbconst.class.php");

class CommitteeNotification{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $m_bShowTrace;
	public $obj_addduser;
	public $obj_initialize;
	public $obj_voucher;
	public $obj_Utility;
	public $objFetchData;
	
	function __construct(){
		
		$this->m_bShowTrace = 0;
		$this->m_dbConn = new dbop(false);
		$this->m_dbConnRoot = new dbop(true);
	}
	
	
	public function sentPaymentVoucherNotificationToCommittee($ModuleRefID,$mode,$dbName,$SocietyID,$client_id,$SetDBName){
			
		try{
			
			$Test = false;
			$TreasurerName = '';
			$TreasurerEmail = '';
			$SecretaryName = '';
			$SecretaryEmail = '';
			$ChairmanName = '';
			$ChairmanEmail = '';
			
			if($SetDBName == true)
			{
				$this->m_dbConn = new dbop(false,$dbName);
				$this->obj_addduser = new adduser($this->m_dbConnRoot,$this->m_dbConn);
				$this->obj_initialize = new initialize($this->m_dbConnRoot);
				$this->obj_voucher = new voucher($this->m_dbConn);
				$this->obj_Utility = new utility($this->m_dbConn,$this->m_dbConnRoot);
				$this->objFetchData = new FetchData($this->m_dbConn);
			}
			
			$VoucherDetailsQuery = "SELECT v.VoucherNo, v.VoucherTypeID, pd.PaidTo, pd.Amount FROM `voucher` as v JOIN paymentdetails as pd ON v.RefNo = pd.id AND RefTableID = '".TABLE_PAYMENT_DETAILS."' and v.RefNo = '".$ModuleRefID."'";
			$VoucherDetails = $this->m_dbConn->select($VoucherDetailsQuery);
			
			if(!empty($VoucherDetails))
			{
				$voucherNo = $VoucherDetails[0]['VoucherNo'];
				$vouchertype = $VoucherDetails[0]['VoucherTypeID'];	
				$PaidTo = $VoucherDetails[0]['PaidTo'];	
				$PaidName = $this->obj_Utility->getLedgerName($VoucherDetails[0]['PaidTo']);	
				$Amount = $VoucherDetails[0]['Amount'];	
				
				$VoucherArray = $this->obj_voucher->GetVoucherDetails($voucherNo ,$vouchertype, true);
				$societyDetails = $this->objFetchData->GetSocietyDetails($SocietyID);
				
				$socContactNo = $this->objFetchData->objSocietyDetails->sSocietyEmailContactNo;
				$socAddress = $this->objFetchData->objSocietyDetails->sSocietyAddress;
				$showSocietyAddressInEmail = $this->objFetchData->objSocietyDetails->bSocietyAddressInEmail;
				
				$societyEmailID = $this->objFetchData->objSocietyDetails->sSocietyEmail;	
					
					$BankID = 0;
					for($j = 0; $j < count($VoucherArray); $j++)
					{
						if($BankID == 0)
						{
							if($VoucherArray[$j]['VoucherTypeID'] == VOUCHER_RECEIPT)
							{
								$BankID = $VoucherArray[$j]['ToLedgerID'];
							}
							else if($VoucherArray[$j]['VoucherTypeID'] == VOUCHER_PAYMENT || $VoucherArray[$j]['VoucherTypeID'] == VOUCHER_CONTRA)
							{
								$BankID = $VoucherArray[$j]['ByLedgerID'];	
							} 
						}
					}
				
					$prefix = $this->obj_Utility->GetPreFix($VoucherArray[0]['VoucherTypeID'],$BankID);
					
					if(!empty($prefix))
					{
						$prefix = $prefix.'-';
					}
					$prefix .= $VoucherArray[0]['ExternalCounter'];
					
					/*
					if($_SESSION['society_id']  == 59)
					{
						
						echo "<pre>";
						print_r($_SESSION);
						echo "</pre>";
							
					}
					*/
					
					
					$CommitteeMemberDetailsQuery = "SELECT c.position, mof.other_name, mof.other_email FROM `commitee` as c JOIN mem_other_family as mof ON c.member_id = mof.mem_other_family_id WHERE c.position in ('".TREASURER."','".SECRETARY."','".CHAIRMAN."')";
					$CommitteeMemberDetails = $this->m_dbConn->select($CommitteeMemberDetailsQuery);
					
					$sqlClientDetails = "SELECT  c.email_footer , l.`EmailID`, l.`Password`  FROM `client` as c JOIN loginemailids as l ON c.id = l.client_id WHERE c.`id` in(SELECT client_id FROM society where society_id = '".$_SESSION['society_id']."')";
					
					$ClientDetails = $this->m_dbConnRoot->select($sqlClientDetails);
					//var_dump($ClientDetails);
					
					$EmailFooter=$ClientDetails[0]['email_footer'];
					
					if($showSocietyAddressInEmail==1)
					{
						$EmailFooter = $socAddress;
					}
					
					if($Test == true)
					{
						$userURL = "http://localhost/beta_aws_test_master/login.php?url=print_voucher.php?_**_vno=".$voucherNo."_**_type=".$vouchertype."_**_direct";
					}
					else
					{
						$userURL = "http://www.way2society.com/login.php?url=print_voucher.php?_**_vno=".$voucherNo."_**_type=".$vouchertype."_**_direct";	
					}
					
					if($mode == ADD)
					{
						$mailSubject = "New Payment Voucher ".$prefix." Generated";	
					}
					else
					{
						$mailSubject = "Payment Voucher ".$prefix." Updated";	
					}
					foreach($CommitteeMemberDetails as $v){
					
						if($v['position'] == TREASURER)
						{
							$TreasurerName = $v['other_name'];
							$TreasurerEmail = $v['other_email'];
							$mailBodyHeader= " Dear ".$v['other_name'].",<br /><br /> Payment Voucher ".$prefix." paid to ".$PaidName." for amount ".$Amount." is generated by ".$_SESSION['name']."<br />";
						}
						else if($v['position'] == SECRETARY)
						{
							$SecretaryName = $v['other_name'];
							$SecretaryEmail = $v['other_email'];
						}
						else if($v['position'] == CHAIRMAN)
						{
							$ChairmanName = $v['other_name'];
							$ChairmanEmail = $v['other_email'];	
						}
					}
					
					$mailBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
									<html xmlns="http://www.w3.org/1999/xhtml">
									 <head>
									  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
									  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
									</head>
									<body style="margin: 0; padding: 0;">					 
										<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
										   <tr>
											 <td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
											   <img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
											  <br />
											  <i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software </b></font></i>
											 </td>
										   </tr>
										   <tr>
											 <td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-right:10px;border-top:none;border-bottom:none;" >
											   <table width="100%">
											   
												<tr><td>'.$ClientDetails[0]['email_header'].'</td></tr>
												<tr><td>'.$mailBodyHeader.'<br />';
												$mailBody .= '<tr>
														<td>Please click below button to check Payment Voucher</td>									
													</tr>
													
													<tr>
														<td align="center">
															<table width="250px" cellspacing="0" cellpadding="0" border="0">
																<tbody>
																	<tr>
																		<br>
																		<td valign="middle" bgcolor="#337AB7" height="40" align="center" style="cursor:pointer;">
																			<a href="'.$userURL.'" target="_blank" style="cursor:pointer;text-decoration: none;">
																				<div style="cursor:pointer;text-decoration: none;" >
																					<font  style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">
																					<b>View Payment Voucher</b>
																					</font>
																				</div>
																			</a>
																	  </td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												<tr><td><br /></td></tr>
												<tr><td>'.$EmailFooter.'</td></tr>
												<tr><td>Email : '.$societyEmailID .'</td></tr>
												<tr><td>Contact No : '. $socContactNo .'</td></tr><tr>
												<td><br /></td>
												</tr>
												<tr>
												<td style="color:#808080;font-size:10px" >
												Note: This is an automated email. Sometimes spam filters block automated emails. If you do not find the email in your inbox, please check your spam filter or bulk email folder. Mark this email as "Not Spam" to get further emails in inbox.
												</td>
												</tr>
											   </table>
											 </td>
										   </tr>
										   <tr>
											 <td bgcolor="#CCCCCC" style="padding: 2px 20px 2px 20px;border-top:none;">
											   <table cellpadding="0" cellspacing="0" width="100%">           
												 <td >             
													<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
												 </td>
												 <td align="center"  style="padding: 0px 50px 0px 1px;">
												<table>
												<tr>
												<td><a href="https://play.google.com/store/apps/details?id=com.ionicframework.way2society869487&amp;rdid=com.ionicframework.way2society869487" target="_blank">
												<img src="http://way2society.com/images/app.png" width="120" height="50" style="style=" top:10px;"></a></td></tr>				
												</table>
											 </td>
												 <td align="right">
												  <table border="0" cellpadding="0" cellspacing="0">
												   <tr>
													<td>
														<a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a>                  
													</td>
													<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
													<td>
														<a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a>                 
													</td>
												   </tr>
												  </table>
												 </td>             
											   </table>
											 </td>
										   </tr>
										 </table>   
									</body>
									</html>';
					
					$EMailID = $ClientDetails[0]['EmailID'];
					$Password = $ClientDetails[0]['Password'];;
					
					if(isset($EMailIDToUse) && $EMailIDToUse['status'] == 0)
					{
						
							$EMailID = $EMailIDToUse['email'];
							$Password = $EMailIDToUse['password'];
					}	
					
				/*	
					echo "Sender Email ID".$EMailID;
					echo "Sender Pass word".$Password;*/
				
					require_once('../swift/swift_required.php');
					//aws config
					include_once("email.class.php");
					$AWS_Config = CommanEmailConfig();
				 	$transport = Swift_SmtpTransport::newInstance($AWS_Config[0]['Endpoint'],$AWS_Config[0]['Port'] , $AWS_Config[0]['Security'])
				  		->setUsername($AWS_Config[0]['Username'])
				  		->setPassword($AWS_Config[0]['Password']);	 
					//$transport = Swift_SmtpTransport::newInstance('103.50.162.146',587)
											//->setUsername($EMailID)
											//->setSourceIp('0.0.0.0')
											//->setPassword($Password); 
					
					$message = Swift_Message::newInstance();
					
					if(!empty($TreasurerEmail))
					{
							$message->setTo(array(
						 
							 $TreasurerEmail => $TreasurerName
						 
						 ));	
					}
					
					if(!empty($SecretaryEmail))
					{
						 $message->setCc(array(
						 $SecretaryEmail => $SecretaryName
						));
					}
				 
					if(!empty($ChairmanEmail))
					{
						 $message->setCc(array(
						 $ChairmanEmail => $ChairmanName
						));
					}
					
					$societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
					if($societyEmail == '')
					{
						$societyEmail = "techsupport@way2society.com";
					}
					 
					$societyName =  $this->objFetchData->objSocietyDetails->sSocietyName;
							
					$society_CCEmail =  $this->objFetchData->objSocietyDetails->sSocietyCC_Email;
					
					
					if($society_CCEmail <> "")
					{
						$message->setReplyTo(array($societyEmail => $societyName,$society_CCEmail => $societyName));
					 }
					 else
					 {		
						   $message->setReplyTo(array(
						   $societyEmail => $societyName
						));
					 }
					 
					$message->setSubject($mailSubject);
					$message->setBody($mailBody);
					$message->setFrom($EMailID,$Password);
					$message->setContentType("text/html");	
					
					$mailer = Swift_Mailer::newInstance($transport);
				
					$result = $mailer->send($message);
					
					if($result > 0)
					{
						return true;
					}
					else
					{
						return false;
					}
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();	
		}			
	}
}

/*$Obj = new CommitteeNotification();

$Query = "SELECT eq.id, eq.ModuleRefID, eq.`Mode`, eq.`dbName`, eq.`SocietyID`, s.`client_id` FROM `emailqueue` as eq JOIN society as s ON s.society_id = eq.SocietyID where eq.ModuleTypeID = '".VOUCHER_PAYMENT."' And eq.SourceTableID = '".TABLE_PAYMENT_DETAILS."' AND eq.`Status` = 0 AND eq.SocietyID != 0 AND eq.dbName != '' order by eq.SocietyID";
$PaymentEntryInQueue = $Obj->m_dbConnRoot->select($Query);

$dbName = $PaymentEntryInQueue[0]['dbName'];
$SocietyID = $PaymentEntryInQueue[0]['SocietyID'];
$SetDBName = false;

for($i = 0 ; $i < count($PaymentEntryInQueue) ; $i++)
{
	$ModuleRefID = $PaymentEntryInQueue[$i]['ModuleRefID'];
	$queueID = $PaymentEntryInQueue[$i]['id'];
	$client_id = $PaymentEntryInQueue[$i]['client_id'];
	$mode = $PaymentEntryInQueue[$i]['Mode'];
	 
	if($dbName <> $PaymentEntryInQueue[$i]['dbName'] || $i == 0)
	{
		$dbName = $PaymentEntryInQueue[$i]['dbName'];
		$SocietyID = $PaymentEntryInQueue[$i]['SocietyID'];	
		$SetDBName = true;
	}
	else
	{
		$SetDBName = false;
	}
	$Obj->sentPaymentVoucherNotificationToCommittee($ModuleRefID,$mode,$dbName,$SocietyID,$client_id,$SetDBName,$queueID);	
}*/

?>
