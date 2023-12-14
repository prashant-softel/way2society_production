<?php

 
class srreport
{
  public $m_dbConn;	
  public $m_dbConnRoot;		
  
  
function __construct($dbConn)
	{
	   //** assing the connection to the variable
	   $this->m_dbConnRoot = new dbop(true);	
	  $this->m_dbConn = $dbConn;
	  
		
	}
	public function GetSocietyName($soc_id)
	{
		 return $result = $this->m_dbConnRoot->select("select society_name from society where society_id='".$soc_id."'");
	}
	public function GetcategoryName($cat)
	{
		 return $result = $this->m_dbConn->select("select category from `servicerequest_category` where ID='".$cat."'");
	}
	
		public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	{
		
		$str = '';

		$all="All Categories";
		$str.="<OPTION VALUE=".' '.">";
		$str.='Select Category'."</OPTION>";
		$str.="<OPTION VALUE=".$all.">";
		$str.=$all."</OPTION>";
		/*if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}*/
		//echo "$query";
		$data = $this->m_dbConn->select($query);
		//print_r($data);
		if(!is_null($data))
		{
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
	
	
	public function getDetails($cat,$str)
	{
		$sql='';
		
		$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."'  and  service_request.`visibility`='1' and service_request.`status` in ($str) ";
			if($cat=="All")
			{
			}
			else
			{
				$sql .= "AND service_request.`category` = '".$cat."' ";
			}
			$result = $this->m_dbConn->select($sql);
		 	if($cat=="All")
			{
				$result['catid']="All";
			}
			return $result;
	}
	
	
		
	public function getViewDetails($request_no,$isview=false)
	{ 
		$fieldname='request_id';
		if($isview==true)
		{
				$fieldname='request_no';
		}
		$sql = "SELECT service_request.* FROM `service_request` WHERE service_request.`society_id` = '".$_SESSION['society_id']."' AND `".$fieldname."` = '".$request_no."'  and  `visibility`='1'";	
		
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	
}
?>