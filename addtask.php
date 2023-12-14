
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title> W2S - Add Task </title>
</head>



<?php include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");
$dbConnRoot =new dbop(true);
include_once("classes/tasks.class.php");
$obj_task = new task($m_dbConn, $dbConnRoot);
include_once ("classes/dbconst.class.php");
//print_r($_SESSION);
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$sComment = "";
$PostDate = "";
$UnitBlock = $_SESSION["unit_blocked"];
$IsNotify = 1;
$sSubject = "";
$sAmount = "";
$sUnitID = "";
$sCreationDate = date(d-m-y);

if(isset($_REQUEST['ID']) && $_REQUEST['module'] == 1)
{
	$arReversalData = $obj_notice->getComment($_REdQUEST['ID']);
	//print_r($arReversalData);
	$sDesc  = $arReversalData[0]["Comments"];
	$sAmount = $arReversalData[0]["Amount"];
	$sSubject  = "Notice : Fine of Rs ".$sAmount;
	//$sUnitID = $arReversalData[0]["comment"];
	$sUnitID = $arReversalData[0]["UnitID"];
	$sTimeStamp = $arReversalData[0]["TimeStamp"];
	$PostDate = date('d-m-Y', $sTimeStamp);
	$sLedgerID = $arReversalData[0]["LedgerID"];
	$resLedger = $m_dbConn->select("select `ledger_name` from ledger");
	$sComment = "Dear Member, <br>This notice is being sent to inform you that management has charged you for Rs." .$sAmount ." for following violation: ". $sDesc." <br><br> Charges will be relected in your next maintenance bill.<br><br>If you have any questions, pl contact society Manager or Secretary. <br><br>From Managing Committee.";
	//$arReversalData[0]["comment"];

}
//echo "email:".$objFetchData->objSocietyDetails->sSocietyEmail;
?>


<html>
<head>
<style>
.submitButton
{
	color: #fff !important;
    background-color: #337ab7 !important ;
    border-color: #2e6da4 !important;
	padding: 6px 12px !important;
	    font-size: 14px;
    font-weight: 400;
}
select.dropdown {
    position: relative;
    width: 100px;
    margin: 0 auto;
    padding: 10px 10px 10px 30px;
	appearance:button;
	

    /* Styles */
    background: #fff;
    border: 1px solid silver;
    cursor: pointer;
    outline: none;
	
}

