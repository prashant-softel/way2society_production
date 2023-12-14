<?php 
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("android.class.php");
include_once( "include/fetch_data.php");


class addCustomerReminder extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../customer_reminder.php";
	public $obj_utility;
	public $objFetchData;
	

	function __construct($dbConn, $dbConnRoot,$socID = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		$this->objFetchData = new FetchData($dbConn);
		if($socID <> "")
		{
			$this->objFetchData->GetSocietyDetails($socID);	
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
	}
	public function startProcess()
	{
		if($_REQUEST['btnSubmit']=='Submit')
		{
			$remtitle=$_POST['title'];
			$frequency=$_POST['frequency'];
			$date=$_POST['daterem'];
			$num=$_POST['number'];
			$next_date= date('Y-m-d', strtotime($date. ' -'.$num.'days'));
			$descarea=$_POST['descp'];
			$dbname = $_SESSION["dbname"];
			if(isset($_POST['sms']))
			{
				$sms ="1";
			}
			else
			{
				$sms = "0";
			}
			if(isset($_POST['email']))
			{
				$email = "1";
			}
			else
			{
				$email = "0";
			}
			if(isset($_POST['mnot']))
			{
				$mnotify = "1";
			}
			else
			{
				$mnotify = "0";
			}
			$targetgrp=$_POST['target'];
			$target_exp=json_encode($targetgrp);
			$societyid=$_SESSION['society_id'];
			$status='Y';
			
				$sqlexec1="insert into customer_reminder(title,description,SMS,EMAIL,MOBILE_NOTIFY,frequency,reminder_date,reminder_before,rem_before_num,group_id,create_at,create_by,status) values('".$remtitle."','".$descarea."','".$sms."','".$email."','".$mnotify."','".$frequency."','".getDBFormatDate($next_date)."','".getDBFormatDate($date)."','".$num."','".$target_exp."',now(),'".$societyid."','".$status."')";
				$res=$this->m_dbConnRoot->insert($sqlexec1);				
				
				$sqlexec2="select id from customer_reminder where title='".$remtitle."' and description='".$descarea."' and reminder_date='".getDBFormatDate($next_date)."' and create_by='".$societyid."' and status='".$status."'";
			$res2=$this->m_dbConnRoot->select($sqlexec2);
			$id=$res2[0]['id'];
			
			$rem_status=1;
			$sqlexec3="insert into remindersms(society_id,rem_id,rem_type,EventDate,rem_status,rem_before,EventReminderDate) values('".$societyid."','".$id."','".$rem_status."','".getDBFormatDate($next_date)."','".$status."','".getDBFormatDate($date)."','".getDBFormatDate(date('Y-m-d'))."')";
			$res3=$this->m_dbConnRoot->insert($sqlexec3);
			
			
			return "Insert";
		}
		
		if($_REQUEST['btnSubmit']=='Update')
		{
			$id=$_POST['reminderid'];
			$custid=$_POST['custid'];
			$remtitle=$_POST['title'];
			$frequency=$_POST['frequency'];
			$date=$_POST['daterem'];
			$num=$_POST['number'];
			$next_date= date('Y-m-d', strtotime($date. ' -'.$num.'days'));
			$descarea=$_POST['descp'];
			if(isset($_POST['sms']))
			{
				$sms ="1";
			}
			else
			{
				$sms = "0";
			}
			if(isset($_POST['email']))
			{
				$email = "1";
			}
			else
			{
				$email = "0";
			}
			if(isset($_POST['mnot']))
			{
				$mnotify = "1";
			}
			else
			{
				$mnotify = "0";
			}
			$targetgrp=$_POST['target'];
			$target_exp=json_encode($targetgrp);
			$societyid=$_SESSION['society_id'];
			$status='Y';
			
			$sqlup1="update customer_reminder set title='".$remtitle."',description='".$descarea."',SMS='".$sms."',EMAIL='".$email."',MOBILE_NOTIFY='".$mnotify."',frequency='".$frequency."',reminder_date='".getDBFormatDate($date)."',reminder_before='".$next_date."',rem_before_num='".$num."',group_id='".$target_exp."',update_at=now(),update_by='".$societyid."',status='".$status."' where id='".$custid."'";
			$res=$this->m_dbConnRoot->update($sqlup1);	
			
			$sqlup2="update remindersms set society_id='".$societyid."',EventDate='".getDBFormatDate($next_date)."',rem_before='".getDBFormatDate($date)."' where rem_id='".$custid."' and ID='".$id."'";
			$res3=$this->m_dbConnRoot->update($sqlup2);		
			
			
			
			
			return "Update";
			
		}
		
		
	}
}




?>