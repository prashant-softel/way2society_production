<?php
class OTP_Setting
{
		
  public $smConn;
   public $m_dbConn;	

  function __construct($dbConn,$smConn)
  {
	  $this->smConn = $smConn;
	  $this->m_dbConn = $dbConn;
  }
	function updaterecord($rep,$new,$exp)
	{
		return $status = $this->smConn->update("UPDATE `feature_setting` SET `OTP_Status_Rep`='".$rep."',`OTP_Status_New`='".$new."',`OTP_Status_Exp` ='".$exp."'");
	}
	function otpstatus()
	{
		return $status1=$this->smConn->select("SELECT `OTP_Status_Rep`,`OTP_Status_New`,`OTP_Status_Exp` from `feature_setting`");
	}
	
}
   