</style>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/jsnotice.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script language="javascript" type="application/javascript">
	function go_error()
    {
		//alert('go_error');
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
		//alert('hide');
        document.getElementById('error').style.display = 'none';	
    }
	
	//$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			window.location.href='suspend.php';
			
		}
    
	//});
	
 </script>
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
  
  <script language="javascript" type="text/javascript">
	  	function FetchTemplateData(id)
	  	{
			if(confirm("This template requires some user input. Would you like to go to Document maker for this purpose?") == true)
			{
				window.open("document_maker.php?tempid=" + id.value + "&View=ADMIN","_self");
			}
			else
			{
				var template_id = id.value;
		  		$.ajax({
				url : "ajax/ajaxnotice.ajax.php",
				type : "POST",
				dataType: "json",
				data: {"method": "fetch_templates", "template_id":template_id} ,
				success : function(data)
				{	
					//alert(data["template_subject"]);
					//var a		= data.trim();
					var val = CKEDITOR.instances['description'].getData();		
					//alert(val.trim().length);			
					if((val.trim().length > 0))
					{ 	
						if(confirm("You have already entered text in Notice Description section. Would you like replace it?") == true)
						{
							if(data == null)
							{
								CKEDITOR.instances['description'].setData("");
								document.getElementById("subject").value = "";		
							}
							else
							{
								CKEDITOR.instances['description'].setData(data["template_data"]);
								document.getElementById("subject").value = data["template_subject"];		
							}	
						}
						else
						{
							//document.getElementById("notice_template").innerHTML = "Please Select";
						}
					}
					else
					{						
						CKEDITOR.instances['description'].setData(data["template_data"]);
						document.getElementById("subject").value = data["template_subject"];
					}
				}
				})
			}	  		
	  	}
		function EnableNoticeType(value)
		{										
			if (value == 1) 
			{				
				$('#upload').hide();
				$('#create').show();
				$('#desc').show();	
				//CKEDITOR.instances['description'].setData("");											
			}            
       		else if(value == 2)
			{				
				//$('#create').hide();
				//$('#desc').hide();
				$('#create').show();
				$('#desc').show();
				$('#upload').show();								
				//CKEDITOR.replace( 'description', {toolbarStartupExpanded : false} );
								
				if(document.getElementById('notify').checked)
				{  
					var val = CKEDITOR.instances['description'].getData();					
					if(!(val.length > 0))
					{ 				
						var msgText = 'Dear Member, <br /> <br /> Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br /> Thanking you, <br />' + document.getElementById('issueby').value;						
						CKEDITOR.instances['description'].setData(msgText);	
					}
				}
			}
			else if(value == 0)
			{									
				$('#upload').hide();
				$('#create').hide();
				$('#desc').hide();				
				//CKEDITOR.instances['description'].setData("");				
			}
		}
		
		function uploadText(id)
		{
			//alert(id);
			var val = CKEDITOR.instances['description'].getData();					
						
			if(id)
			{
				value = document.getElementById('notice_creation_type').value;							
				<?php 
				//echo "name:".$objFetchData->objSocietyDetails->sSocietyEmail;
				if($objFetchData->objSocietyDetails->sSocietyEmail == "")
					{?>
						alert("Please set society Email ID to use this feature");
					   window.location.href ="society.php?id=" + "<?php echo $_SESSION['society_id'];?>" + "&show&imp";
							
			   <?php }?>
				
				if (value == 2) 
				{
					var uploaded_fileName = document.getElementById('userfile').value;
					//alert(uploaded_fileName.length);
					if(!(val.length > 0))
					{ 
					//var msgText = 'Dear Member, <br /> <br /> Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br /> Thanking you, <br />' + document.getElementById('issueby').value;						
					var msgText = 'Dear Member, <br /> <br /> ';
						if(uploaded_fileName.length != "")
						{
							msgText += 'Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br />';
						}
						msgText += 'Thanking you, <br />' + document.getElementById('issueby').value;						
					CKEDITOR.instances['description'].setData(msgText);
					$("#description").val(msgText);

					}
				}
			}
			else
			{	
				value = document.getElementById('notice_creation_type').value;							
				if (value == 2) 
				{		
					if(!(val.length > 0))
					{ 																			
					CKEDITOR.instances['description'].setData("");	
					}
				}
			}
			
		}
	document.body.onload =	function()
		{			
			go_error();
			EnableNoticeType(0);
		}
	</script>	
 <!-- <script type="text/javascript" src="jquery.js"></script> -->
    <script type="text/javascript">
    function setCookie(cname, cvalue, exdays) 
    {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) 
	{
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}

	function checkCookie() 
	{
	    var user = getCookie("username");
	    if (user != "") {
	        alert("Welcome again " + user);
	    } else {
	        user = prompt("Please enter your name:", "");
	        if (user != "" && user != null) {
	            setCookie("username", user, 365);
	        }
	    }
	}
    var iIsEmailSent = 0;	
	$(document).ready(function() 
	{
		var module = '<?php echo $_REQUEST['module']; ?>';
		if(module == 1)
		{
			var sReversaID = '<?php echo $_REQUEST['ID'] ?>';
			if(sReversaID != '')
			{	
				//$("#addnotice").hide();	
				//document.getElementById('#addnotice').style.visibility = 'hidden';
		    	document.getElementById('notice_type').value = 7;
		    	//document.getElementById('document_type').value = 7;
	    		//document.getElementById('exp_date').value = '00-00-0000';
	    		var sSelectedUnitID = '<?php echo $sUnitID ?>';
		    	document.getElementById('notify').checked = '<?php echo $IsNotify ? "1" : "0"?>';
				document.getElementById('subject').value = '<?php echo $sSubject ?>';
				var sComment = '<?php echo $sComment ?>';
				//alert(sComment);
		    	CKEDITOR.instances['description'].setData(sComment);
		    	$("#description").val(sComment);

	    		var s = document.getElementById("post_noticeto");			
				s.options[0].selected = false;
				for ( var k = 0; k < s.options.length; k++ )
				{																												
					if(s.options[k].value == sSelectedUnitID)
					{																												
						s.options[k].selected = true;																							
					}											
				}
			
				//alert(iIsEmailSent);
				//$("#addnotice").submit();
				//document.getElementById("addnotice").submit();
				iIsEmailSent = 1;
				setTimeout(function() 
				{
					alert("Please click on Submit to create Notice.");
				}, 200);
				//$( "#insert" ).trigger( "click" );
			}
			else	
			{
				document.getElementById('notice_type').value = 4;
	    	
			}
		}
		else if(module == 2)
		{		
			setTimeout(function() 
			{
				alert("Please click on Submit to create Notice.");
			}, 200);
			//your code
			var selected_unit = '<?php echo $_REQUEST['unitid']; ?>';
			//alert(selected_unit);
			var ckeditor = "<?php echo $_REQUEST['ckeditor']; ?>";
			var date = '<?php echo $_REQUEST['date']; ?>';
			
			CKEDITOR.instances['description'].setData(ckeditor);
			
			var s = document.getElementById("post_noticeto");			
			s.options[0].selected = false;
			for ( var k = 0; k < s.options.length; k++ )
			{																												
				if(s.options[k].value == selected_unit)
				{																												
					s.options[k].selected = true;																							
				}										
			}
			
			if(date != '')
			{
				document.getElementById('post_date').value = date;
				document.getElementById('exp_date').value = date;
			}
			
			document.getElementById('subject').value = "<?php echo $_REQUEST['sub']; ?>";
			document.getElementById('notify').checked = true;
			
			var temp_id = '<?php echo $_REQUEST['tempid']; ?>';
			if(temp_id == 27) //overdue payment
			{
				document.getElementById('notice_type').options[5].selected = true;
				document.getElementById('notice_template').options[2].selected = true;
			}
			else if(temp_id == 25) //agm
			{
				document.getElementById('notice_type').options[4].selected = true;
				document.getElementById('notice_template').options[1].selected = true;
			}
			else if(temp_id == 28) //fine
			{
				document.getElementById('notice_type').options[6].selected = true;
				document.getElementById('notice_template').options[3].selected = true;
			}
			else if(temp_id == 29) //reverse charge
			{
				document.getElementById('notice_type').options[7].selected = true;
				document.getElementById('notice_template').options[4].selected = true;
			}
			else if(temp_id == 37) //web access blocked
			{
				document.getElementById('notice_type').options[4].selected = true;
				document.getElementById('notice_template').options[5].selected = true;
			}
		}
		else if(module == 3)
		{		
			setTimeout(function() 
			{
				alert("Please click on Submit to create Notice.");
			}, 200);
			
			var unit_id = '<?php echo $_REQUEST['unitid']; ?>';
			var temp_id = '<?php echo $_REQUEST['tempid']; ?>';
  			
			var s = document.getElementById("post_noticeto");			
			s.options[0].selected = false;
			for ( var k = 0; k < s.options.length; k++ )
			{																												
				if(s.options[k].value == unit_id)
				{																												
					s.options[k].selected = true;																							
				}										
			}
			
			$.ajax({
			url : "ajax/ajaxdocument_maker.ajax.php",
			type : "POST",
			dataType: "json",
			data: {"method": "fetch_template_data", "template_id":temp_id, "unit_id":unit_id} ,
			success : function(data)
			{
				//alert(data);
				CKEDITOR.instances['description'].setData(data);
			}
			});
			
			if(temp_id == 37) //web access blocked
			{
				document.getElementById('notice_type').options[4].selected = true;
				document.getElementById('notice_template').options[5].selected = true;
			}
			else if(temp_id == 38) //web access restored
			{
				document.getElementById('notice_type').options[4].selected = true;
				document.getElementById('notice_template').options[6].selected = true;
			}
		}
	});
	</script>
  
