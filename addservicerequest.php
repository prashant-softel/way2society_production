<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add New Service Request</title>
</head>

<?php 
	include_once("includes/head_s.php"); 
	include_once("classes/servicerequest.class.php");
	include_once("classes/dbconst.class.php");
	$obj_servicerequest = new servicerequest($m_dbConn,$m_dbConnRoot);
	//$details = $obj_servicerequest->getDetails();
	  //print_r($_SESSION);
    //  $loginID = $_SESSION["login_id"];

    if(isset($_REQUEST['edit']))
    {
	if($_REQUEST['edit']<>"")
	{ //echo "ho";
		$details = $obj_request->getViewDetails($_REQUEST['edit']);
		//for($i=0;$i <= sizeof($edit)-1; $i++)
			//{ 
			//print_r($details);
			$image=$details[0]['img'];
			$image_collection = explode(',', $image);	
			//print_r($image_collection);
			
	}
	}

	$MemberDetails = $obj_servicerequest->m_objUtility->GetMemberPersonalDetails($_SESSION["unit_id"]);
	
	$UnitBlock = $_SESSION["unit_blocked"];
	$MemberUnitNo = $obj_servicerequest->m_objUtility->GetUnitNo($_SESSION["unit_id"]);
	$MemberUnitNoForDD = $obj_servicerequest->m_objUtility->GetUnitNoForDD();

	$LoginDetails = $obj_servicerequest->m_objUtility->GetMyLoginDetails();
    //print_r($LoginDetails);
    $loginEmailID = $LoginDetails[0]["member_id"];
?>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/jsServiceRequest_26072018.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "dd-mm-yy", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true 
	})});
