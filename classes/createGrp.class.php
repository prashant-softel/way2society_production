<?php
include_once("defaults.class.php");
include_once("utility.class.php");
include_once("/includes/dbop.class.php");
set_time_limit(0);
class createGrp extends dbop
{
	private $m_dbConn;
	function __construct($dbConn)
	{
			//echo "ctor";
		$this->m_dbConn = $dbConn;
	}
 	function addGroupDetails($gN, $gD)
 	{
		//echo "In Group Details..";
		$url= HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=1&name=$gN&des=$gD&dbName=".$_SESSION['dbname'];
		//return "<br>url".$url;
		$res = file_get_contents($url);
		//echo "In group details: ";
		//print_r($res);    
		if($res=="success")
		{
			return "success";
		}
		else if($res="problem")
		{
			return "problem";
		}
		else
		{
			return "failure";
		}
  }
  function addGrpMemberDetails($MemId, $gName)
  {
	//echo "In add grp member details";
	//echo "name: ".$grpName;
  	$res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=5&name=$gName&dbName=".$_SESSION['dbname']);
	$jRes = json_decode($res,true);
	//echo "mId:";
	//echo "<pre>";
	//print_r($MemId);
	//echo "</pre>";
	foreach($jRes as $k => $v)
	{
		$gId = $v;
		break;
	}
	$len=sizeof($MemId);
	for($i=0;$i<$len;$i++)
	{
		$mId=urlencode($MemId[$i]);
		$gId=urlencode($gId);
		//echo "gid:".$gId;
		$memRes=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/servletGrpMember?mode=1&gId=$gId&memId=$mId&dbName=".$_SESSION['dbname']);
		//echo "Mem Result:".$memRes;
	}
	return "success";
  }
  function comboboxForMemberSelection($query,$id, $defaultText = 'Please Select', $defaultValue = '')
  {
     if($defaultText != '')
     { 
        ?>
          <ul>
            <li>&nbsp;<input type="checkbox" id ='0' class="checkBox" name="mem_id[]" checked/>&nbsp;<?php echo $defaultText ; ?></li>
		<?php 
     }
     $data = $this->m_dbConn->select($query);
     //echo $data;
     for($i = 0; $i < sizeof($data); $i++)
     { 
        ?>
        	<li>&nbsp;<input type="checkbox" id="<?php echo $data[$i]['mem_other_family_id']; ?>" class="checkBox" onChange="uncheckDefaultCheckBox(this.id);" name="<?php echo $data[$i]['mem_other_family_id']; ?>"/> &nbsp; <?php echo $data[$i]['other_name'];?></li>
        <?php   
     }
        ?>
      	</ul> 
        <?php
  }
  function getMemDetails($gId)
  {
	  $res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/servletGrpMember?mode=6&gId=$gId&dbName=".$_SESSION['dbname']);
	  $jRes=json_decode($res,true);
	  /*$memId=array();
	  for($i=0;$i<sizeof($jRes);$i++)
	  {
		  $memId[$i]=$jRes[$i]['MemberId'];
	  }*/
	  return $jRes;
  }
  function getGroupDetails($gId)
  {
	  $res=file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=6&id=$gId&dbName=".$_SESSION['dbname']);
	  $jRes=json_decode($res,true);
	  return $jRes;
  }
  public function updateGroupDetails($gId,$gN,$gD)
  {
		$res = file_get_contents(HOST_NAME."8080/MinutesOfMeetingS/Servlet1?mode=2&id=$gId&name=$gN&des=$gD&dbName=".$_SESSION['dbname']); 
		if($res=="success")
		{
			return "success";
		}
		else if($res="problem")
		{
			return "problem";
		}
		else
		{
			return "failure";
		}		
	}
}
?>
