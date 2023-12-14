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
		$insertQuery6 = "insert into `round_master` (`Name`,`Description`) value('".$_POST['master_names']."','".$_POST['desc']."')";
	 	$data = $this->smConn->insert($insertQuery6);
	 	//echo "Insert".$Insert;
		if($_POST['checkpost_id']<>"")
			{
				foreach($_POST['checkpost_id'] as $k => $v)
				{
					 $insertQuery7 = "insert into `round_checkpost_master` (`round_id`,`checkpost_id`) value('".$data."','".$v."')";
					 $res = $this->smConn->insert($insertQuery7);
				}
			}
			//die();
		$this->actionPage='../round_master.php';
	 	return "Insert";	   
 	}
 	else if($_POST['insert']=='Update')
 	{ 	
 		
 			// $this->smConn->begin_transaction();
	 	  	$updateQUery="Update `round_master` set `Name`='".$_POST['master_names']."',`Description`='".$_POST['desc']."' where `id`='".$_REQUEST['id']."'";
			$Update = $this->smConn->update($updateQUery);	
			 
			$delete = "DELETE FROM `round_checkpost_master` WHERE `round_id` = '".$_REQUEST['id']."'";
			$del=$this->smConn->delete($delete);
			foreach($_POST['checkpost_id'] as $k => $v)
			{
				$insertQuery7 = "insert into `round_checkpost_master` (`round_id`,`checkpost_id`) value('".$_REQUEST['id']."','".$v."')";
				$res = $this->smConn->insert($insertQuery7);	
			}
			
			
 		
		$this->actionPage='../round_master.php';
 	 	return "Update";	
         
    
 	}
    
        

}

public function getschedule_list_report()
{
	
	$sql = "SELECT rm.*, count(rcm.id) as checkpostCount FROM `round_master` as rm join round_checkpost_master as rcm on rcm.round_id = rm.id group by rcm.round_id";
	$res = $this->smConn->select($sql);
	return $res;
}

public function selecting($Id)
{
	$chkid_array= array();
	$sql = "SELECT `Name`, `Description` FROM `round_master` WHERE `id` = '".$Id."'";
	$res =  $this->smConn->select($sql);	
	
	 $sql1 = "SELECT `checkpost_id` FROM `round_checkpost_master` WHERE `round_id` = '".$Id."'";
	$res1 =  $this->smConn->select($sql1);
	for($i= 0; $i<sizeof($res1); $i++)
	{
		array_push($chkid_array,$res1[$i]['checkpost_id']); 
	}
	//var_dump($chkid_array);
	$res[0]['Checkpost']= $chkid_array;
	return $res;	
}

public function combobox($query, $id)
{		
    $str.="<option value=''>Please Select</option>";
    $data = $this->smConn->select($query);
    
    if(!is_null($data))
    {
		$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
       foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
						//echo $str;
					}
					else
					{
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					$i++;
				}
			
        }
    }
            return $str;                
}

public function combobox1($query,$name,$id)
	{
		  $data = $this->smConn->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
					}
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	
public function deleting($Id)
{
	$sql = "DELETE a.*, b.* FROM round_checkpost_master as a, round_master as b WHERE b.id=a.round_id and b.id='".$_REQUEST['id']."'";
	$res=$this->smConn->delete($sql);
	return $res;	
}
}