</script>
<script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
	function getTenantStatus(unitNo)
	{
		var result = false;
		var tenantData = [];
		$.ajax
		({
			url : "ajax/ajaxservicerequest.php",
			type : "POST",
			dataType : "json",
			data: {"method" : "checkTenantStatus", "unitId":unitNo},
			success: function(data)
			{
				//alert(data);
				tenantData = data;
				console.log("data : ",data);
				if(data['tenantStatus'] == "1")
				{
					document.getElementById('error').style.display = "";
					document.getElementById('error').innerHTML = "Active tenant Exits to the selected Unit No. <a href='tenant.php?mem_id="+data['memberId']+"&tik_id=<?php echo time();?>&edit="+data['tenantId']+"&ter&sr' target = '_self'>Click here</a> to terminate exisiting lease.";	
			//go_error();
				}
				else
				{
					document.getElementById('error').style.display = "";
					result = true;
				}
			},
			complete: function (data) 
			{
      			return(result); 
     		}
		});
		return (result);
	}
	function validate()
	{
		var email = trim(document.getElementById('email').value);
		var priority = trim(document.getElementById('priority').value);
		var category = trim(document.getElementById('category').value);
		var summery = trim(document.getElementById('summery').value);
		//var details = trim(document.getElementById('details').value);
		var unitNo = document.getElementById("unit_no2").value;
		//alert("unitNo :"+unitNo);
		var details = CKEDITOR.instances['details'].getData();
		//document.getElementById('insert').disabled=true;
		var role = "<?php echo $_SESSION['role'];?>";
		if(role != "<?php echo ROLE_MEMBER;?>")
		{
			if(unitNo == "0")
			{
				document.getElementById('error').style.display = "";
				document.getElementById('error').innerHTML = "Please Select Unit No.";	
				document.getElementById('unit_no').focus();
				go_error();
				return false;
			}
		}
		if(email == "")
		{
			document.getElementById('error').style.display = "";
			document.getElementById('error').innerHTML = "Please Enter Valid Email ID";	
			document.getElementById('email').focus();
			go_error();
			return false;
		}
		
		if(priority == 0)
		{
			document.getElementById('error').style.display = "";
			document.getElementById('error').innerHTML = "Please Select Priority";
			document.getElementById('priority').focus();
			go_error();
			return false;
		}
		
		if(category == 0)
		{
			document.getElementById('error').style.display = "";
			document.getElementById('error').innerHTML = "Please Select Category";
			document.getElementById('category').focus();
			go_error();
			return false;
		}
		
		//alert(category);
		if("<?php echo $_SESSION['society_id'];?>" == 288) //For Shree Marigold
		{
			if(category == 21 || category == 22 || category == 23) //For maid, cook and driver
			{				
				//make attachment mandatory
				var attachmentFile = trim(document.getElementById('img').value);
				//alert(attachmentFile);
				if(attachmentFile == "")
				{
					document.getElementById('error').style.display = "";
					document.getElementById('error').innerHTML = "Please attach Aadhar card";
					document.getElementById('category').focus();
					go_error();
					return false;					
				}
			}
		}  
		
		if(summery == "")
		{
			document.getElementById('error').style.display = "";
			document.getElementById('error').innerHTML = "Please Enter Title of SR";	
			document.getElementById('summery').focus();
			go_error();
			return false;
		}
		//alert(category);
		if(category != "<?php echo $_SESSION['RENOVATION_DOC_ID'];?>" && category != "<?php echo $_SESSION['TENANT_REQUEST_ID']?>" && category != "<?php echo $_SESSION['ADDRESS_PROOF_ID']?>") <!--Vaishali--> 
		{
			if(details == "")
			{		
				document.getElementById('error').style.display = "";
				document.getElementById('error').innerHTML = "Please Enter Request Details";
				document.getElementById('details').focus();
				go_error();
				return false;	
			}
		}
		$('input[type=submit]').click(function(){
		$(this).attr('disabled', 'disabled');
	});
		///////////////////////////////////////////////////////////////////////////	
		function LTrim( value )
		{
		var re = /\s*((\S+\s*)*)/;
		return value.replace(re, "$1");
		}
		function RTrim( value )
		{
		var re = /((\s*\S+)*)\s*/;
		return value.replace(re, "$1");
		}
		function trim( value )
		{
		return LTrim(RTrim(value));
		}
		///////////////////////////////////////////////////////////////////////////	
	}
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	var isblocked = '<?php echo $UnitBlock ?>';
    //alert(isblocked);
    //alert("Test");
    if(isblocked==1)
    {
        //alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
        window.location.href='suspend.php';
    }
    $(document).ready(function()
	{
		var role = "<?php echo $_SESSION['role'];?>";
		var iUnitID = 0;
		if(role == "<?php echo ROLE_MEMBER?>")
		{
			iUnitID = "<?php echo $_SESSION['unit_id'];?>";
			document.getElementById("unit_no").value = iUnitID;
			document.getElementById("unit_no2").value = iUnitID;
			//document.getElementById("unit_no2").disabled = true;    // comment out in 19-09-2019 
		}
		else
		{
			document.getElementById("unit_no2").value = iUnitID;
			document.getElementById("unit_no").value = iUnitID;
		}
	});
	
	
	function AcceptTnC(value)
	{
		//alert(value);
		if(value == true)
		{
			document.getElementById("insert").disabled = false; 
		}
		else
		{
			document.getElementById("insert").disabled = true; 
		}
	}
	
	function goToRenovationRequest(value)
	{
		//console.log("value",value);
		//alert(value);
		
		document.getElementById("tnc_accept").style.display = "none";
		document.getElementById("Maid_TnC").style.display = "none";
//alert(image_collection);
				//var attachmentFile = trim(document.getElementById("uploadTd5").value);
				//alert(attachmentFile);

		if("<?php echo $_SESSION['society_id'];?>" == 288) //For Shree Marigold
		{
			if(value == 21 || value == 22 || value == 23) //For maid, cook and driver
			{				
				document.getElementById("Maid_TnC").style.display = "contents";
				document.getElementById("insert").disabled = true; 
				document.getElementById("tnc_accept").style.display = "inline";
				document.getElementById("tnc_accept").disabled = false;
				var val = CKEDITOR.instances['details'].getData();					
				if(!(val.length > 0))
				{ 				
					var msgText = 'Note : Please read and accept terms and conditions before you submit the service request in following format.<BR><BR>-----------------------------------------<BR><BR>I want to request you to approve entry of my following service provider. <BR><BR>Name of the person (service provider) : <br /> <br /> Aadhar Card Number :<br /> Please attach softcopy of Aadhar card<br /> <br /> Timing of visit, or 12/24 hrs : <br /> <br /> Date of start of visit :  <br /> <br /> Note : <BR>';						
					CKEDITOR.instances['details'].setData(msgText);	
				}
			}
		}


		if(value == "<?php echo $_SESSION['RENOVATION_DOC_ID'];?>" || value == "<?php echo $_SESSION['TENANT_REQUEST_ID'];?>" || value == "<?php echo $_SESSION['ADDRESS_PROOF_ID'];?>") <!--Vaishali--> 
		{
			document.getElementById("priority").value = "Medium";
			document.getElementById("detailsTr").style.display = "none";
			document.getElementById("insert").value = "Next";
			document.getElementById("uploadTd1").style.display = "none";
			document.getElementById("uploadTd2").style.display = "none";
			document.getElementById("uploadTd3").style.display = "none";
			document.getElementById("uploadTd4").style.display = "none";
			document.getElementById("uploadTd5").style.display = "none";
		}//
		else
		{
			document.getElementById("detailsTr").style.display = "table-row";	
			document.getElementById("insert").value = "Submit";
			document.getElementById("uploadTd1").style.display = "table-cell";
			document.getElementById("uploadTd2").style.display = "table-cell";
			document.getElementById("uploadTd3").style.display = "table-cell";
			document.getElementById("uploadTd4").style.display = "table-cell";
			document.getElementById("uploadTd5").style.display = "table-cell";
		}
	}
