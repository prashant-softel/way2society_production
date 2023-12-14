 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class smsQuota extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../viewSMSQuota.php";
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
	}
	//Used to get society selection box
	public function comboboxForSociety($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$str.="<option value='0'>Please Select</option>";
		$data = $this->m_dbConnRoot->select($query);
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
	public function display($res)
	{
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		if($res<>"")
		{
			?>
            <thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<th style="text-align:center">Client Name</th>
            		<th style="text-align:center">Society Name</th>
            		<th style="text-align:center">Sell Date</th>
                    <th style="text-align:center">Sold By</th>
                    <th style="text-align:center">SMS Allotted</th>
                    <th style="text-align:center">Amount</th>
                    <th style="text-align:center">Payment Status</th>
                    <?php if($_SESSION['login_id']=="4")
					{
					?>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
                    <?php
					}
					?>
            	</tr>
            </thead>
            <tbody>
            <?php
				for($i=0;$i<sizeof($res);$i++)
				{
			 ?>		
             		<tr height="25" bgcolor="#BDD8F4" align="center"> 
                		<td align="center"><?php echo $res[$i]['client_name'];?></td>
                		<td align="center"><?php echo $res[$i]['society_name'];?> </td>
                		<td align="center"><?php echo getDisplayFormatDate($res[$i]['SellDate']);?> </td>
                        <td align="center"><?php echo $res[$i]['SoldBy'];?> </td>
                        <td align="center"><?php echo $res[$i]['SMSAllotted'];?></td>
                        <td align="center"><?php echo $res[$i]['Amount'];?> </td>
                        <?php
						if($res[$i]['Payment_Received']=="1") 
						{
						?>
                         	<td align="center">Paid</td>
                        <?php
						}
						if($res[$i]['Payment_Received']=="2")
						{
						?>
                        	<td align="center">Unpaid</td>
                        <?php
						}
						if($_SESSION['login_id']=="4")
						{
						?>
                    	<td><a href="addSMSQuota.php?method=edit&smsQuotaId=<?php echo $res[$i]['Id'];?>"><img src="images/edit.gif"  /></a></td>
                    	<td><a onclick="deleteSMSQuota(<?php echo $res[$i]['Id']?>)"><img src="images/del.gif" /></a></td>
                       	<?php
						}
						?>
                    </tr>
      				<?php
					}      
					?>
               	</tbody>
         <?php
		}
		else
		{
		?>
        	<thead>
            	<tr  height="30" bgcolor="#FFFFFF">
                	<th style="text-align:center">Client Name</th>
            		<th style="text-align:center">Society Name</th>
            		<th style="text-align:center">Sell Date</th>
                    <th style="text-align:center">Sold By</th>
                    <th style="text-align:center">SMS Allotted</th>
                    <th style="text-align:center">Amount</th>
                    <th style="text-align:center">Payment Status</th>
            		<?php if($_SESSION['login_id']=="4")
					{
					?>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
                    <?php
					}
					?>
            	</tr>
            </thead>
        	<tbody>
            	<tr>
                <?php if($_SESSION['login_id']=="4")
				{
				?>	
                	<td></td>
                    <td></td>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				else
				{
				?>
                    <td></td>
                    <td></td>
                    <td></td>
            		<td style="text-align:center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
                    <td></td>
                    <td></td>
                    <td></td>
                 <?php
				}
				?>
            	</tr>
            </tbody>
       	<?php	
		}
	}
	public function pgnation($socId)
	{
		if($socId == "0")
		{
			 $query = "SELECT sa.`Id`,c.`client_name`,s.`society_name`,sa.`SellDate`,sa.`SoldBy`,sa.`SMSAllotted`,sa.`Amount`,sa.`Payment_Received` FROM `sms_allotment` sa,`society` s, `client` c WHERE c.`id` = s.`client_id` AND sa.`Status` = 'Y' AND s.`society_id` = sa.`SocietyId` AND sa.`SocietyId`='".$_SESSION['society_id']."'";
		}
		else
		{
			$query = "SELECT sa.`Id`,c.`client_name`,s.`society_name`,sa.`SellDate`,sa.`SoldBy`,sa.`SMSAllotted`,sa.`Amount`,sa.`Payment_Received` FROM `sms_allotment` sa,`society` s, `client` c WHERE c.`id` = s.`client_id` AND sa.`Status` = 'Y' AND s.`society_id` = sa.`SocietyId` AND sa.`SocietyId`=".$socId;
		}
		//$sql = "SELECT * FROM `sms_allotment`";
		//echo "Query:".$query;
		$res = $this->m_dbConnRoot->select($query);
		//echo "In res";
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		for($i=0;$i<sizeof($res);$i++)
		{
			$res[$i]['SellDate']=getDisplayFormatDate($res[$i]['SellDate']);
		}
		$data = $this->display($res);
		//echo "<pre>";
		//print_r($data);
		//echo "</pre>";
		return $data;
	}
	public function deleteSMSQuota($Id)
	{
		$sql =  "Update `sms_allotment` set Status = 'N' where `Id` = ".$Id;
		$res = $this->m_dbConnRoot->update($sql);
		return $res;
	}
}
?>	