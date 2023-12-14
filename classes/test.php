<?php //include_once "includes/head.php"; ?>	
<?php //include_once "includes/head_m.php"; ?>	
<?php include_once "includes/head_s.php"; ?>	

<center><h2>Email Test in Classes</h2></center>


<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
echo "<BR>My Array 1";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
echo "<BR>My Array 2";

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

echo "<BR>My Array 4";

	$recipients = array(
   'payalchaurasia0011786@gmail.com' => 'Person 1',
	 'prashant@way2society.com' => 'PSSS',
   'kajal09chaurasiya@gmail.com' => 'Person 2',
    'aakash.j@somaiya.edu' => 'Person 3',
	'shahdigambar5@gmail.com' => 'Person 4',
	 'shahdigambar@gmail.com' => 'Person 5',
    'akhijais60@gmail.com' => 'Person 6',
	 'jaiswaraakash0@gmail.com' => 'Person 7',
	 'akhijais@yahoo.com' => 'Person 8',
	'softel.in@gmail.com' => 'SOOFTY',
	);
	 
echo "<BR>My Loop";
$counter = 1;
foreach($recipients as $email => $name)
{
	echo "<BR>New loop : " . $counter ++ ;
   	sendEmail($email, $name);
	if($counter > 2)
	{
		//break;
	}
}

function sendEmail($emailid, $name)

{
	echo "<br>sending email to emailid: $emailid  & name: $name  -- result:";

$mail = new EMail;
 
$mail->Server = "md-in-64.webhostbox.net";    
 
//Enter your FULL email address:
$mail->Username = 'test@way2society.com';    
 
//Enter the password for your email address:
$mail->Password = 'test123';
    
//Enter the email address you wish to send FROM (Name is an optional friendly name):
$mail->SetFrom("test@way2society.com");  
 
//Enter the email address you wish to send TO (Name is an optional friendly name):
$mail->AddTo($emailid,$name);
 
 
//Enter the Subject of your message:
$mail->Subject = "Test1 from server";
 
//Enter the content of your email message:
$mail->Message = "Server Test 1";
 
//Optional extras
$mail->ContentType = "text/html";    // Defaults to "text/plain; charset=iso-8859-1"
//$mail->Headers['X-SomeHeader'] = 'abcde';    // Set some extra headers if required
 
echo $success = $mail->Send(); //Send the email.
 
 }
 
 
/*
This is the EMail class.
Anything below this should not be edited unless you really know what you're doing.
*/
class EMail
{
  const newline = "\r\n";
 
  private
    $Port, $Localhost, $skt;
 
  public
    $Server, $Username, $Password, $ConnectTimeout, $ResponseTimeout,
    $Headers, $ContentType, $From, $To, $Cc, $Subject, $Message,
    $Log;
 
  function __construct()
  {
	  echo "<BR>In ctr";
    $this->Server = "md-in-64.webhostbox.net";
    $this->Port = 25;
    $this->Localhost = "localhost";
    $this->ConnectTimeout = 30;
    $this->ResponseTimeout = 8;
	$this->SMTPSecure = 'ssl'; 
	
    $this->From = array();
    $this->To = array();
    $this->Cc = array();
    $this->Log = array();
    $this->Headers['MIME-Version'] = "1.0";
    $this->Headers['Content-type'] = "text/plain; charset=iso-8859-1";

	  echo "<BR>In ctr ok";  }
 
  private function GetResponse()
  {
    stream_set_timeout($this->skt, $this->ResponseTimeout);
    $response = '';
    while (($line = fgets($this->skt, 515)) != false)
    {
 $response .= trim($line) . "\n";
 if (substr($line,3,1)==' ') break;
    }
    return trim($response);
  }
 
  private function SendCMD($CMD)
  {
    fputs($this->skt, $CMD . self::newline);
 
    return $this->GetResponse();
  }
 
  private function FmtAddr(&$addr)
  {
    if ($addr[1] == "") return $addr[0]; else return "\"{$addr[1]}\" <{$addr[0]}>";
  }
 
  private function FmtAddrList(&$addrs)
  {
    $list = "";
    foreach ($addrs as $addr)
    {
      if ($list) $list .= ", ".self::newline."\t";
      $list .= $this->FmtAddr($addr);
    }
    return $list;
  }
 
  function AddTo($addr,$name = "")
  {
    $this->To[] = array($addr,$name);
  }
 
  function AddCc($addr,$name = "")
  {
    $this->Cc[] = array($addr,$name);
  }
 
  function SetFrom($addr,$name = "")
  {
    $this->From = array($addr,$name);
  }
  function Send()
  {
    $newLine = self::newline;
 
    //Connect to the host on the specified port
    $this->skt = fsockopen($this->Server, $this->Port, $errno, $errstr, $this->ConnectTimeout);
 
    if (empty($this->skt))
	{
		echo "<BR>fsockopen failed";
      return false;
	}
 
    $this->Log['connection'] = $this->GetResponse();
 
    //Say Hello to SMTP
    $this->Log['helo']     = $this->SendCMD("EHLO {$this->Localhost}");
 
    //Request Auth Login
    $this->Log['auth']     = $this->SendCMD("AUTH LOGIN");
    $this->Log['username'] = $this->SendCMD(base64_encode($this->Username));
    $this->Log['password'] = $this->SendCMD(base64_encode($this->Password));
 
    //Email From
    $this->Log['mailfrom'] = $this->SendCMD("MAIL FROM:<{$this->From[0]}>");
 
    //Email To
    $i = 1;
    foreach (array_merge($this->To,$this->Cc) as $addr)
      $this->Log['rcptto'.$i++] = $this->SendCMD("RCPT TO:<{$addr[0]}>");
 
    //The Email
    $this->Log['data1'] = $this->SendCMD("DATA");
 
    //Construct Headers
    if (!empty($this->ContentType))
      $this->Headers['Content-type'] = $this->ContentType;
    $this->Headers['From'] = $this->FmtAddr($this->From);
    $this->Headers['To'] = $this->FmtAddrList($this->To);
    if (!empty($this->Cc))
      $this->Headers['Cc'] = $this->FmtAddrList($this->Cc);
    $this->Headers['Subject'] = $this->Subject;
    $this->Headers['Date'] = date('r');
 
    $headers = '';
    foreach ($this->Headers as $key => $val)
      $headers .= $key . ': ' . $val . self::newline;
 
    $this->Log['data2'] = $this->SendCMD("{$headers}{$newLine}{$this->Message}{$newLine}.");
 
    // Say Bye to SMTP
    $this->Log['quit']  = $this->SendCMD("QUIT");
 
    fclose($this->skt);
 
    return substr($this->Log['data2'],0,3) == "250";
  }
}
?>


	

<?php include_once "includes/foot.php"; ?>														
