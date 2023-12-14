<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Service Request Setting</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
  include_once("includes/header.php");
}
else
{
  include_once("includes/head_s.php");
}
?>
<?php
include_once("classes/utility.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/servicerequest.class.php");
include_once("classes/serviceRequestSetting.class.php");
$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
$obj_request = new servicerequest($m_dbConn);
$obj_serviceRequestSetting = new serviceRequestSetting($m_dbConn);
$star = "<font color='#FF0000'>*</font>";
$defaultDetails = $obj_serviceRequestSetting->getServiceRequestDefaults();
//var_dump($defaultDetails[2]);
		
/*echo "<pre>";
print_r($defaultDetails[0]['Renovation_terms_conditions']);
echo "</pre>";*/
?> 

<html>
  <head>
    <title>SettingPage</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/parkingType.js"></script>
   	  <script type="text/javascript">
       $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker
			({ 
            	dateFormat: "dd-mm-yy", 
            	showOn: "both", 
            	buttonImage: "images/calendar.gif", 
            	buttonImageOnly: true, 
				yearRange: '-10:+10', // Range of years to display in drop-down,
        	})
		});
		function collapsable(id) 
		{
			if(id == "1")
			{
				$("#renovationDetailsTable").toggle();
			}
			else if(id == "2")
			{
				$("#tenantReuqestDetails").toggle();
			}
			else
			{
				$("#addressProofDetailsTable").toggle();
			}
		}
		//var iRuleCnt = 1;
		var iWorkType = 1;
		/*function addRule()
		{
			iRuleCnt = iRuleCnt + 1;
			var sContent = "<tr><td><input type='text' id='rule"+iRuleCnt+"' name='rule[]' value='"+iRuleCnt+".' style='vertical-align:top;width:90%;size:10%'/></td></tr>";
				$("#rulesTable > tbody").append(sContent);
		}*/
		$(document).ready(function(e) {
            iWorkType = parseInt(document.getElementById("workTypeCount").value);
        });
		function addWorkType()
		{
			alert(iWorkType);
			iWorkType = iWorkType + 1;
			
			var sContent = "<tr id = 'workTypeTr_"+iWorkType+"'><td style='text-align:center;padding-top:1%;'><input type='text' id='workType"+iWorkType+"' name = 'workType[]' style='vertical-align:top;width:90%;size:10%'/></td><td style='text-align:left;padding-top:1%;padding-left:5%'><input type = 'checkbox' name = 'drawing[]' id = 'drawing"+iWorkType+"' class = 'drawing'/><b>&nbsp;Required&nbsp;&nbsp;</b><a id='del' onClick='deleteWorkType("+iWorkType+")' style='width:15%;height:120%;'><img src='images/del.gif' alt='Delete' style='width:15%;height:120%;'></img></a></td>";
			$("#workTypeTable > tbody").append(sContent);
			document.getElementById("workTypeCount").value = iWorkType;

		}
		function deleteWorkType(id)
		{
			var trId = "workTypeTr_"+id;
			document.getElementById(trId).style.display = "none";
		}
		function submitRenovationDefaults()
		{
			var categoryId = document.getElementById("renovationCategoryId").value;
			if( categoryId  == "0")
			{
				alert("Please select category Id for Renovation Request.").
				document.getElementById('renovationCategoryId').focus();
			}
			else
			{
				var approvalLevel = document.getElementById("renovationApprovalLevel").value;
				//var header = CKEDITOR.instances.renovationHeader.getData();
				var header = document.getElementById("renovationHeader").value;			
				header = header.trim().replace(/'/g,"/'");
				console.log("header : "+header);
				//alert ("header : "+header);
				//var footer = CKEDITOR.instances.renovationFooter.getData();
				var footer = document.getElementById("renovationFooter").value;			
				footer = footer.trim().replace(/'/g,"/'");
				console.log("footer : "+footer);
				//var termCond = CKEDITOR.instances.renovationTC.getData();
				var termCond = document.getElementById("renovationTC").value;			
				termCond = termCond.trim().replace(/'/g,"/'");
				console.log("termCond : "+termCond);
				//var thankYouNote = CKEDITOR.instances.RenovationThankyouNote.getData();
				var thankYouNote = document.getElementById("RenovationThankyouNote").value;			
				thankYouNote = thankYouNote.trim().replace(/'/g,"/'");
				console.log("thankYouNote : "+thankYouNote);
				var finalWorkListArray = [];
				var j = 0;
				var workTypeCnt = parseInt(document.getElementById("workTypeCount").value);
				for(var i = 1; i <= workTypeCnt;i++)
				{
					if(document.getElementById("workTypeTr_"+i).style.display != "none")
					{
						var workList = [];
						workList[j] = document.getElementById("workType"+i).value;
						j = j + 1;
						if(document.getElementById("drawing"+i).checked)
						{
							workList[j] = "Yes";
						}
						else
						{
							workList[j] = "No";
						}
						j = 0;
						//console.log("workList",workList);

						finalWorkListArray.push(workList);
						//console.log("finalWorkListArray",finalWorkListArray);
			
					}
				}
				//console.log("workList",workList);
				//console.log("finalWorkListArray",finalWorkListArray);
				$.ajax
				({
					url : "ajax/ajaxServiceRequestSetting.ajax.php",
					type : "POST",
					data: {"method": "SubmitRenovationDefaults", "categoryId":categoryId, "approvalLevel":approvalLevel, "header":header, "footer":footer, "thankYouNote":thankYouNote,"termsCondition":termCond,"workList":JSON.stringify(finalWorkListArray)},
					success: function(data)
					{
						alert ("Renovation Request Defaults are saved successfully.");
						window.location.reload();
					}
				});
			}
			
		}
		function submitTenantDefaults()
		{
			var categoryId = document.getElementById("tenantCategoryId").value;
			if( categoryId  == "0")
			{
				alert("Please select category Id for Leave & License NOC Request.").
				document.getElementById('tenantCategoryId').focus();
			}
			else
			{
				var approvalLevel = document.getElementById("tenantApprovalLevel").value;
				//var header = CKEDITOR.instances.tenantHeader.getData();
				var header = document.getElementById("tenantHeader").value;
				header = header.trim().replace(/'/g,"/'");
				console.log("header : "+header);
				//alert ("header : "+header);
				//var footer = CKEDITOR.instances.tenantFooter.getData();
				var footer = document.getElementById("tenantFooter").value;
				footer = footer.trim().replace(/'/g,"/'");
				console.log("footer : "+footer);
				//var thankYouNote = CKEDITOR.instances.tenantThankyouNote.getData();
				var thankYouNote = document.getElementById("tenantThankyouNote").value;			
				thankYouNote = thankYouNote.trim().replace(/'/g,"/'");
				console.log("thankYouNote : "+thankYouNote);
				$.ajax
				({
					url : "ajax/ajaxServiceRequestSetting.ajax.php",
					type : "POST",
					data: {"method": "SubmitTenantDefaults", "categoryId":categoryId, "approvalLevel":approvalLevel, "header":header, "footer":footer, "thankYouNote":thankYouNote},
					success: function(data)
					{
						alert ("Leave & License NOC Defaults are saved successfully.");
						window.location.reload();
					
					}
				});
			}
		}
		function submitAddressProofDefaults()
		{
			var categoryId = document.getElementById("addressProofCategoryId").value;
			if( categoryId  == "0")
			{
				alert("Please select category Id for Address Proof NOC Request.").
				document.getElementById('addressProofCategoryId').focus();
			}
			else
			{
				var approvalLevel = document.getElementById("addressProofApprovalLevel").value;
				//var header = CKEDITOR.instances.addressProofHeader.getData();
				var header = document.getElementById("addressProofHeader").value;
				header = header.trim().replace(/'/g,"/'");
				console.log("header : "+header);
				//var footer = CKEDITOR.instances.addressProofFooter.getData();
				var footer = document.getElementById("addressProofFooter").value;
				footer = footer.trim().replace(/'/g,"/'");
				console.log("footer : "+footer);
				//var thankYouNote = CKEDITOR.instances.addressProofThankyouNote.getData();
				var thankYouNote = document.getElementById("addressProofThankyouNote").value;
				thankYouNote = thankYouNote.trim().replace(/'/g,"/'");
				console.log("thankYouNote : "+thankYouNote);
				$.ajax
				({
					url : "ajax/ajaxServiceRequestSetting.ajax.php",
					type : "POST",
					data: {"method": "SubmitAddressProofDefaults", "categoryId":categoryId, "approvalLevel":approvalLevel, "header":header, "footer":footer, "thankYouNote":thankYouNote},
					success: function(data)
					{
						alert ("Address Proof Defaults are saved successfully.");
						window.location.reload();
					}
				});
			}
		}
	</script>
      <style>
        select.dropdown
        {
          position: relative;
          width: 100px;
          margin: 0 auto;
          padding: 10px 10px 10px 30px;
          appearance:button;
          /* Styles */
          background: #fff;
          border: 1px solid silver;
          /* cursor: pointer;*/
          outline: none;
        }
        @media print
        {    
          .no-print, .no-print *
          {
            display: none !important;
          }
          div.tr, div.td , div.th 
          {
            page-break-inside: avoid;
          }
        }
		#hide
        {
          display: none;
		  /*text-align: center;*/
        }
		/* Style the tab */
		<style>
		body {font-family: Arial;}

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
    		font-size: 17px;
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
     <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
  </head>
  <body>
    <center>
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
          <div class="panel-heading" id="pageheader">Service Request Setting</div>
            <form name = "serviceRequestSetting" id = "serviceRequestSetting" method="post" action="">
              <br>
              <div style="position:absolute, top: 10px, right:10px">
              	<table style="width:100%">
                	<tr>
                        <td width="100%" style="text-align:center;padding-top:1%"><b><input type="button" name="1" id="1" style="width:90%;text-align:center" onClick="collapsable(this.id);" Value = "Repair/Renovation Request" class="btn btn-primary"></b></td>
              		</tr>
                    <tr id="renovationDetailsTable" style="display:none;">
                        <td width="80%" style="text-align:center;padding-top:1%">
                        	<table width="100%" style="text-align:center;">
                            	<tr>
                                	<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Select Category &nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<select id="renovationCategoryId" name="renovationCategoryId"> 
            								<?php echo $combo_category = $obj_request->combobox("SELECT `id`, `category` FROM `servicerequest_category` WHERE `status` = 'Y'", $defaultDetails[0]['RenovationRequestCategoryId']); ?>
            							</select>
        							</td>
                                    <td style="text-align:right"><?php echo $star;?>&nbsp;<b>Approval Level&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<input type="text" name="renovationApprovalLevel" id="renovationApprovalLevel" style="width:20%" value = "<?php echo $defaultDetails[0]['LevelOfApprovalForRenovationRequest'];?>"/>
        							</td>
                                </tr>
                                
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Renovation Document Header&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="renovationHeader" id="renovationHeader" rows="5" cols="120"><?php echo $defaultDetails[0]['Renovation_template_header'];?></textarea>
                                        <script>
										//Uncomment to use ckeditor	
										/*		
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('renovationHeader', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>		
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Renovation Document Footer&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="renovationFooter" id="renovationFooter" rows="5" cols="120"><?php echo $defaultDetails[0]['Renovation_template_footer'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('renovationFooter', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Renovation Terms & conditions&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="renovationTC" id="renovationTC" rows="5" cols="120"><?php echo $defaultDetails[0]['Renovation_terms_conditions'];?></textarea>
                                        <script>
										//Uncomment to use ckeditor	
										/*			
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('renovationTC', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                    <!-- Uncomment below code to add rule one by one in ordered list format -->
                                    <!-- ------------------------------------------------------------------------------------------------------------------ -->
                                    <!--<td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<table width="80%" id="rulesTable">
                                         	<tr>
                                            	<td style="text-align:center;"><b>To add more terms & conditions click on Add.</b></td>
                                          	</tr>
                                            <?php
											/*if($defaultDetails[0]['Renovation_terms_conditions'] <> "")
											{
												$renovationTermsConditions = $defaultDetails[0]['Renovation_terms_conditions']; 
												$renovationTC = explode("</li>",$renovationTermsConditions);
												//var_dump($renovationTC);
												for($i = 0; $i < sizeof($renovationTC)-1; $i++)
												{
													if($i == 0)
													{ 
												?>
                                                <tr>
                                                	<td style="padding-top:1%;"><textarea name="rule[]" id="rule<?php echo $i+1;?>" cols="120"><?php echo ($i+1).". ".substr($renovationTC[$i],8);?></textarea>
                                                   
                                                	
                                                     <button type="button" class="btn btn-primary btn-xs" value="Add" onClick="addRule()" style="float: right; ">Add</button>
                                              		</td>
                                                </tr>
                                                	<?php 
													}
													else
													{
													?>
                                                    <tr>
                                                		<td style="padding-top:1%;">
                                                        <textarea name="rule[]" id="rule<?php echo $i+1;?>" cols="120"><?php echo ($i+1).". ".substr($renovationTC[$i],4);?></textarea>
                                                       </td>
                                                    </tr>
                                                
                                                    <?php 
													}
												}
											}*/
											?>
                                       	</table>
        							</td>-->
                                    <!-- ------------------------------------------------------------------------------------------------------------------ -->
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Renovation Thank You Note&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="RenovationThankyouNote" id="RenovationThankyouNote" rows="5" cols="120"><?php echo $defaultDetails[0]['RenovationRequestThankYouNote']?></textarea>
                                        <script>	
										//Uncomment to use ckeditor	
										/*		
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('RenovationThankyouNote', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Work Type&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<table width="45%" id="workTypeTable">
                                        
                                            <tr>
                                            	<td style="text-align:center;padding-top:1%;"><b>Work Type</b>
                                                </td>
                                                <td style="text-align:center;padding-top:1%;">
                                                	<b>Drawing file</b>
                                                     <button type="button" class="btn btn-primary btn-xs" value="Add" onClick="addWorkType()" style="float: right; ">Add</button>
                                              	</td>	
                                            </tr>
                                            <?php 
												if(sizeof($defaultDetails[0]['List_Of_Work']) > 0)
												{
													$workList = $defaultDetails[0]['List_Of_Work'];
												?>													 
                                                <input type = "hidden" id="workTypeCount" name = "workTypeCount" value = "<?php echo sizeof($workList);?>"/>
                                                <?php 
													
													for($i = 0; $i < sizeof($workList); $i++) 
													{
                                                ?>
                                                <tr id="workTypeTr_<?php echo ($i+1);?>">
                                            	
												<td style="text-align:center;padding-top:1%;"><input type="text" id="workType<?php echo ($i+1);?>" name="workType[]" style="vertical-align:top;width:90%;size:10%" value="<?php echo $workList[$i]['work']?>"/>
                                                </td>
                                                    <?php 
													if($i == 0)
													{
													?>
                                                    
                                                    <td style="text-align:left;padding-top:1%;padding-left:5%;">
                                                	<input type = "checkbox" name = "drawing[]" id = "drawing1" class = "drawing" <?php if ($workList[$i]['drawingReq'] == "Yes") { ?>checked<?php }else {}?>/>&nbsp;<b>Required</b>&nbsp;&nbsp;<a id="del" onClick="deleteWorkType(<?php echo ($i+1);?>)" style="width:15%;height:120%;"><img src="images/del.gif" alt="Delete" style="width:15%;height:120%;"></img></a></td>
                                                      
                                                    <?php 
													}
													else
													{
													?>
														<td style="text-align:left;padding-top:1%;padding-left:5%">
                                                	<input type = "checkbox" name = "drawing[]" id = "drawing<?php echo ($i+1);?>" class = "drawing" <?php if ($workList[$i]['drawingReq'] == "Yes") { ?>checked<?php }else {}?>/>&nbsp;<b>Required</b>&nbsp;&nbsp;<a id="del" onClick="deleteWorkType(<?php echo ($i+1);?>)" style="width:15%;height:120%;"><img src="images/del.gif" alt="Delete" style="width:15%;height:120%;"></img></a>
  </td>
                                                    <?php 
													}
												}
													?>
                                              	</td>
                                               
                                            </tr>
                                            	<?php 
													}
													else
                                                    {
													?>
                                                    <input type = "hidden" id="workTypeCount" name = "workTypeCount" value = "1"/>
                                                
                                                    <tr>
                                                    	<td style="text-align:center;padding-top:1%;"><input type="text" id="workType1" name="workType[]" style="vertical-align:top;width:90%;size:10%"/>
                                                		</td>
                                                		<td style="text-align:left;padding-top:1%;padding-left:5%;">
            		                                    	<input type = "checkbox" name = "drawing[]" id = "drawing1" class = "drawing"/>&nbsp;<b>Required</b>&nbsp;&nbsp;<a id="del" onClick="deleteWorkType(<?php echo ($i+1);?>)" style="width:15%;height:120%;"><img src="images/del.gif" alt="Delete" style="width:15%;height:120%;"></img></a>
                                                   			
                                                   	</td>
                                              		</tr>
                                                    <?php 
													}
													?>
                                              </table>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:center;padding-top:1%;" colspan="4">
                                    	<button type="button" class="btn btn-primary" value="Submit" onclick = "submitRenovationDefaults()">Submit</button>
                                        <button type="button" class="btn btn-primary" value="Cancel" >Cancel</button>
        							</td>
                                </tr>
                            </table>
                       	</td>
              		</tr>
                    <tr>
                        <td width="100%" style="text-align:center;padding-top:1%"><b><input type="button" name="2" id="2" style="width:90%;text-align:center" onClick="collapsable(this.id);" value = "Leave & License Request" class="btn btn-primary"></b></td>
              		</tr>
                    <tr id="tenantReuqestDetails" style="display:none;">
                        <td width="80%" style="text-align:center;padding-top:1%">
                        	<table width="100%" style="text-align:center;">
                            	<tr>
                                	<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Select Category &nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<select id="tenantCategoryId" name="tenantCategoryId"> 
            								<?php echo $combo_category = $obj_request->combobox("SELECT `id`, `category` FROM `servicerequest_category` WHERE `status` = 'Y'", $defaultDetails[1]['TenantRequestCategoryId']); ?>
            							</select>
        							</td>
                                    <td style="text-align:right"><?php echo $star;?>&nbsp;<b>Approval Level&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<input type="text" name="tenantApprovalLevel" id="tenantApprovalLevel" style="width:20%" value="<?php echo $defaultDetails[1]['LevelOfApprovalForTenantRequest'];?>"/>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Leave & License NOC Header&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="tenantHeader" id="tenantHeader" rows="5" cols="120"><?php echo $defaultDetails[1]['Tenant_NOC_header'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('tenantHeader', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>		
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Leave & License NOC Footer&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="tenantFooter" id="tenantFooter" rows="5" cols="120"><?php echo $defaultDetails[1]['Tenant_NOC_footer'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('tenantFooter', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Leave & License Thank You Note&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="tenantThankyouNote" id="tenantThankyouNote" rows="5" cols="120"><?php echo $defaultDetails[1]['TenantRequestThankyouNote'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('tenantThankyouNote', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:center;padding-top:1%;" colspan="4">
                                    	<button type="button" class="btn btn-primary" value="Submit" onClick="submitTenantDefaults()">Submit</button>
                                        <button type="button" class="btn btn-primary" value="Cancel" >Cancel</button>
        							</td>
                                </tr>
                            </table>
                       	</td>
              		</tr>
                    <tr>
                        <td width="100%" style="text-align:center;padding-top:1%"><b><input type="button" name="3" id="3" style="width:90%;text-align:center" onClick="collapsable(this.id);" value = "Address Proof Request" class="btn btn-primary"></b></td>
              		</tr>
                    <tr id="addressProofDetailsTable" style="display:none;">
                        <td width="80%" style="text-align:center;padding-top:1%">
                        	<table width="100%" style="text-align:center;">
                            	<tr>
                                	<td style="text-align:right"><?php echo $star;?>&nbsp;<b>Select Category &nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<select id="addressProofCategoryId" name="addressProofCategoryId"> 
            								<?php echo $combo_category = $obj_request->combobox("SELECT `id`, `category` FROM `servicerequest_category` WHERE `status` = 'Y'",$defaultDetails[2]['AddressRequestCategoryId']); ?>
            							</select>
        							</td>
                                    <td style="text-align:right"><?php echo $star;?>&nbsp;<b>Approval Level&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left">
                                    	<input type="text" name="addressProofApprovalLevel" id="addressProofApprovalLevel" style="width:20%" value="<?php echo $defaultDetails[2]['LevelOfApprovalForAddressProofRequest'];?>"/>
        							</td>
                                </tr>
                                 <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Address Proof NOC Header&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="addressProofHeader" id="addressProofHeader" rows="5" cols="120"><?php echo $defaultDetails[2]['AddressProof_NOC_header'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('addressProofHeader', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>		
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Address Proof NOC Footer&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="addressProofFooter" id="addressProofFooter" rows="5" cols="120"><?php echo $defaultDetails[2]['AddressProof_NOC_footer'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('addressProofFooter', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:right;padding-top:1%;"><b>Address Proof Thank You Note&nbsp;:&nbsp;</b></td>
                                    <td style="text-align:left;padding-top:1%;" colspan="3">
                                    	<textarea name="addressProofThankyouNote" id="addressProofThankyouNote" rows="5" cols="120"><?php echo $defaultDetails[2]['AddressProofThankyouNote'];?></textarea>
                                        <script>			
										//Uncomment to use ckeditor	
										/*
										CKEDITOR.config.extraPlugins = 'justify';
										CKEDITOR.replace('addressProofThankyouNote', {toolbar: [
											{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
											{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
											 ],
											 height: 150,
											 width: 700,
											 uiColor: '#14B8C4'
									});*/
										</script>
        							</td>
                                </tr>
                                <tr style="padding-top:1%">
                                	<td style="text-align:center;padding-top:1%;" colspan="4">
                                    	<button type="button" class="btn btn-primary" value="Submit" onClick="submitAddressProofDefaults()">Submit</button>
                                        <button type="button" class="btn btn-primary" value="Cancel" >Cancel</button>
        							</td>
                                </tr>
                            </table>
                       	</td>
              		</tr>
              	</table>
              </div>	
          	</form>
            <br>
            <br>
            <div style="width:70%">
            <?php
				//echo $obj_parkingType->pgnation();				
            ?>
            </div>
      	</div>
        </div>
<?php include_once "includes/foot.php"; ?>