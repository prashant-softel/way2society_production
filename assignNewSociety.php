<?php include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");
	include_once("classes/assignSociety.class.php");
	
	$objNewSociety = new assign_society($m_dbConn, $m_dbConnRoot);		
	$loginID = base64_decode($_REQUEST['loginID']);		
	$clientID = base64_decode($_REQUEST['ClID']);	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

	<script type="text/javascript" src="js/ajax.js"></script> 
    <script type="text/javascript" src="js/populateData.js"></script>   
   	<script tytpe="text/javascript" src="js/ajax_new.js"></script>    
	<link rel="stylesheet" type="text/css" href="css/tabs.css"> 
    <script type="text/javascript" src="js/assignNewSociety.js"></script>       
	<script language="javascript" type="application/javascript">
	var iEncryptedClientID = '<?php echo $_REQUEST['ClID'] ?>';	
	</script>
</head>

<body>
<br>	
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Client Details</div>
	<center>
		<br />
		<div id="client_name" style="font-size:24px;font-weight:bold;"><?php echo $client_details[0]['client_name']; ?></div>
		<br />
        <div id="addSociety">        	
        	<div style="font-size:22px;">Add User</div>
           	<br /><br />           				
            <table style="border: 1px solid black; padding:20px;">
                <tr>
                    <th>Society</th>
                    <td> &nbsp; : &nbsp; </td>
                    <td><select name="societies" id="societies" onchange="getUnits(this);"> 
                    		<?php if($loginID > 0) { echo $combo_society = $objNewSociety->combobox("SELECT society_id, society_name FROM `society` WHERE `society_id` NOT IN ( SELECT DISTINCT s.society_id FROM `society` AS s INNER JOIN `mapping` AS m ON s.society_id = m.society_id WHERE m.login_id = '".$loginID."') and client_id = '" . $clientID . "' and status = 'Y' order by society_name ASC", 'society_id', 'Please Select', 0);} 
								else {  echo $combo_society = $objNewSociety->combobox("SELECT society_id, society_name FROM `society`", 'society_id', 'Please Select', 0); } ?>
                    	</select>
                   	</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td> &nbsp; : &nbsp; </td>
                    <td>
                        <select name="role" id="role" onchange="onRoleChange(this.value);" >   
                            <option value="<?php echo ROLE_MEMBER; ?>"><?php echo ROLE_MEMBER; ?></option>
                            <option value="<?php echo ROLE_ADMIN; ?>"><?php echo ROLE_ADMIN; ?></option>                               
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Unit</th>
                    <td> &nbsp; : &nbsp; </td>
                    <td>
                        <select name="unitID" id="unitID" >                                                        
                        </select>
                    </td>
                </tr>                    
                <tr> 
                    <td colspan="3" align="center" style="padding:10px;"><input type="submit" value="Submit" onclick="addUser();" />
                    <a href="" id="addUser"></a>
                    <input type="hidden" id="loginID" name="loginID" value="<?php echo $loginID; ?>" />
                    </td>
                </tr>
            </table>
            <script>
                    onRoleChange('<?php echo $_REQUEST['role']; ?>');
                    function onRoleChange(role)
                    {												
                        document.getElementById('role').value = role;
                        if(role == '<?php echo ROLE_MEMBER; ?>' || role == '')
                        {
                            document.getElementById('unitID').disabled = false;
                        }
                        else
                        {								
                            document.getElementById('unitID').value = 0;
                            document.getElementById('unitID').disabled = true;
                        }							
                    }
            </script>           
        </div>
        </center>
	</div>

<?php include_once "includes/foot.php"; ?>
           