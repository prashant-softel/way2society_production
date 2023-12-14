<?php //include_once "ses_set.php"; ?>
<?php
//include_once("includes/header.php");
include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");

include_once("classes/service_prd_reg_edit.class.php");
$obj_service_prd_reg = new service_prd_reg($m_dbConn, $m_dbConnRoot);

if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_service_prd_reg->reg_edit($_REQUEST['id']);
	}
}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css">
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsservice_prd_reg.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
	{
		setTimeout('hide_error()',6000);	
	}
	function hide_error()
	{
		document.getElementById('error').style.display = 'none';
	}
	</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker_bday.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "dd-mm-yy", 
		showOn: "both", 
		buttonImage: "images/calendar.gif",
		changeMonth: true,
    	changeYear: true,
    	yearRange: '-150:+10', 
		buttonImageOnly: true 
	})});
	
	function EnableBrowse(status, value)
	{						
		if(status == 'onstart')
		{
			value = document.getElementById('totaldoc').value;
			for(i = 0; i < value; i++)
			{
				var key = 'file'+i;
				document.getElementById(key).style.visibility = 'hidden';
			}			
		}
		else
		{
			var key = 'file'+value;		
			if (status) 
				document.getElementById(key).style.visibility = 'visible';            
			else 			
				document.getElementById(key).style.visibility = 'hidden';	
		}
	}
	
	function common()
	{
		go_error();
		EnableBrowse('onstart',0);
	}
    </script>
     <style>
.btn_register
{
color: #fff !important;
background-color: #337ab7 !important;
border-color: #2e6da4;
border-radius: 4px!important;
font-size: 14px;
font-weight: 400;
line-height: 1.42857143;
text-align: center;
white-space: nowrap;
vertical-align: middle;
cursor: pointer;
}
	</style>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['nul'])) { ?>
<body onLoad="common();">
<?php }else{ ?>
<body onLoad="EnableBrowse('onstart',0);">
<?php } ?>

<div class="panel panel-info" id="panel" style="display:block;width: 70%;margin-left:3.5%;margin-top:3%">
<div class="panel-heading" id="pageheader">Update service provider profile</div>
<br>
<center>
<button type="button" class="btn btn-primary" onClick="window.location.href='service_prd_reg_view.php?srm'">Back to service provider list</button>
</center>