</head>
<?php if(isset($_POST["ShowData"])){?>
<body>
<?php } ?>

<div id="middle">

<br>
<div class="panel panel-info" style="margin-top:2%;margin-left:3.5%; border:none;width:70%">
<div class="panel-heading" id="pageheader"><button type="button" class="btn btn-primary" onclick="window.location.href='tasks.php'" style="float: left">Go Back</button>
Add New Task</div>
<br>
<br>
<center>
<form name="addtask" id="addtask" method="post" action="process/tasks.process.php" enctype="multipart/form-data" onSubmit="return val();">
<?php $star = "<font color='#FF0000'>*&nbsp;</font>";?>
<table align='center' style="width:90%">
	<?php
		if(isset($_POST["ShowData"]))
			{
	?>
				<tr height="30"><td colspan="8" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
	<?php   }
			else
			{?>
    			<tr height="30"><td colspan="8" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
          <?php } ?>
          
     <tr>
        <td rowspan="17" valign="middle"></td>
        <th rowspan="17"></th>
        <td rowspan="17"></td>
        <td rowspan="17"><b>Assigned To :</b></br>
        <select name="AssignedTo[]" id="AssignedTo" multiple="single"  style=" width:250px; height:350px;" class="dropdown" >
                <?php //echo $combo_unit = $obj_notice->combobox2("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order ", $_REQUEST['unit_id'],'0');
					?>
				<?php
					//$sql ="SELECT mapping.id,mapping.login_id, login.member_id,login.name,mapping.unit_id,mapping.desc FROM `mapping` join login on mapping.login_id=login.login_id where mapping.society_id=";
					echo $combo_unit = $obj_task->combobox2Root("SELECT mapping.id, CONCAT(CONCAT(mapping.desc,' - '),login.name) FROM `mapping` join login on mapping.login_id=login.login_id where mapping.society_id='" . $_SESSION['society_id'] . "'",$_REQUEST['unit_id'],'0'); 
				?>	
             </select>
             
        </td>
     
      </tr>
      

    <tr>
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Title</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><input type="text" name="title" id="title" style="width:200px; resize:none" required="required" /></td>
	</tr>   
    
	<tr>
	    	<td>&nbsp;
	        </td>
	    </tr>
   <tr id="">
	   <td valign="middle"></td>
	    <th style="text-align:left;"><b>Description </b></th>
	    <td>&nbsp;  <b>:</b>&nbsp;</td>
	    <td colspan="6"><textarea name="task_desc" id="task_desc" rows="5" cols="50" required="required"></textarea></td>
    </tr>
	<tr>
	    	<td>&nbsp;
	        </td>
	    </tr>
	    <tr>
	        	<td valign="middle"><?php echo $star;?></td>
				<th><b>Due Date</b></th>
	            <td>&nbsp;<b>:<b>&nbsp;</td>
				<td><input type="text" name="due_date" id="due_date" value="<?php echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;"/></td>
	     </tr>

	<!-- <tr >
	    	<td>&nbsp;
	        </td>
	    </tr>
      <tr>
        <td><?php echo $star;?></td>
        <td><b>Task Type</b></td>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="task_type" id="task_type">
        		<?php //echo $combo_doc = $obj_task->combobox("select ID, doc_type from document_type",'0');
						?>
        	</select>
        </td>
	</tr> -->
   <tr>
    	<td>&nbsp;
        </td>
    </tr>
    <tr align="left" style="">
        <td valign="left"></td>
        <th><b>Priority</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="priority" id="priority" style=" width:135px; height:20;">
                <OPTION VALUE="<?php echo PRIORITY_LOW; ?>" selected>Low</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_MEDIUM; ?>">Medium</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_HIGH; ?>">High</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_CRITICAL; ?>">Critical</OPTION>
             </select>
        </td>
	</tr>
	<tr>
    	<td>&nbsp;
        </td>
    </tr>
    <tr align="left" style="">
        <td valign="left"></td>
        <th><b>Status</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="status" id="status" style=" width:135px; height:20;">
                <OPTION VALUE="<?php echo PRIORITY_LOW; ?>" selected>Raised</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_MEDIUM; ?>">Waiting</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_HIGH; ?>">In Progress</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_CRITICAL; ?>">Completed</OPTION>
                <OPTION VALUE="<?php echo PRIORITY_CRITICAL; ?>">Cancelled</OPTION>
             </select>
        </td>
	</tr>       
    <tr>
    	<td>&nbsp;
        </td>
    </tr>
    <tr id=""> 
        <td valign="middle"></td>
        <td><b>Attachment</b></td>   
        <td>&nbsp;<b>:<b>&nbsp;</td>               
        <td><input name="userfile" id="userfile" type="file" /> <a id="noticename" style="visibility:hidden;" target="_blank"> View Attachment </a></td>
    </tr>

    <tr>
    	<td valign="middle"></td>
        
        <td><b>Percent Completed</b></td>
        	<td><b>:</b></br></td>
        <td><input type="text" name="PercentCompleted" id="PercentCompleted"  min="0" max="100"  value="0"  style="" />
        </td>
     
      </tr>

    <tr>
    	<td>&nbsp;
        </td>
    </tr>    
    <tr align="left" >
    	<td valign="left"></td> 
        <td><b> Notify Members By Email </b></td>  
        <td>&nbsp;<b>:<b>&nbsp;</td>     
        <td><input type="checkbox" name="notify" id="notify" value="1" onChange="uploadText(this.checked);">   </td>
     </tr>
     <tr>
    	<td>&nbsp;
        </td>
    </tr>
    
   
   <tr><td colspan="4">&nbsp;</td></tr>
  
    
    <!--<tr id=""><td colspan="4"><textarea name="description" id="description" rows="5" cols="50"></textarea></td></tr>-->
       	<script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify,table';
			//CKEDITOR.config.extraPlugins = 'table';
			CKEDITOR.replace('description', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
								{ name: 'insert', items: [ 'Table' ] },
								{ name: 'insert_2', items: [ 'PageBreak' ] }
   								 ],
								 height: 300,
        						 width: 650,
								 uiColor: '#14B8C4'
								 });
		</script>
        
        
        
     <tr><td colspan="4">&nbsp;</td></tr>
 <!--   <tr>
    <td valign="middle"></td>
    <th style="text-align:left;"><b>Extra Note</b></th>
    <td colspan="2">&nbsp; : &nbsp;</td>
    </tr>
    <tr><td colspan="4"><textarea name="note" id="note" rows="2" cols="50"></textarea></td></tr>
       	<script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('note', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList','BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 40,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script> -->  
    <tr><td colspan="4">&nbsp;</td></tr>
    <?php
	if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['id']; ?>" />
    <?php	
	}
	else if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['deleteid']; ?>" />
	<?php	
	} ?>
    <tr>
		<td colspan="10" align="center"><input type="submit" name="insert" id="insert" value="Submit"  class="submitButton"></td>
    </tr>
</table>    
</form>

</div>
</center>
</div>

<?php
	if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{
		?>
			<script>
				getNotice('edit-' + <?php echo $_REQUEST['id'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getNotice('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>
<?php include_once "includes/foot.php"; ?>