</script>

<?php if(isset($_POST["ShowData"])){?>
<body onLoad="go_error();">
<?php } ?>
<br><br>
<div class="panel panel-info" id="panel" style="width:76%;display:block;margin-left: 1%;">
<div class="panel-heading" style="font-size:20px;text-align:center;">
     Create New Service Request
</div>
<br />
<?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN))
		{
			$Url = "servicerequest.php?type=open";
		}
		else
		{
			$Url = "servicerequest.php?type=createdme";
		} ?>
<center>
    <button type="button" class="btn btn-primary" onClick="window.location.href='<?php echo $Url;?>'">Go Back</button>
</center>
<br>
<center>
<form name="addrequest" id="addrequest" method="post" action="process/servicerequest.process.php" enctype="multipart/form-data" onSubmit="return validate(); ">
<?php $star = "<font color='#FF0000'>*&nbsp;</font>";?>
<table align='center'>
 <input type="hidden" id="request_no" name="request_no" value="">
	<?php
		if(isset($_POST["ShowData"]))
			{
	?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
	<?php   }
			else
			{?>
    			<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
          <?php } ?>               
               
    <tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Created By </b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" name="reported_by" id="reported_by" value="<?php echo $_SESSION["name"];?>"</td>
        
        <td>&nbsp; &nbsp; &nbsp;</td>
        
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Created for Unit No. </b></th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        <input type = "hidden" id = "unit_no" name = "unit_no" value = "0"/> 
        <select id="unit_no2" name="unit_no2" value="<?php echo $_SESSION['unit_id'];?>"> 
            	<?php echo $obj_servicerequest->comboboxEx("SELECT u.unit_id, concat_ws(' - ', u.`unit_no`, m.`owner_name`) FROM `member_main` as m join `unit` as u on u.`unit_id` = m.`unit`",$_SESSION['unit_id']); ?>
                
            </select>
            </td>
	</tr>
    <tr><td colspan="4"><input type="hidden" name="reportedby" id="reportedby" value="<?php echo $_SESSION['name'];?>"> </td></tr>
    
    <!--<tr>
    	<td valign="middle"><?php //echo $star;?></td>
        <th><b>Created for Unit No. :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><select id="unit_no" name="unit_no" > 
            	<?php //echo $obj_servicerequest->comboboxEx("SELECT u.unit_id, concat_ws(' - ', u.`unit_no`, m.`primary_owner_name`) FROM `member_main` as m join `unit` as u on u.`unit_id` = m.`unit`"); ?>
                
            </select>
            </td>
    </tr>-->
    <tr><td><br></td></tr>
    <!--<tr align="left">
        	<td valign="middle"><?php// echo $star;?></td>
			<th><b>Date Of Complaint Raised</b></th>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="complaint_date" id="complaint_date" value="<?php// echo date('d-m-Y');?>" class="basics" size="10" readonly  style="width:80px;"/></td>
	</tr> 
