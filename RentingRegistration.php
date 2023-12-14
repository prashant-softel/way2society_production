<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Renting Registration</title>
</head>

<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/rentingRegistration.class.php");
$star = "<font color='#FF0000'>*</font>";

$obj_rentingRegistration = new rentingRegistration($m_dbConn,$m_dbConnRoot);
if($_REQUEST["View"] == "ADMIN")
{
	$width = "100%";
}
else if($_REQUEST["View"] == "MEMBER")
{
	$width = "77%";
}
else
{
	$width = "100%";
}
$pDetails = $obj_rentingRegistration->getProfession();
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
//echo "xyz";
?>
<html>
  <head>
    <title>RentingRegistration</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/jsRentingRegistration.js"></script>
   	  <script type="text/javascript">
	  		 function changeRentType(str)
			 {
				 //alert(str);
				 if(str == "Fixed Rent")
				 {
					 document.getElementById("fixedRent").style.display = "table-row";
					 document.getElementById("varyingRent").style.display = "none";
				 }
				  if(str == "Varying Rent")
				 {
					 document.getElementById("varyingRent").style.display = "table-row";
					 document.getElementById("fixedRent").style.display = "none";
				 }
			 }
	  		$(function()
        	{
				$.datepicker.setDefaults($.datepicker.regional['']);
				$(".basics").datepicker
				({ 
					dateFormat: "dd-mm-yy", 
					showOn: "both", 
					buttonImage: "images/calendar.gif", 
					buttonImageOnly: true 
        		})
			});
			$(function()
        	{
            	$.datepicker.setDefaults($.datepicker.regional['']);
            	$(".basics_Dob").datepicker(datePickerOptions)
			});
  			$( document ).ready(function()
			{
    			var unitId=document.getElementById("unitId").value;
				var type=document.getElementById("type").value;
				var action="<?php echo $_REQUEST['action']?>";
				var tenantId = "<?php echo $_REQUEST['tId'];?>";
				//alert ("unitId:"+unitId);
				if(unitId!=0 && type=="owner")
				{
					$.ajax
					({
						url : "ajax/rentingRegistration.ajax.php",
						type : "POST",
						datatype: "JSON",
						data : {"method":"getOwnerDetails","unitId":unitId},
						success : function(data)
						{
							//alert ("Data:"+data);	
							var ownerDetails=Array();
							
							ownerDetails=JSON.parse(data);
							var i;
							var id=0;
							var floorNo = String(ownerDetails['response']['OwnerDetails']['0']['floor_no']);
							//alert ("floorNo :"+floorNo);
							var lastChar = floorNo[floorNo.length -1];
							var ff = "th";
							if(lastChar == "1")
							{
								ff = "st";
							}
							else if (lastChar == "2")
							{
								ff = "nd";
							}
							else if (lastChar == "3")
							{
								ff = "rd";
							}
							else
							{
								ff = "th";
							}
							//alert (ff);
							document.getElementById("FName").value=ownerDetails['response']['OwnerDetails']['0']['FirstName'];
							document.getElementById("LName").value=ownerDetails['response']['OwnerDetails']['0']['LastName'];
							document.getElementById("dob").value=ownerDetails['response']['OwnerDetails']['0']['dob'];
							//alert (":"+ownerDetails['response']['OwnerDetails']['0']['dob']);
							document.getElementById("Address1").value=ownerDetails['response']['OwnerDetails']['0']['unit_no']+" / "+ownerDetails['response']['OwnerDetails']['0']['wing']+", "+ownerDetails['response']['OwnerDetails']['0']['floor_no']+" "+ff+" floor, "+ownerDetails['response']['OwnerDetails']['0']['society_name'];
							document.getElementById("Address2").value=ownerDetails['response']['OwnerDetails']['0']['landmark']+", "+ownerDetails['response']['OwnerDetails']['0']['society_add']
							document.getElementById("city").value=ownerDetails['response']['OwnerDetails']['0']['city'];
							document.getElementById("Pincode").value=ownerDetails['response']['OwnerDetails']['0']['postalCode'];
							if(ownerDetails['response']['OwnerDetails']['0']['NoOfOwner'] > 1)
							{
								document.getElementById("NoOwner").checked = "true";
							}
							else
							{
								document.getElementById("NoOwner").checked = "false";
							}
							document.getElementById("memberId").value = ownerDetails['response']['OwnerDetails']['0']['member_id'];
						}
					});
				}
				if(unitId!=0 && type=="tenant" && tenantId > 0)
				{
					tenantId = tenantId.trim();
					$.ajax
					({
						url : "ajax/rentingRegistration.ajax.php",
						type : "POST",
						datatype: "JSON",
						data : {"method":"getTenantDetails","unitId":unitId,"tenantId":tenantId},
						success : function(data)
						{
							//alert ("Data:"+data);	
							var tenantDetails=Array();
							
							tenantDetails=JSON.parse(data);
							//alert("Owner Details:"+tenantDetails['response']['Tenant']['0']['FirstName']);
							var i;
							var id=0;
							document.getElementById("TFName").value = tenantDetails['response']['Tenant']['0']['tenant_name'];
							document.getElementById("TMName").value = tenantDetails['response']['Tenant']['0']['tenant_MName'];
							document.getElementById("TLName").value = tenantDetails['response']['Tenant']['0']['tenant_LName'];
							document.getElementById("members_1").value = tenantDetails['response']['Tenant']['0']['tenant_name']+" "+tenantDetails['response']['Tenant']['0']['tenant_MName']+" "+tenantDetails['response']['Tenant']['0']['tenant_LName'];
							document.getElementById("TProfession").selected = tenantDetails['response']['Tenant']['0']['Profession'];
							document.getElementById("TAddress1").value = tenantDetails['response']['Tenant']['0']['Address1'];
							document.getElementById("TAddress2").value = tenantDetails['response']['Tenant']['0']['Address2'];
							document.getElementById("TCity").value = tenantDetails['response']['Tenant']['0']['City'];
							document.getElementById("TPincode").value = tenantDetails['response']['Tenant']['0']['Pincode'];
							document.getElementById("TAdhaarNo").value = tenantDetails['response']['Tenant']['0']['AdhaarCard_CINNo'];
							document.getElementById("mem_dob_1").value = tenantDetails['response']['Tenant']['0']['dob'];
							document.getElementById("relation_1").value = "Self";
							document.getElementById("contact_1").value = tenantDetails['response']['Tenant']['0']['mobile_no'];
							document.getElementById("email_1").value = tenantDetails['response']['Tenant']['0']['email'];
							if(tenantDetails['response']['Tenant']['0']['members'] > 1)
							{
								document.getElementById("NoTenant").checked = "true";
							}
							else
							{
								document.getElementById("NoTenant").checked = "false";
							}
							var member = parseInt(tenantDetails['response']['Tenant']['0']['members']);
							var i=2;
							var scontent = "";
							$.ajax
							({
								url : "ajax/rentingRegistration.ajax.php",
								type : "POST",
								datatype: "JSON",
								data : {"method":"getTenantMemberDetails","tenantId":tenantId},
								success : function(data1)
								{
									//alert ("Data:"+data1);	
									var tenantDetails=Array();
									tenantDetails=JSON.parse(data1);
									var j = 0;
									for(i=2;i<=member;i++)
									{
										scontent = "<td id='members_td_"+i+"'><input type='hidden' name='member_id_"+i+"' id='member_id_"+i+"' value='"+tenantDetails['response']['Tenant'][j]['tmember_id']+"'><input name = 'members_"+i+"' id = 'members_"+i+"' type='text' value = '"+tenantDetails['response']['Tenant'][j]['mem_name']+"' style='width:150px;'/></td>&nbsp;<td id='relation_td_"+i+"'><input name = 'relation_"+i+"' id = 'relation_"+i+"' type='text' value = '"+tenantDetails['response']['Tenant'][j]['relation']+"'  style='width:80px;'/></td>&nbsp;&nbsp;<td id='mem_dob_td_"+i+"'><input name = 'mem_dob_"+i+"' id = 'mem_dob_"+i+"' class='basics' type='text' value = '"+tenantDetails['response']['Tenant'][j]['mem_dob']+"' size='10' style='width:80px;'/></td><td id='contact_td_"+i+"'><input name = 'contact_"+i+"' id = 'contact_"+i+"' type='text' value = '"+tenantDetails['response']['Tenant'][j]['contact_no']+"' style='width:80px;'/></td><td id='email_td_"+i+"'><input name = 'email_"+i+"' id = 'email_"+i+"' type='text' value = '"+tenantDetails['response']['Tenant'][j]['email']+"' style='width:140px;'/>&nbsp;</td><td></td><td></td>";
										document.getElementById('tenantCount').value=i;
										var div = $("<tr style='text-align:center;' />");
										div.html(scontent);
										$("#mem_table").append(div);
										j = j+1;
									}
									for(i=2;i<=member;i++)
									{
										document.getElementById("mem_dob_"+i).className = "basics_Dob";
									}
									document.getElementById("btnSubmitTenant").value = "Update Tenant";
								}
							});
						}
					});
				}
				if(unitId!=0 && type=="agreementTerms" && action == "edt")
				{
					var tenantId = "<?php echo $_REQUEST['tId'];?>";
					$.ajax
					({
						url : "ajax/rentingRegistration.ajax.php",
						type : "POST",
						datatype: "JSON",
						data : {"method":"getAgreementdetails","tenantModuleId":tenantId},
						success : function(data)
						{
							//alert ("Data:"+data);	
							var ownerDetails=Array();
							
							ownerDetails=JSON.parse(data);
							//alert("Owner Details:"+ownerDetails['response']['OwnerDetails']['0']['FirstName']);
							var i;
							var id=0;
							document.getElementById("pFromDate").value=ownerDetails['response']['OwnerDetails']['0']['StartDate'];
							document.getElementById("pregion").value=ownerDetails['response']['OwnerDetails']['0']['region'];
							document.getElementById("pcity").value=ownerDetails['response']['OwnerDetails']['0']['city'];
							document.getElementById("pPincode").value=ownerDetails['response']['OwnerDetails']['0']['postal_code'];
							//alert (ownerDetails['response']['OwnerDetails']['0']['EndDate']);
							if(ownerDetails['response']['OwnerDetails']['0']['EndDate'] == "undefined")
							{
								document.getElementById("pToDate").value="";
							}
							else
							{
								document.getElementById("pToDate").value=ownerDetails['response']['OwnerDetails']['0']['EndDate'];
							}
							if(ownerDetails['response']['OwnerDetails']['0']['StartDate']=="undefined")
							{
								document.getElementById("pFromDate").value="";
							}
							else
							{
								document.getElementById("pFromDate").value=ownerDetails['response']['OwnerDetails']['0']['StartDate'];
							}
						}
					});
				}
			});
			var datePickerOptions={ 
				dateFormat: "dd-mm-yy", 
				showOn: "both", 
				buttonImage: "images/calendar.gif", 
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
				buttonImageOnly: true ,
				defaultDate: '01-01-1980'
        	};
			var FieldCount=1;
			var MaxInputs=5;
			function getDyanamicTextBox()
			{
				if(FieldCount <= MaxInputs) //max file box allowed
               	{
					FieldCount++; 
					document.getElementById('tenantCount').value=FieldCount;
				}
				var div = $("<tr style='text-align:center;' />");
				div.html(GetDynamicTextBox(""));
				$("#mem_table").append(div);
				$(".basics").datepicker(datePickerOptions);
			}
			function GetDynamicTextBox(value) {
    			return '<td id="members_td_'+FieldCount+'"><input name = "members_'+FieldCount+'" id = "members_'+FieldCount+'" type="text" value = "' + value + '"   style="width:150px;" /></td>&nbsp;<td id="relation_td_'+FieldCount+'"><input name = "relation_'+FieldCount+'" id = "relation_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td>&nbsp;&nbsp;'+'<td id="mem_dob_td_'+FieldCount+'"><input name = "mem_dob_'+FieldCount+'" id = "mem_dob_'+FieldCount+'"  class="basics" type="text" value = "' + value + '" size="10"   style="width:80px;" /></td><td id="contact_td_'+FieldCount+'"><input name = "contact_'+FieldCount+'" id = "contact_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td><td id="email_td_'+FieldCount+'"><input name = "email_'+FieldCount+'" id = "email_'+FieldCount+'" type="text" value = "' + value + '"  style="width:140px;"  />&nbsp;</td><td></td><td></td>';
}
			var DocCount=1;
			var MaxInputs=10;
			$(function () 
			{
				$("#btnAddDoc").bind("click", function () 
				{
		//	a	lert("Add");
					if(FieldCount <= MaxInputs) //max file box allowed
            		{
						DocCount++; 
						document.getElementById('doc_count').value=DocCount;
					}
	    			var div = $("<tr />");
        			div.html(GetDynamicFileBox(""));
        			$("#tblDocument").append(div);
				});
		//$(".basics_Dob").datepicker(datePickerOptions);
			});
			function GetDynamicFileBox(value) {
    return '<td><input name = "userfile'+DocCount+'" id = "userfile'+DocCount+'" type="file" value = "' + value + '" /></td>'+'<td><input name = "doc_name_'+DocCount+'" id = "doc_name_'+DocCount+'" type="text" value = "' + value + '" /></td>'+
            '<!--<input type="button" value="Remove" class="remove" />-->'
}
    </script>
      <style>
        

