<?php
class helpline
{
	  public $m_dbConn;	
	  function __construct($dbConn)
  	  {
	  //** assing the connection to the variable
	  $this->m_dbConn = $dbConn;
     }
	 function insertrecord($cat,$category,$name,$contact,$details)
	 {
		 $sql="";
		if($category=="")
		{
			$fetchcat=$this->fetchcategoryname($cat);
			$catname=$fetchcat[0]['category'];
			$sql="insert into helpline(`category`, `name`, `numbers`, `Note`) values('".$catname."','".$name."','".$contact."','".$details."')";
		} 
		else
		{
			$sql="insert into helpline(`category`, `name`, `numbers`, `Note`) values('".$category."','".$name."','".$contact."','".$details."')";
		}
		$result = $this->m_dbConn->insert($sql);
		return "0";
	 }
	 function updaterecord($id,$cat,$category,$name,$contact,$details)
	 {
		  
		  $sql="";
		if($category=="")
		{
			$fetchcat=$this->fetchcategoryname($cat);
			$catname=$fetchcat[0]['category'];
			
			 $sql="update helpline set `category`='".$catname."' , `name`='".$name."', `numbers`='".$contact."', `Note`='".$details."' where id='".$id."'";
		} 
		else
		{
			 $sql="update helpline set `category`='".$category."' , `name`='".$name."', `numbers`='".$contact."', `Note`='".$details."' where id='".$id."'";
		}
		$result = $this->m_dbConn->update($sql);
		return "0";
	 }
	 function fetchcategoryname($cat)
	 {
		 return $result=$this->m_dbConn->select("select category from helpline where id='".$cat."'");
	 }
	 function fetchdetails()
	 {
		return $result = $this->m_dbConn->select("select * from helpline where status='Y'"); 
	 }
	 function getViewDetails($id)
	 {
		 return $result=$this->m_dbConn->select("select * from helpline where id='".$id."'");
	 }
	 public function deleting($id)
	 {
	 $sql = "update  `helpline` set `status`='N' where id='".$id."'";
		$res = $this->m_dbConn->update($sql);
		return $res;
	 }
	 public function combobox($query, $id, $defaultText = 'Please Select', $defaultValue = '')
	 {
		
		$str = '';
		$other="Other";
		$str.="<OPTION VALUE=".' '.">";
		$str.='Select Category'."</OPTION>";
		
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
		$str.="<OPTION VALUE=".$other.">";
		$str.=$other."</OPTION>";
		return $str;
	}
	
}
?>