-->    
		<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Email </b></th>
        <td>&nbsp; : &nbsp;</td>
        
        <?php 
        if($MemberDetails[0]['email']<>'')
		{?>
      <td>  <input type="text" name="email" value="<?php echo $MemberDetails[0]['email'];?>" id="email" /></td>
      <?php }
	  else
	  {?>
       <td><input type="text" name="email" value="<?php echo $loginEmailID;?>" id="email" /></td>
      <?php }?>
      <td>&nbsp; &nbsp; &nbsp;</td>
      <td valign="middle"><?php //echo $star;?></td>
        <th><b>Phone </b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" name="phone" value="<?php if($MemberDetails[0]['mob'] == "") { echo "0"; } else { echo $MemberDetails[0]['mob'];}?>" id="phone" onKeyPress="return blockNonNumbers(this, event, true, true);"/></td>
	</tr>   
    <tr><td><br></td></tr>
    <!--<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Phone :</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="text" name="phone" value="<?php echo $MemberDetails[0]['mob'];?>" id="phone" onKeyPress="return blockNonNumbers(this, event, true, true);"/></td>
	</tr>  -->                   
    <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Priority</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="priority" name="priority">
            	<option value="0"> Please Select </option>
                <option value="Low">Low </option>
                <option value="Medium">Medium </option>
                <option value="High">High </option>
            </select>
        </td>
        <td>&nbsp; &nbsp; &nbsp;</td>
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Category</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="category" name="category" onChange="goToRenovationRequest(this.value)"> 
            	<?php echo $combo_category = $obj_servicerequest->combobox("SELECT `id`, `category` FROM `servicerequest_category` WHERE `status` = 'Y'", 0); ?>
            </select>
        </td>
	</tr>
    <tr><td><br></td></tr>
  	<!--<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Category</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="category" name="category" onChange="getEmailID(this.value);"> 
            	<?php echo $combo_category = $obj_servicerequest->combobox("SELECT `id`, `category` FROM `servicerequest_category` WHERE `status` = 'Y'", 0); ?>
                
            </select>
        </td>
	</tr>-->
    <tr id="upload"> 
        <td valign="top"><?php echo $star;?></td>
        <td><b>Title</b></td>   
        <td>&nbsp; : &nbsp;</td>
        <td><textarea name="summery" id="summery" rows="2" cols="50" style="max-width:10;"></textarea></td>
      	<td id = "uploadTd1">&nbsp; &nbsp; &nbsp;</td>
       	<td valign="left" id = "uploadTd2"></td>
        <td id = "uploadTd3"><b>Upload Image</b></td>
        <td id = "uploadTd4">&nbsp; : &nbsp;</td>
        <td id = "uploadTd5"><input  style=" width: 200px;" name="img[]" id="img" type="file" accept=".jpg, .png, .jpeg, .gif" multiple /></td>
       	<?php 
		for($i=0;$i<sizeof($image_collection);$i++)
		{ 
		//var_dump($image_collection);
			if(strlen($image_collection[$i]) >0 )
			{ //echo "hello" .$image_collection[$i];
		?>
        
		<a href="upload/main/<?php echo $image_collection[$i];?>"><img  style="width:50px; height:35px;" src="upload/main/<?php echo $image_collection[$i]?>"></a><a href="javascript:void(0);" onClick="del_photo('<?php echo $image_collection[$i];?>',<?php echo $_REQUEST['edit']?>);"><img style="width:15px;margin-top:-30px; margin-left: -10px;" src="images/del.gif" /></a>
      <?php
	 	 	 }
		}
		?>
       </tr>
       <tr><td><br></td></tr>
       
    <!--<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <td><b>Summary</b></td>
        <td>&nbsp; : &nbsp;</td>
        <td><textarea name="summery" id="summery" rows="2" cols="50" style="max-width:10;"></textarea></td>
    </tr>-->
   
    <tr align="left" id="Maid_TnC" style="display:none">
    	<td valign="left"><?php echo $star;?></td>
    	<td style="text-align:left;"><b>Terms and Conditions</b></td>
   		<td>&nbsp; : &nbsp;</td>
        <td><input type="checkbox" name="tnc_accept" id="tnc_accept" value="1" onChange="AcceptTnC(this.checked);">&nbsp;&nbsp;<b>I accept  <a href='docs\maid_tnc.pdf' target = '_blank'>terms and conditions</a></b></td>
        
    </tr>

    <tr><td>&nbsp; : &nbsp;</td></tr>
    <tr align="left" id="detailsTr">
    	<td valign="left"><?php echo $star;?></td>
    	<td style="text-align:left;"><b>Request Details</b></td>
   		<td>&nbsp; : &nbsp;</td>
        <td colspan="6"><textarea name="details" id="details" rows="5" cols="50"></textarea></td>
    </tr>
    <!--<tr><td colspan="4"><textarea name="details" id="details" rows="5" cols="50"></textarea></td></tr>-->
       	<script>			
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('details', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 350,
        						 width: 500,
								 uiColor: '#14B8C4'
								 });
		</script>
            <tr><td colspan="4"><input type="hidden" name="request_id" id="request_id" value="<?php //echo $_SESSION['name'];?>"> </td></tr>
                                           	  
    <tr><td colspan="4">&nbsp;<input type="hidden" name="memberemail" id="memberemail" /><input type="hidden" name="email1" id="email1" /> </td></tr>    
    <tr><td><br></td></tr>
  <?php /*?>  <?php
	if(isset($_REQUEST['rq']) && $_REQUEST['rq'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['rq']; ?>" />
    <?php	
	}
	else if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['deleteid']; ?>" />
	<?php	
	} ?><?php */?>
    <tr>
		<td colspan="10" align="center"><input type="submit" name="insert" id="insert" class="btn btn-primary" value="Submit" style="width: 150px; height: 30px; background-color: #337ab7; color:#FFF"; ></td>
    </tr>
    <tr><td><br></td></tr>
</table> 
</form>
</center>
</div>
</body>
<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getService('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getService('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>
<?php include_once "includes/foot.php"; ?>