<center>
<form name="service_prd_reg" id="service_prd_reg" method="post" action="process/service_prd_reg_edit.process.php" enctype="multipart/form-data" onSubmit="return val();">
   <?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center' border="0">
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="40"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="40"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="20"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
        <tr><td colspan="4" align="center" valign="middle"><b style="font-size:20px;color:#43729F">Personal Details</b></td></tr>
		<tr height="20"><td>&nbsp;</td></tr>
         
        <tr align="left">
        	<td><?php echo $star;?>Enter Society Staff Id</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="staff_id" id="staff_id" value="<?php echo $edit[0]['society_staff_id'];?>" disabled/ style="background-color:#efeeee;"></td>
		</tr>
		<tr align="left">
        	<td><?php echo $star;?>Enter Full Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="full_name" id="full_name" value="<?php echo $edit[0]['full_name'];?>"/></td>
		</tr>
        
		<tr align="left">
        	<td><?php echo $star;?>Photo</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <table><tr><td>
            <input type="file" name="photo" id="photo" size="18" />
            <input type="hidden" name="photo_old" id="photo_old" value="<?php echo $edit[0]['photo'];?>"/>
            <input type="hidden" name="photo_thumb_old" id="photo_thumb_old" value="<?php echo $edit[0]['photo_thumb'];?>"/>
            </td><td>
            <a href="<?php echo substr($edit[0]['photo'],3);?>"><img src="<?php echo substr($edit[0]['photo_thumb'],3);?>" height="30" width="30"></a>
            </td></tr></table>
            </td>
		</tr>
        
		<tr align="left">
			<td valign="top"><?php echo $star;?>Category</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td valign="top">
                <div style="overflow-y:scroll;overflow-x:hidden;width:280px; height:120px; border:solid #CCCCCC 2px;">
				<?php echo $combo_cat_id = $obj_service_prd_reg->combobox111("select cat_id,cat from cat where status='Y' order by cat","cat_id[]","cat_id","select cat_id from spr_cat where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); ?>
                </div>
            </td>
		</tr>
        
        <!--
		<tr align="left">
			<td>Age</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="age" id="age" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['age'];?>"/></td>
		</tr>
		-->
        
        <tr align="left">
			<td><?php echo $star;?>Date of Birth </td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="dob" id="dob" class="basics" size="10" value="<?php echo $edit[0]['dob'];?>" readonly style="width:100px;"/></td>
		</tr>
		
        <tr align="left">
        	<td><?php echo $star;?>Identification Marks</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="identy_mark" id="identy_mark" value="<?php echo $edit[0]['identy_mark'];?>"/></td>
		</tr>
		<tr align="left">
			<td>Working In Society Since</td>
            <td>&nbsp; : &nbsp;</td>
			<td><!--<input type="text" name="since" id="since" value="<?php echo $edit[0]['since'];?>" size="10" maxlength="4" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" style="width:100px;"/>&nbsp;<font color="#FF0000">[ Year Only ]</font>-->
            <input type="text" name="since" id="since" value="<?php echo getDisplayFormatDate($edit[0]['since']);?>" class="basics" size="10" readonly  style="width:100px;"/></td>
		</tr>
        
		<tr align="left">
        	<td><?php echo $star;?>Education</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="education" id="education" value="<?php echo $edit[0]['education'];?>"/></td>
		</tr>
        
		<tr align="left">
			<td>Married</td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<?php
				if($edit[0]['marry']=="Yes")
				{
					$marry = "checked='checked'";
				}
				else 
				{
					$marry1 = "checked='checked'";
				}
				?>
                <input type="radio" name="marry" id="marry" value="Yes" <?php echo $marry;?>>Yes	
                <input type="radio" name="marry" id="marry" value="No" <?php echo $marry1;?>>No	
            </td>
		</tr>
        
        <tr><td><br></td></tr>
     <tr><td colspan="4" height="30" align="center" valign="middle" ><font size="+1"><b style="font-size: 15px;">Contact Details</b></font></td></tr> 
         <tr><td><br></td></tr>
     	 <table style="width:90%">
      	<tr>
        <td style="width:50%" colspan="3">
     	 <table style="width:100%">  
        <tr align="left">
        	<td valign="top"><?php echo $star;?>Current Residence<br>Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="cur_resd_add" id="cur_resd_add" rows="4" cols="32"><?php echo $edit[0]['cur_resd_add'];?></textarea></td>
		</tr>
		<tr align="left">
        	<td><?php echo $star;?>Contact No.1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="cur_con_1" id="cur_con_1" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['cur_con_1'];?>"/></td>
		</tr>
        
		<tr align="left">
			<td>Contact No.2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="cur_con_2" id="cur_con_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['cur_con_2'];?>"/></td>
		</tr>
       <tr><td><br><br></td></tr>
		<tr align="left">
        	<td><?php echo $star;?>Reference Name - 1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_name" id="ref_name" value="<?php echo $edit[0]['ref_name'];?>"/></td>
		</tr>
        <tr align="left">
        	<td valign="top"><?php echo $star;?>Reference Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="ref_add" id="ref_add" rows="4" cols="32"><?php echo $edit[0]['ref_add'];?></textarea></td>
		</tr>
        <tr align="left">
        	<td><?php echo $star;?>Reference Contact No.1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_con_1" id="ref_con_1" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['ref_con_1'];?>"/></td>
		</tr>
        <tr align="left">
			<td>Reference Contact No.2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_con_2" id="ref_con_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['ref_con_2'];?>"/></td>
		</tr>
		</table></td>
        <td style="width:50%;" colspan="3">
        <table style="width:100%">
        <tr align="left">
        	<td valign="top"><?php //echo $star;?>Permanent / Native <br> Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="native_add" id="native_add" rows="4" cols="32"><?php echo $edit[0]['native_add'];?></textarea></td>
		</tr>
		
        <tr align="left">
        	<td><?php //echo $star;?>Contact No.1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="native_con_1" id="native_con_1" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['native_con_1'];?>"/></td>
		</tr>
        
		<tr align="left">
			<td>Contact No.2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="native_con_2" id="native_con_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $edit[0]['native_con_2'];?>"/></td>
		</tr>
         <tr><td><br><br></td></tr>
        <tr align="left">
			<td>Reference Name - 2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_name2" id="ref_name2" size="30" value="<?php echo $edit[0]['ref_name2'];?>"/></td>
		</tr>
        <tr align="left">
			<td valign="top">Reference Address</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><textarea name="ref_add2" id="ref_add2" rows="4" cols="32"><?php echo $edit[0]['ref_add2'];?></textarea></td>
		</tr>
        <tr align="left">
			<td>Reference Contact No.1</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_con_1_2" id="ref_con_1_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30" value="<?php echo $edit[0]['ref_con_1_2'];?>"/></td>
		</tr>
        <tr align="left">
			<td>Reference Contact No.2</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="ref_con_2_2" id="ref_con_2_2" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30" value="<?php echo $edit[0]['ref_con_2_2'];?>"/></td>
		</tr>
        </table></td></tr>
        </table>
        
         <tr><td><br><br></td></tr>
         <tr><td colspan="4" height="40" align="center" valign="middle"><font size="+1"><b style="font-size: 15px;">Others & Family Details</b></font></td></tr>  
         <tr><td><br><br></td></tr>
		
        
       	<tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">
            <table align="center" border="1">
            <tr>
                <th width="110" height="25">Family Details</th>
                <th>Name</th>
                <th>Occupation/Contact No./Address</th>
            </tr>
            <tr align="center">
                <td>Father</td>
                <td><input type="text" name="father_name" id="father_name" 	size="18" value="<?php echo $edit[0]['father_name'];?>"/></td>
                <td><input type="text" name="father_occ" id="father_occ" size="18" value="<?php echo $edit[0]['father_occ'];?>"/></td>
            </tr>
            <tr align="center">
                <td>Mother</td>
                <td><input type="text" name="mother_name" id="mother_name" size="18" value="<?php echo $edit[0]['mother_name'];?>"/></td>
                <td><input type="text" name="mother_occ" id="mother_occ" size="18" value="<?php echo $edit[0]['mother_occ'];?>"/></td>
            </tr>
            <tr align="center">
                <td>Husband / Wife</td>
                <td><input type="text" name="hus_wife_name" id="hus_wife_name" size="18" value="<?php echo $edit[0]['hus_wife_name'];?>"/></td>
                <td><input type="text" name="hus_wife_occ" id="hus_wife_occ" size="18" value="<?php echo $edit[0]['hus_wife_occ'];?>"/></td>
            </tr>
            <tr align="center">
                <td>Son / Daughter</td>
                <td><input type="text" name="son_dou_name" id="son_dou_name" size="18" value="<?php echo $edit[0]['son_dou_name'];?>"/></td>
                <td><input type="text" name="son_dou_occ" id="son_dou_occ" size="18" value="<?php echo $edit[0]['son_dou_occ'];?>"/></td>
            </tr>
            <tr align="center">
                <td>Other One</td>
                <td><input type="text" name="other_name" id="other_name" size="18" value="<?php echo $edit[0]['other_name'];?>"/></td>
                <td><input type="text" name="other_occ" id="other_occ" size="18" value="<?php echo $edit[0]['other_occ'];?>"/></td>
            </tr>
            </table>       
        </td></tr>
        <tr><td colspan="4"><br></td></tr>
        
		<tr align="left">
        	<td valign="top"><?php echo $star;?><b style="font-size: 15px;">Document Attached for Verification</b><br></td>
            <td valign="top">&nbsp;  &nbsp;</td>
           
			<td>
               <!-- <div style="overflow-y:scroll;overflow-x:hidden;width:280px; height:120px; border:solid #CCCCCC 2px;">
				<?php //echo $combo_cat_id = $obj_service_prd_reg->combobox111("select document_id,document from document where status='Y' order by document","document[]","document","select document_id from spr_document where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'"); ?>
                </div> -->
                <table align="center">                	
                    <?php
						$documents = $obj_service_prd_reg->fetchDocuments();
						$selecteddocs = $obj_service_prd_reg->fetchSelectedDocs($_REQUEST['id']);
						for($i = 0; $i < sizeof($documents); $i++)
						{ 							
					?>
					<tr>
                    	<?php 
							$checked = '';
							for($j = 0; $j < sizeof($selecteddocs); $j++)
							{
								if($documents[$i]['document_id'] == $selecteddocs[$j]['document_id'])
								{						
                        			$checked = 'checked';
									
									$FileExt=explode('.' ,$selecteddocs[$j]['attached_doc']);
									$checkExt=$FileExt[1];
									
									$Files=$selecteddocs[$j]['attached_doc'];
									$Spr_Doc_id=$selecteddocs[$j]['spr_document_id'];
									
								}																								
					   		} 
							if( $checked <> "")
							{?>
                        <td> <input type="checkbox" value="<?php echo $documents[$i]['document_id'];?>"  id="document<?php echo $i;?>" name="document<?php echo $i;?>" checked='checked' onClick="EnableBrowse(this.checked, <?php echo $i;?>);"/> </td>
                       
                        <?php }
							else
							{ ?>
						<td> <input type="checkbox" value="<?php echo $documents[$i]['document_id'];?>"  id="document<?php echo $i;?>" name="document<?php echo $i;?>" onClick="EnableBrowse(this.checked, <?php echo $i;?>);"/> </td>		
						<?php } ?> 
                    	<td> <?php echo $documents[$i]['document']; ?> </td>
                        <td> <input type="file" name="file<?php echo $i;?>" id="file<?php echo $i;?>" />
                       <input type="hidden" name="file<?php echo $i;?>" id="file<?php echo $i;?>" value="<?php echo $Files?>"/>
                         </td>                  <?php 
                        if($checked <> "")
						{
						?>
                      
                   <?php 
				   		if($checkExt == 'jpeg' || $checkExt == 'jpg' || $checkExt == 'png' || $checkExt == 'bmp')
					   	 {?> <td>
                      	 	<a href="Service_Provider_Documents/<?php echo $Files;?>"><img  style="width:50px; height:35px;" src="Service_Provider_Documents/<?php echo $Files?>"></a><a href="javascript:void(0);" onClick="del_Doc('<?php echo $Files;?>',<?php echo $Spr_Doc_id;?>);"><img style="width: 15px;margin-top: -30px; margin-left: -10px;" src="images/del.gif" /></a> </td>   
                    <?php }
					   else if($checkExt == 'pdf')
					   	 {?> <td>
						    <a href="Service_Provider_Documents/<?php echo $Files;?>"><img  style="width:50px; height:50px;" src="Service_Provider_Documents/pdf.png"></a><a href="javascript:void(0);" onClick="del_Doc('<?php echo $Files;?>',<?php echo $Spr_Doc_id;?>);"><img style="width: 15px;margin-top: -30px; margin-left: -10px;" src="images/del.gif" /></a> </td>   
						  <?php 
						 }
					    else if($checkExt == 'doc' || $checkExt == 'docx' )
						{?> <td>
							 <a href="Service_Provider_Documents/<?php echo $Files;?>"><img  style="width:50px; height:40px;" src="Service_Provider_Documents/Doc.png"></a><a href="javascript:void(0);" onClick="del_Doc('<?php echo $Files;?>',<?php echo $Spr_Doc_id;?>);"><img style="width: 15px;margin-top: -30px; margin-left: -10px;" src="images/del.gif" /></a> </td>   
						<?php 
						}
						else
						{?>
					         <td>
							 <a href="Service_Provider_Documents/<?php echo $Files;?>"><img  style="width:50px; height:45px;" src="Service_Provider_Documents/file.png"></a><a href="javascript:void(0);" onClick="del_Doc('<?php echo $Files;?>',<?php echo $Spr_Doc_id;?>);"><img style="width: 15px;margin-top: -30px; margin-left: -10px;" src="images/del.gif" /></a> </td>     
                         <?php }
						 }
						 ?>  
                                
                    </tr>	
                    <?php	
						}
					?>
                    <tr><td colspan="3">	<input type="hidden" name="totaldoc" id="totaldoc" value="<?php echo $i; ?>"  />
                    						 </td></tr>
                </table>
        	</td>
		</tr>
        
        <table width="90%">
      <center> <tr align="left">
        	
			<tr><td colspan="4" height="40" align="center" valign="middle"><font size="+1"><?php echo $star;?><b style="font-size: 15px;">Service provider working in following units</b></font></td></tr>
		</tr></center>		
		
       <tr align="center"><td colspan="4">        
          	<table align="center">
                <tr align="left">
                    <th valign="top" style="width:40%;" align="center">All Unit List</th>
                    <td valign="top" style="width:20%;" align="center">&nbsp;&nbsp; <b> : </b>&nbsp;&nbsp;</td>
                    <th valign="top" style="width:40%;" align="center">Working in Units</th>  
                </tr>
                <tr>                  
                    <td>                     	                       
						<select name="unit" id="unit" multiple="multiple"  style="width: 300px;height: 200px;overflow-x: auto;" class="dropdown">                   
                        <?php 
							$units = $obj_service_prd_reg->fetchUnits();
							for($i = 0; $i < sizeof($units); $i++)
							{
						?>                           
                           <option value="<?php echo $units[$i]['unit_id']; ?>"> <?php echo $units[$i]['unit_no']. "[" . $units[$i]['owner_name']. "]"; ?> </option>
                        <?php  
							}
						?>
                        </select>
							<p>(Note : You can select multiple units by pressing ctrl key + unit no.)</p>                                    
                   	</td>
                    <td>
                    	<input type="button" name="add" id="add" value="Add" onClick="addUnit();" /> <br /> <br />
                        <input type="button" name="remove" id="remove" value="Remove" onClick="removeUnit();" /> 
                    </td>
                    <td>
                    <select name="selectedUnits" id="selectedUnits" multiple="multiple"  style="width: 300px;height:200px;overflow-x: auto;" class="dropdown"> 
                      <?php 
							$units = $obj_service_prd_reg->fetchSelectedUnits($_REQUEST['id']);
							for($i = 0; $i < sizeof($units); $i++)
							{
						?>                           
                           <option value="<?php echo $units[$i]['unit_id']; ?>"> <?php echo $units[$i]['unit_no']; ?> </option>
                           <script language="javascript">
						   		$("#unit option[value='<?php echo $units[$i]['unit_id']; ?>']").remove();
						   </script>
                        <?php  
							}
						?>   
                        </select>                        
                    </td>
                    <td>
                    <input type="hidden" name="unit1" value="" id="unit1">                                        
                    </td>
                </tr>
        </table>
       </td></tr>	
       
        
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id" value="<?php echo $edit[0]['service_prd_reg_id'];?>">            
            <input type="submit" name="insert" id="insert" class="btn_register" value="Update" onClick="setArray();">
            </td>
		</tr>
        <tr><td><br></td></tr>
</table>
</form>
</center>

<script language="javascript">
function addUnit() 
{ 
 	var units = document.getElementById('unit');	
	for(i = 0; i < units.length; i++)
	{						
		if (units.options[i].selected)
		{					
			var text = units.options[i].text;	
			var val = units.options[i].value;		
			//var val = document.getElementById('unit').value;	
			var newDD = document.getElementsByName('selectedUnits'); 					
			$(newDD).append("<option value="+val+">"+text+"</option>");
			units.remove(i);
			i--;			
		}
	}		
}

function removeUnit()
{		
	var selectedUnits = document.getElementById('selectedUnits');	
	for(i = 0; i < selectedUnits.length; i++)
	{		
		if (selectedUnits.options[i].selected)
		{	
			var text = selectedUnits.options[i].text;		
			var val = selectedUnits.options[i].value;							
			var newData = document.getElementsByName('unit'); 			
			$(newData).append("<option value="+val+">"+text+"</option>");
			selectedUnits.remove(i);
			i--;
		}
	}		
}

function setArray()
{		
	var unit1 = new Array();	
	var units = document.getElementById('selectedUnits');		
	for(i = 0; i < units.length;i++)
	{
		var val = units.options[i].value;
		unit1.push(val);
		var text = units.options[i].text;
		unit1.push(text);		
	}		
	document.service_prd_reg.unit1.value = JSON.stringify(unit1);	
	//alert(unit1);
}
</script>



<table align="center" style="display:none;">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_service_prd_reg->pgnation();
echo "<br>";
echo $str = $obj_service_prd_reg->display1($str1);
echo "<br>";
$str1 = $obj_service_prd_reg->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>


<?php include_once "includes/foot.php"; ?>