/* Style the tab */
		.tab 
		{
			float:left;
    		border: 1px solid #ccc;
			background-color:#337ab7;
		}

/* Style the buttons inside the tab */
		.tab button 
		{
    		background-color: inherit;
    		float: left;
    		border: 1px thin white;
    		outline: none;
    		cursor: pointer;
    		padding: 14px 16px;
    		transition: 0.3s;
    		font-size: 50px;
		}

/* Change background color of buttons on hover */
		.tab button:hover
		{
    		background-color: #ddd;
			color:#000000;
		}

/* Create an active/current tablink class */
		.tab button.active
		{
    		background-color: #ccc;
		}

/* Style the tab content */
	.tabcontent
	{
    	display: none;
    	padding: 6px 12px;
    	border: 1px solid #ccc;
    	border-top: none;
	}
    </style>
  </head>
  <body>
    <center>
        <br>
        <div class="panel panel-info" id="panel" style="display:block;width:<?php echo $width;?>;float:left;margin-left:1%">
          <div class="panel-heading" id="pageheader" >Renting Registration</div>
            <form name="rentingRegistration" id="rentingRegistration" method="post" action="#">
              <br>
              <div style="position:absolute, top: 10px, right:10px"></div>
            	<div class="panel-body">
                	<div class="table-responsive">
  						<ul class="nav nav-tabs" role="tablist">
            				<li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "owner") ? 'class="active"' : ""; ?>> 
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='RentingRegistration.php?type=owner&unitId=<?php echo $_REQUEST['unitId'];?>&View=<?php echo $_REQUEST['View'];?>'"><b>Owner Details&nbsp;&nbsp;<i class="fa fa-chevron-circle-right
"></i>&nbsp;<i class="fa fa-chevron-circle-right"></i></b></a>
    						</li>
            				<li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "tenant") ? 'class="active"' : ""; ?>>
            					<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='RentingRegistration.php?type=tenant&unitId=<?php echo $_REQUEST['unitId'];?>&View=<?php echo $_REQUEST['View'];?>'"><b>Tenant Details&nbsp;&nbsp;<i class="fa fa-chevron-circle-right">&nbsp;</i><i class="fa fa-chevron-circle-right"></i></b></a>
    						</li>
                            <li <?php echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "agreementTerms") ? 'class="active"' : ""; ?>> 
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='RentingRegistration.php?type=agreementTerms&unitId=<?php echo $_REQUEST['unitId'];?>&View=<?php echo $_REQUEST['View'];?>'"><b>Agreement Terms&nbsp;&nbsp;
                                <i class="fa fa-chevron-circle-right"></i>&nbsp;<i class="fa fa-chevron-circle-right"></i></b></a>
    						</li>
                            	<!--<li <?php //echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "closed") ? 'class="active"' : ""; ?>> 
            					<a href="#profile" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=closed'">Minuted</a>
    						</li>
                            <li <?php // echo (isset($_REQUEST['type']) && $_REQUEST['type'] == "cancel") ? 'class="active"' : ""; ?> >
            					<a href="#home" role="tab" data-toggle="tab" onClick="window.location.href='meeting.php?type=cancel'">Cancelled</a>
    						</li>-->
        				</ul>
					</div>	
          	  </div>
          	</form>
            <div style="width:100%">
            	<input type="hidden" name="unitId" id="unitId" value="<?php echo $_REQUEST['unitId'];?>"/>
                <input type="hidden" name="type" id="type" value="<?php echo $_REQUEST['type'];?>"/>
               	<input type="hidden" name="view" id="view" value="<?php echo $_REQUEST['View'];?>"/>
                <input type="hidden" name="doc_count" id="doc_count" value="1">
                <input type="hidden" name="tenantId" id="tenantId" value="<?php echo $_REQUEST['tId'];?>">
            <?php
            if(isset($_REQUEST['type']) && $_REQUEST['type'] == "owner")
			{
				?>
				
                    	<table width="100%">
                        	<tr>
                            	<td style="text-align:right;width:10%;vertical-align:center;"><?php echo $star;?><b>Full Name &nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="85%" style="text-align:left">
                                	<input type="text" id="FName" placeholder="First Name"/>&nbsp;&nbsp;&nbsp;
                                    <input type="text" id="MName" placeholder="Middle Name"/>&nbsp;&nbsp;&nbsp;
                                    <input type="text" id="LName" placeholder="Last Name"/>&nbsp;&nbsp;&nbsp;
                                    <input type="hidden" id = "memberId" name = "memberId" value = ""/>
                                </td>     
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Date of Birth&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                               		<input type="text" id="dob" placeholder="Date of birth" class="basics_Dob"  style="width:15%"/>
                                   	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Gender&nbsp;:&nbsp;</b>
                                    <select id = "Gender" >
                                    	<option value="Male">MALE</option>
                                        <option value="Female">FEMALE</option>
                                        <option value="Other">OTHER</option>
                                    </select>
                                    <!--<input type="radio" name = "gender" id = "gender" value="male"> Male &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="female"> Female &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="other"> Other     -->
                            	</td>
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Profession&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<select id = "Profession" >
                                    	<?php
											for($i=0;$i<sizeof($pDetails);$i++)
											{
												?>
                                                <option value = "<?php echo $pDetails[$i]['Id'];?>"><?php echo $pDetails[$i]['Profession'];?></option>
                                                <?php
											}
										?>
                                    </select>
                            	</td>
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Owner's Address&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<textarea id = "Address1" name = "address1" placeholder="House/ Flat/ Shop No, Building Name,Floor No,Society." style="width:495px;height:46px" rows="3"></textarea>&nbsp;&nbsp;&nbsp;<br/>
                                    <textarea id = "Address2" name = "address2" placeholder="Street No, Street Name, Location" style="width:495px;height:46px;margin-top:1%" rows="3"></textarea>&nbsp;&nbsp;&nbsp;<br/>
                            	</td>
                                   
                        	</tr>
                            <tr>
                            	<tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>City&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<input type="text" id="city" placeholder="City" style="vertical-align:top;width:20%;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Pincode&nbsp;:&nbsp;</b> 
                                    <input type="text" id="Pincode" placeholder="Pincode" style="vertical-align:top;width:20%;"/>
                            	</td>
                            </tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<b>Power of Attorney holder is involved&nbsp;:&nbsp;</b>
                               		<input type="checkbox" id = "PowerOfAttorney" />&nbsp;<b>Yes</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
                                    <b>More than one owners exist. &nbsp;:&nbsp;</b><input type="checkbox" id = "NoOwner" />&nbsp;<b>Yes</b>
                                    <!--<input type="radio" name = "gender" id = "gender" value="male"> Male &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="female"> Female &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="other"> Other     -->
                            	</td>
                        	</tr>
                           <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="background-color:yellow;text-align:center">
                                	
                                	<b>*Note : Additional owner details will be collected later. All owners need to have Adhaar and PAN.</b>
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="text-align:center">
                                <input type="submit" id="btnSubmitOwner" name="btnSubmitOwner" value="Save & Continue" class="btn btn-primary" onClick="saveOwnersDetails()"/><input type="button" id="btnCancel" name="btnCancel" class="btn btn-primary" value="Cancel" style="width:10%;margin-left:10%"/><br/></td>
                               
                        	</tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                        </table>
                   
                    <?php
			}
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "tenant")
			{
				?>
                    	<table width="100%">
                        	<tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;"><?php echo $star;?><b>Full Name &nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="80%" style="text-align:left">
                                	<input type="text" id="TFName" placeholder="First Name"/>&nbsp;&nbsp;&nbsp;
                                    <input type="text" id="TMName" placeholder="Middle Name"/>&nbsp;&nbsp;&nbsp;
                                    <input type="text" id="TLName" placeholder="Last Name" onBlur="getFirstMemberName()"/>&nbsp;&nbsp;&nbsp;
                                </td>     
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%;"><b>Tenant Family members Details&nbsp;:&nbsp;</b>
                                <input type="hidden" id="tenantCount" name="tenantCount" value="1"/></td>
                            	<td colspan="3" style="padding-top:1%;text-align:left">
                                    <table width="95%" style="margin-left:5%" id="mem_table">                        
                                      <tr >
                                        <td width="15%" style="text-align:center"><b>&nbsp;&nbsp;Name</b></td>
                                        <td width="5%" style="text-align:center"><b>Relation</b></td>
                                        <td width="15%" style="text-align:center"><b><?php echo $star;?>Date Of Birth<br/>(DD-MM-YYYY)</b></td>
                                        <td width="8%" style="text-align:center"><b><?php echo $star;?>&nbsp;&nbsp;Contact No.</b></td>
                                        <td width="5%" style="text-align:center"><b><?php echo $star;?>Email Address</b></td>
                                        <td id="create_login" width="5%" style="text-align:center">Create Login</td>
                                        <td id="send_emails" width="5%" style="text-align:center">Send E-Mails ?</td>
                                        <td width="3%" style="text-align:center"></td>
                                      </tr>
                                      <tr align="center">
                                        <td id="members_td_1"><input type="text" name="members_1" id="members_1" style="width:150px;" /></td>
                                        <td id="relation_td_1"><input type="text" name="relation_1" id="relation_1"  style="width:80px;" /></td>
                                        <td id="mem_dob_td_1"><input type="text" name="mem_dob_1" id="mem_dob_1"   class="basics_Dob" size="10" style="width:80px;" /></td>
                                        <td id="contact_td_1"><input type="text" name="contact_1" id="contact_1"  style="width:80px;"  onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30" /></td>
                                        <td id="email_td_1"><input type="text" name="email_1" id="email_1" style="width:140px;" /></td>            
                                        <td style="text-align:center"><input type="checkbox"  name="chkCreateLogin" id="chkCreateLogin" value="1" /></td>
                                        <td><input type="checkbox" name="other_send_commu_emails" id="other_send_commu_emails" value="1" /></td>
                                        <td><a onClick="getDyanamicTextBox();"><i class="fa-plus-circle"></i></a></font></td>
                                      </tr>
                                  	</table>
                              	</td>
                            </tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Profession&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                               		<select id = "TProfession" style="width:30%">
                                    	<?php
											for($i=0;$i<sizeof($pDetails);$i++)
											{
												?>
                                                <option value = "<?php echo $pDetails[$i]['Id'];?>"><?php echo $pDetails[$i]['Profession'];?></option>
                                                <?php
											}
										?>   
                                    </select>
                                    <!--<input type="radio" name = "gender" id = "gender" value="male"> Male &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="female"> Female &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="other"> Other     -->
                            	</td>
                        	</tr>
                            <!--<tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Profession&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<select id = "TProfession" >
                                    	<option value="Student">Student</option>
                                        <option value="Service">Service</option>
                                        <option value="Business">Business</option>
                                        <option value="Housewife">Housewife</option>
                                        <option value="Agriculture">Agriculture</option>
                                        <option value="Retired Goverment Officer">Retired Goverment Officer</option>
                                        <option value="Other">Other</option>   
                                    </select>
                            	</td>
                        	</tr>-->
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Permnant Address&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<textarea id = "TAddress1" name = "TAddress1" placeholder="House/ Flat/ Shop No, Building Name,Floor No,Society." style="width:495px;height:46px" rows="3"></textarea>&nbsp;&nbsp;&nbsp;<br/>
                                    <textarea id = "TAddress2" name = "TAddress2" placeholder="Street No, Street Name, Location" style="width:495px;height:46px;margin-top:1%" rows="3"></textarea>&nbsp;&nbsp;&nbsp;
                            	</td>
                        	</tr>
                             <tr>
                            	<tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>City&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<input type="text" id="TCity" placeholder="City" style="vertical-align:top;width:20%;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Pincode&nbsp;:&nbsp;</b> 
                                    <input type="text" id="TPincode" placeholder="Pincode" style="vertical-align:top;width:20%;"/>
                            	</td>
                            </tr>
                            </tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Lease Documents&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<table id = "tblDocument" width="70%">
                                		<tr>
                                    		<td><b>Select file to upload</b></td>
                                        	<td><b>Enter document name</b></td>
                                        	<td></td>
                                    	</tr>
                                    	<tr>
                                    		<td><input type="file" name="userfile1" id="userfile1"/></td>
                                        	<td><input type="text" id="doc_name_1" name="doc_name_1"/></td>
                                        	<td><input id="btnAddDoc" type="button" value="Add More" /></td>
                                    	</tr>
                                	</table>
                            	</td>
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<b>Tenant is a Company and not individual&nbsp;:&nbsp;</b>
                               		<input type="checkbox" id = "TCompany" onChange="getAdhaarCINno()"/>&nbsp;<b>Yes</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;
                                    <b>More than one tenants to be mentioned in agreement. &nbsp;:&nbsp;</b><input type="checkbox" id = "NoTenant" onChange="getTenantName()"/>&nbsp;<b>Yes</b>
                                    <!--<input type="radio" name = "gender" id = "gender" value="male"> Male &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="female"> Female &nbsp;&nbsp;&nbsp;
  									<input type="radio" name = "gender" id = "gender" value="other"> Other     -->
                            	</td>
                        	</tr>
                            <tr id = "TAdhaar">
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Adhaar Card No&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<input type="text" id="TAdhaarNo" placeholder="Adhaar No" style="vertical-align:top;width:20%;"/>&nbsp;&nbsp;&nbsp;<br/>
                            	</td>
                        	</tr>
                             <tr id = "TCIN" style="display:none">
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>CIN No &nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<input type="text" id="TCINno" placeholder="CIN no" style="vertical-align:top;width:20%;"/>&nbsp;&nbsp;&nbsp;<br/>
                            	</td>
                        	</tr>
                           <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="background-color:yellow;text-align:center">
                                	
                                	<b>*Note : Additional owner details will be collected later.All owners need to have Adhaar and PAN.</b>
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="text-align:center">
                                <input type="submit" id="btnSubmitTenant" name="btnSubmitTenant" value="Save" class="btn btn-primary" onClick="saveTenantsDetails()"/><input type="button" id="btnCancel" name="btnCancel" class="btn btn-primary" value="Cancel" style="width:10%;margin-left:10%"/><br/></td>
                               
                        	</tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                        </table>
                  <?php 
			}	
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "agreementTerms")
			{
				?>
                		<table width="100%">
                        	<tr>
                            	<td style="text-align:right;width:10%;vertical-align:center;"><?php echo $star;?><b>Propety Type &nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="85%" style="text-align:left">
                                	<select id="propertyType" style="width:15%">
                                    	<option value="">Select</option>
                                    	<option value="Flat">Flat</option>
                                    	<option value="House">House</option>
                                    </select>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?><b>Property Use&nbsp;:&nbsp;</b>
                                    <select id="propertyUse" style="width:20%">
                                    	<option value="">Select</option>
                                    	<option value = "Residential">Residential</option>
                                        <option value = "Non-Residential">Non-Residential</option>
                                    </select>
                                </td>     
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><b>Property Address&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<textarea id = "pAddress1" name = "pAddress1" placeholder="House/ Flat/ Shop No, Building Name,Floor No,Society." style="width:495px;height:46px" rows="3"></textarea>&nbsp;&nbsp;&nbsp;<br/>
                                    <textarea id = "pAddress2" name = "pAddress2" placeholder="Street No, Street Name, Location" style="width:495px;height:46px;margin-top:1%" rows="3"></textarea>&nbsp;&nbsp;&nbsp;
                                   </td>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>City&nbsp;:&nbsp;</b></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                    <input type="text" id="pcity" name="pcity" value ="" placeholder="City" style="width:20%"/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?><b>Pincode&nbsp;:&nbsp;</b>
                                    <input type="text" id="pPincode" placeholder="Pincode" style="vertical-align:top;width:15%;"/>
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?><b>Region&nbsp;:&nbsp;</b>
                                    <input type="text" id="pregion" name="pregion" placeholder="Region Eg. Andheri (West)" style="width:20%"/> 

                            	</td>
                        	</tr>
                            <tr>
                            	<td style="text-align:right;width:15%;vertical-align:center;padding-top:1%"></td>
                            	<td style="width:5%;padding-top:1%"></td>
                           		<td width="80%" style="padding-top:1%;text-align:left">
                                	<b>Click here if property is in rural area&nbsp;&nbsp;</b>
                               		<input type="checkbox" id = "propertyArea" />&nbsp;<b>Yes</b>
                            	</td>
                        	</tr>
                           <!--<tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="background-color:yellow;text-align:center">
                                	
                                	<b>*Note : For Registration, the From date cannot be earlier than 4 months from today's date.</b>
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>-->
                            <tr>
                            	<td style="text-align:right;width:10%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Lease Start Date &nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="85%" style="text-align:left;padding-top:1%">
                                	<input type="text" name="pFromDate" id="pFromDate" class="basics" style="width:20%"/>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?><b>Lease End Date&nbsp;:&nbsp;</b>
                                    
                                    <input type="text" name="pToDate" id="pToDate" class="basics" style = "width:20%"/> 

                                </td>
                            </tr>
                            <tr>
                            	<td style="text-align:right;width:10%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Rent Type &nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="85%" style="text-align:left;padding-top:1%">
 									<input type="radio" id="rentType" name="rentType" value="Fixed Rent" onChange="changeRentType(this.value)"> <b>Fixed Rent</b>    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                            
                            
                                    <input type="radio" name="rentType" value="Varying Rent" id="rentType" onChange="changeRentType(this.value)"> <b>Varying Rent</b> 
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $star;?><b>Refundable Deposit(Rs.)&nbsp;:&nbsp;</b>
                                    <input type="text" id="deposit" name="deposit" placeholder="Rs." style="width:20%"/>
                                </td>
                            </tr>
                            <tr id="fixedRent" style="display:none">
                            	<td style="text-align:right;width:10%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Monthly Rent(Rs.)&nbsp;:&nbsp;</b></td>
                            	<td width="5%"></td>
                            	<td width="85%" style="text-align:left;padding-top:1%">
                                    <input type="text" id="monthlyRent" name="monthlyRent" placeholder="Rs." style="width:20%"/> 
                                                                        

                                </td>
                            </tr>
                            </tr>
                            <tr id="varyingRent" style="display:none">
                            	<td colspan="4" style="text-align:center;padding-top:1%">
                                	<center>
                                	<table width="40%">
                                    	<tr>
                                        	<th width="35%">From Month</th>
                                            <th width="35%">To Month</th>
                                            <th width="35%">Rent</th>
                                        </tr>
                                        <tr>
                                        	<td><input type="text" id="f1" name="f1" value="1" readonly style="width:45%"></td>
                                            <td><input type="text" id="t1" name="t1" style="width:45%" onBlur="getNextValue(this.value,this.id)"/></td>
                                            <td><input type="text" id="r1" name="r1" style="width:55%" placeholder="Rs."/></td>
                                        </tr>
                                        <tr>
                                        	<td><input type="text" id="f2" name="f2" style="width:45%" readonly></td>
                                            <td><input type="text" id="t2" name="t2" style="width:45%" onBlur="getNextValue(this.value,this.id)"/></td>
                                            <td><input type="text" id="r2" name="r2" style="width:55%" placeholder="Rs."/></td>
                                        </tr>
                                        <tr>
                                        	<td><input type="text" id="f3" name="f3" style="width:45%" readonly></td>
                                            <td><input type="text" id="t3" name="t3" style="width:45%" onBlur="getNextValue(this.value,this.id)"/></td>
                                            <td><input type="text" id="r3" name="r3" style="width:55%" placeholder="Rs."/></td>
                                        </tr>
                                        <!--<tr>
                                        	<td><input type="text" id="f4" name="f4" style="width:45%" readonly></td>
                                            <td><input type="text" id="t4" name="t4" style="width:45%" onBlur="getNextValue(this.value,this.id)"/></td>
                                            <td><input type="text" id="r4" name="r4" style="width:55%" placeholder="Rs."/></td>
                                        </tr>
                                        <tr>
                                        	<td><input type="text" id="f5" name="f5" style="width:45%" readonly></td>
                                            <td><input type="text" id="t5" name="t5" style="width:45%" onBlur="getNextValue(this.value,this.id)"/></td>
                                            <td><input type="text" id="r5" name="r5" style="width:55%" placeholder="Rs."/></td>
                                        </tr>-->
                                    </table>
                                    </center>
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                            <tr>
                            	
                            	<td colspan="4" style="text-align:center">
                                <input type="submit" id="btnSubmitAT" name="btnSubmitAT" value="Save & Continue" class="btn btn-primary" onClick="validateAgreementDetails()"/><input type="submit" id="btnSubmitDR" name="btnSubmitDR" value="Finalise" class="btn btn-primary" onClick="sendDetailsToDigitalRenting()" style="display:none;margin-left:10%"/><input type="button" id="btnCancel" name="btnCancel" class="btn btn-primary" value="Cancel" style="width:10%;margin-left:10%"/><br/></td>
                               
                        	</tr>
                            <tr>
                            	<td colspan="4"><br/></td>
                            </tr>
                        </table>
                <?php
			}
			if(isset($_REQUEST['type']) && $_REQUEST['type'] == "submit")
			{
				?>
                	<center>
                	<table width="100%">
                    	<tr>
                        	<td style="text-align:center"><b>Your response submitted successfully.</b></td>
                        </tr>
                    </table>
                    </center>
                <?php
            }
			?>
            </div>
      	</div>
<?php include_once "includes/foot.php"; ?>