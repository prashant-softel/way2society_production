<?php

require_once('swift/swift_required.php');
include_once('classes/include/dbop.class.php');


class forgetpassword
{
	public $m_dbConn;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		echo "<br>".$this->sendEmail('madhurprsh@gmail.com','123', 'Madhura Shetye','','');
	}
	
	private function sendEmail($email, $password, $name,$member_id, $fbcode)
	{
		
		$mailSubject = 'Account Password Recovery';
		$mailTxt = '';
		
		if($password == '' && $fbcode <> '')
		{
			$mailTxt .= 'We have identified that you are connected to way2society.com using your facebook account. Kindly select "Sign in with Facebook" option and use your Facebook credentials to login.<br><br>';
		}
		else
		{
			$mailTxt .="Your log-in details are as given below:<br><br>"; 
			$mailTxt .='Password : <b>' . $password."</b>";
		}
		$mailBody .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
								<tr>
									<td>Dear <b>'.$name.'</b>,<br /><br />'.$mailTxt.'<br />
									</td>                   
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td align="center">
										<table width="250px" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                <tr>
                                                    <td valign="middle" bgcolor="#337AB7" height="40" align="center">
                                                        <a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="http://way2society.com/login.php?mlog">Login To Way2Society</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td>Always keep your username and password confidential to prevent any unauthorized access of your account.<br><br>
										If you need assistance, please write to us at cs@way2society.com <br><br>
									</td>                   
								</tr>
								<tr>
									<td font="colr:#999999;">Thank You,<br>way2society.com</td>
								</tr>
							   </table>
							 </td>
						   </tr>
						   <tr>
							 <td bgcolor="#CCCCCC" style="padding: 20px 20px 20px 20px;border-top:none;">
							   <table cellpadding="0" cellspacing="0" width="100%">           
								 <td >             
									<a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a>              
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
		//$mailBody .='Always keep your username and password confidential to prevent any unauthorized access of your account.<br><br>';
		//$mailBody .='If you need assistance, please write to us at cs@way2society.com <br><br>';
		//$mailBody .="Thank you,<br>way2society.com";
		//$mail = new PHPMailer(true);
		try
		{
			$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
			$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
			$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
			$sender = "no-reply@way2society.com";
			$senderName = "Way2Society.com";

  

			$transport = Swift_SmtpTransport::newInstance($SMTP_endpoint, 587,'tls')
				  ->setUsername($SMTP_Username)
				  ->setPassword($SMTP_Password);	 
			 
			//Create the message
			$message = Swift_Message::newInstance();
			$message->setTo(array(
			  $email => $name
			 ));
			 
			 $message->setReplyTo(array(
					   $sender => $senderName
					)); 
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom($sender, $senderName);
			$message->setContentType("text/html"); 
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);

			$message = Swift_Message::newInstance();
			$message->setTo(array(
			  $email => $name
			 ));
			 
			 $message->setReplyTo(array(
					   $sender => "Way2Society"
					)); 
			$message->setSubject($mailSubject);
			$message->setBody($mailBody);
			$message->setFrom($sender, "Way2Society");
			$message->setContentType("text/html"); 
			$mailer = Swift_Mailer::newInstance($transport);
			$result = $mailer->send($message);
			
			if($result == 1)
			{
				return 'E-Mail with recovery information has been sent  to ' . $email;
			}
			else
			{
				return 'Failed to send e-mail. Verify E-Mail ID and try again...';
			}
		}
		catch(Exception $exp)
		{
			//log message
			//return "Failed to send email";
			$msg = $exp->getMessage();
			return $msg . "<BR>Pl send screenshot of this error to techsupport@way2society.com";
			return $exp;
		}
	}
}

$conn = new dbop();
$obj = new forgetpassword($conn);

?>
