<?php
include_once("utility.class.php");

class SM_Report 
{	
  public $m_dbConn;	
  public $m_dbConnRoot;		
  public $smConn;
  public $smConnRoot;
  public $m_objUtility;
  public $m_fetch;
  public $actionPage;
  public $obj_fetch;

  function __construct($dbConn,$dbConnRoot,$smConn,$smConnRoot)
  {
	  //** assing the connection to the variable
	  $this->m_dbConn = $dbConn;
	  $this->m_dbConnRoot = $dbConnRoot;
	  $this->smConn = $smConn;
	  $this->smConnRoot = $smConnRoot;
	  $this->m_objUtility =  new utility($this->m_dbConn);
	  $this->obj_fetch = new FetchData($this->m_dbConn);

  }
  public function startProcess()
{
	
// function AddExpectedVistor()
	$errorExists=0;
	if($_POST['insert']=='Insert')
	{
		$insertQuery6 = "insert into `schedule_master` (`schedule_name`,`round_id`,`frequency`,`round_time`) value('".$_POST['schedulename']."','".$_POST['roundtype']."','".$_POST['frequency']."','".$_POST['time']."')";
	 	$Insert = $this->smConn->insert($insertQuery6);
		$this->actionPage='../shedule_list.php';
	 	return "Insert";	   
 	}
 	else if($_POST['insert']=='Update')
 	{ 	
 		$IsSuccess = true;
 		try
 		{  
 			 $this->smConn->begin_transaction();
	 	  	 $updateQUery="Update `schedule_master` set `schedule_name`='".$_POST['schedulename']."',`round_id`='".$_POST['roundtype']."',`frequency`='".$_POST['frequency']."',`round_time`='".$_POST['time']."' where `id`='".$_REQUEST['id']."'";
			 $Update = $this->smConn->update($updateQUery);		
			 $this->smConn->commit();
 		}
 		catch(Exception $exp)
		{			
			$this->smConn->rollback();
			$IsSuccess = false;				
		}
 		
		$this->actionPage='../shedule_list.php';
 	 	return "Update";	
 	}

}

public function getschedule_list_report()
{
	$sql = "SELECT * FROM `schedule_master` order by id DESC";
	$res = $this->smConn->select($sql);
    for($i=0; $i<sizeof($res); $i++)
    {
        $sql1="select Name from round_master where id ='".$res[$i]['round_id']."'";
        $res1=$this->smConn->select($sql1);
        $res[$i]['round_id']=$res1[0]['Name'];
    }
	return $res;
}

public function selecting($Id)
{
	$sql = "SELECT `schedule_name`, `round_id`, `frequency`, `round_time` FROM `schedule_master` WHERE `id` = '".$Id."'";
	$res =  $this->smConn->select($sql);	
	return $res;	
}

public function deleting($Id)
{
	$sql = "DELETE FROM schedule_master WHERE `id` = '".$Id."'";
	$res =  $this->smConn->delete($sql);
 	return "Delete";	
}

public function schedule_list_report($type)
{
    $sSelectQuery1 = "SELECT * FROM `security_round` where  DATE(round_time)=CURDATE()";
    $result =  $this->smConn->select($sSelectQuery1);	
   
    $selectdb = "";
    $scheduleIds ="";
    
    for($i =0 ; $i< sizeof($result);$i++)
    {
        $scheduleIds .= $result[$i]['schedule_id'].',';
    }
        $scheduleIds=rtrim($scheduleIds , ','); 

    if($scheduleIds <> "")
	{ 
        if($type == 0)
        {   
            $selectdb = "SELECT sm.id,sm.round_id,sm.schedule_name,TIME_FORMAT(sm.round_time, '%H %i %p') as rtime, sm.frequency as frequency,count(rcm.id) as no_of_checkpost FROM `schedule_master` as sm right join `round_checkpost_master` as rcm on rcm.round_id =sm.round_id where sm.id NOT IN(".$scheduleIds.") group by sm.id";
        }
        else
        {   
            $selectdb = "SELECT sm.id,sm.round_id,sm.schedule_name,TIME_FORMAT(sm.round_time, '%H %i %p') as rtime, sm.frequency  as frequency,count(rcm.id) as no_of_checkpost FROM `schedule_master` as sm right join `round_checkpost_master` as rcm on rcm.round_id =sm.round_id where sm.id IN(".$scheduleIds.") group by sm.id";
        }      
    }
    else
    {
        if($type == 0)
            {
                $selectdb = "SELECT sm.id,sm.round_id,sm.schedule_name,TIME_FORMAT(sm.round_time, '%H %i %p') as rtime, sm.frequency as frequency,count(rcm.id) as no_of_checkpost FROM `schedule_master` as sm right join `round_checkpost_master` as rcm on rcm.round_id =sm.round_id group by sm.id";
            }
            else
            {
                $selectdb = "SELECT sm.id,sm.round_id,sm.schedule_name,TIME_FORMAT(sm.round_time, '%H %i %p') as rtime, sm.frequency as frequency,count(rcm.id) as no_of_checkpost FROM `schedule_master` as sm right join `round_checkpost_master` as rcm on rcm.round_id =sm.round_id where sm.id IN('0') group by sm.id";
            }
    }

    $result =  $this->smConn->select($selectdb);
    return $result;
  //  }
}

public function combobox($query, $id)
{		
    $str="<option value=''>Please Select</option>";
    $data = $this->smConn->select($query);
    
    if(!is_null($data))
    {
        foreach($data as $key => $value)
        {
            $i=0;
            foreach($value as $k => $v)
            {
                if($i==0)
                {
                    if($v==$id)
                    {
                        $sel = "selected";
                    }
                    else
                    {
                        $sel = "";	
                    }
                        $str.="<OPTION VALUE=".$v." ".$sel.">";
                }
                    else
                    {
                        $str.=$v."</OPTION>";
                    }
                    $i++;
            }
        }
    }
            return $str;                
}

}