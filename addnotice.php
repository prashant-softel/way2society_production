
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Add New Notice</title>
</head>



<?php include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");
$dbConnRoot =new dbop(true);
$dbConn =  new dbop();
include_once("classes/notice.class.php");
$obj_notice = new notice($m_dbConn, $dbConnRoot);
include_once ("classes/dbconst.class.php");
//print_r($_SESSION);
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$Mobile =$objFetchData->getMobileNumber($_SESSION['unit_id']);



$sComment = "";
$PostDate = "";
$UnitBlock = $_SESSION["unit_blocked"];
$IsNotify = 1;
$sSubject = "";
$sAmount = "";
$sUnitID = "";
$gId = "";
$memberDetails = "";
$sCreationDate = date("d-m-y");

if(isset($_REQUEST['ID']) && $_REQUEST['module'] == 1)
{
	$arReversalData = $obj_notice->getComment($_REQUEST['ID']);
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
#post_noticeto
{
	overflow:scroll;
}

</style>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/jsnotice30102018.js"></script>
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
  		function show_template()
		{
			if(document.getElementById('template_check').checked == true)
			{
				document.getElementById('template_hide').style.display = "table-row";
			}
			else if(document.getElementById('template_check').checked == false)
			{
				document.getElementById('template_hide').style.display = "none";
			}
		}
		
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
					//alert(uploaded_fileName);
					if((val.length > 0))
					{ 
					//var msgText = 'Dear Member, <br /> <br /> Please find attachment : ' + document.getElementById('subject').value + ' <br /> <br /> Thanking you, <br />' + document.getElementById('issueby').value;						
					var msgText = 'Dear Member, <br /> <br /> ';
						if(uploaded_fileName.length != "")
						{
							msgText += 'Please find attachment : ' +  ' <br /> <br />';
						}
						msgText += 'Thanking you, <br />' + document.getElementById('issueby').value;						
					CKEDITOR.instances['description'].setData(msgText);
					$("#description").val(msgText);

					}
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
		uploadText(true);
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
			var ckeditor = '<?php echo $_REQUEST['ckeditor'];?>';
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
		else if(module==4)
		{
			setTimeout(function() 
			{
				alert("Please click on Submit to create Notice.");
			}, 200);
			document.getElementById("groupId").value = '<?php echo $_REQUEST['queryType'];?>';
			document.getElementById("groupId").disabled = 'true';
			<?php
				$memRes = "";
				//echo "In module 4";
				//if($_SESSION['society_id'] == '59' || $_SESSION['society_id'] == '149') // enabled for all society
				{
					$gId = $_REQUEST['queryType'];
					if($gId == "AO")
					{
						$sql = "Select member_id as MemberId, primary_owner_name as other_name from member_main where ownership_status = '1'" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "ACO")
					{
						$sql = "Select mem_other_family_id as MemberId, other_name as other_name from mem_other_family where coowner = '2'" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "AOCO")
					{
						$sql = "Select member_id as MemberId, primary_owner_name as other_name from member_main where ownership_status = '1' union Select mem_other_family_id as MemberId, other_name as other_name from mem_other_family where coowner = '2'" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "AR")
					{
						$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
						//echo $sql;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "ART")
					{
						$sql = "Select mem_other_family_id as MemberId, other_name as other_name from mem_other_family where status = 'Y' union Select tmember_id as MemberId, mem_name as other_name from tenant_member where status = 'Y'" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "ACM")
					{
						$sql = "Select C.member_id as MemberId, M.other_name from mem_other_family as M, commitee as C where M.status = 'Y' and M.mem_other_family_id = C.member_id" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "AT")
					{
						$sql = "Select tenant_id as MemberId, tenant_name as other_name from tenant_module where status = 'Y'";
						$memId = $dbConn->select($sql);
					}
					else if($gId == "AVO")
					{
						$sql = "SELECT mof.mem_other_family_id as MemberId, mof.other_name FROM `mem_other_family` mof, `mem_car_parking` mcp where mcp.member_id = mof.member_id and mcp.status ='Y'" ;
						$memId = $dbConn->select($sql);
					}
					else if($gId == "ALH")
					{
						$sql = "Select L.member_id as MemberId, M.owner_name as other_name from mortgage_details as L, member_main as M where L.Status = 'Y' and L.LienStatus = 'Open' and M.member_id = L.member_id";
						$memId = $dbConn->select($sql);
					}
					else
					{
						$sql = "Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as MemberId, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as MemberId,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`";
						$resultMem = $dbConn->select($sql);
						$sql2 = "Select MemberId from membergroup_members where GroupId = '".$gId."' and Status = 'Y'";
						$resultGMem = $dbConn->select($sql);
						$k=0;
						for($i=0;$i<sizeof($resultGMem);$i++)
						{
							for($j=0;$j<sizeof($resultMem);$j++)
							{
								if($resultGMem[$i]['MemberId'] == $resultMem[$j]['MemberId'])
								{
									$memId[$k]['MemberId'] = $resultMem[$j]['MemberId'];
									$memId[$k]['other_name'] = $resultMem[$j]['other_name'];
									$k=$k+1;
								}
							}
						}
					}
					if(sizeof($memId)>0)
        			{ 
            			$memRes .= "<option value='0'>All</option>";
          			}
					for($i = 0; $i < sizeof($memId); $i++)
					{ 
						$memRes.= "<option value = '".$memId[$i]['MemberId']."'>".$memId[$i]['other_name']."</option>";
					}
				}
				?>
			var memberDetails = " <?php echo $memRes;?>";
			document.getElementById("post_noticeto").innerHTML = memberDetails;
			var memArr=[];
			memArr='<?php echo $_REQUEST['unitid']?>';
		//	alert(memArr[0]);
			var ckeditor = '<?php echo $_REQUEST['ckeditor']; ?>';
			var subject='<?php echo $_REQUEST['title']; ?>';
			var mDate= '<?php echo $_REQUEST['mDate']; ?>';
			var mId='<?php echo $_REQUEST['mId']; ?>';
			var s = document.getElementById("post_noticeto");			
			s.options[0].selected = false;
			for(i=0;i<memArr.length;i++)
			{
				for(k=0;k<s.options.length;k++)
				{
					if(s.options[k].value==memArr[i])
					{
						s.options[k].selected=true;
					}
				}
			}
			document.getElementById("insert").style.display="none";
			document.getElementById("insertMeeting").style.display="table-cell";
			CKEDITOR.instances['description'].setData(ckeditor);
			document.getElementById("notice_type").selectedIndex=4;
			document.getElementById("subject").value=subject;
			document.getElementById("exp_date").value=mDate;
			document.getElementById("mId").value=mId;
		}
	});
	</script>
  
</head>
<?php if(isset($_POST["ShowData"])){?>
<body>
<?php } ?>

<div id="middle">

<br>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
<div class="panel-heading" id="pageheader">Add New Notice</div>
<br>
<center><button type="button" class="btn btn-primary" onClick="window.location.href='notices.php'">Go Back</button></center>
<br>
<center>
<form name="addnotice" id="addnotice" method="post" action="process/notice.process.php" enctype="multipart/form-data" onSubmit="return val();">
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
                <input type="hidden" id = "mId" name = "mId" value = "<?php echo $_REQUEST['mId']?>"/>
          <?php } ?>
     <tr>
     	<td></td>
        <td></td>
        <td></td>
        <td><b>Select Group : </b>
        	<select name="groupId" id="groupId" style=" width:180px;" onChange="changeMember(this.value);">
            	<option value="0">Select the Group</option>
                <option value ="AO">All Owners</option>
                <option value ="ACO">All Co-Owners</option>
               	<option value="AOCO">All Owners & Co-Owners</option>
                <option value="AR">All Residents</option>                                            
                                            <!--<option value="ART">All Residents including Tenants</option>-->
				<option value="ACM">All Committee Members</option>
                <option value="AVO">All Car Owners</option>
                                            <!--<option value="ALH">All Lien Holders</option>-->
               	<option value="AT">All Tenants</option>
                <option value="MHT">Member Having Tenants</option>
                 <option value="1BHK">1-BHK Flats</option>
                <?php echo $combo_unit = $obj_notice->comboboxGroup("Select concat('W',wing_id) as wing_id, concat(wing,' - Wing') as wing from wing where wing != '-' and status = 'Y'",'0');?>
            	<?php echo $combo_unit = $obj_notice->comboboxGroup("Select Id, Name from membergroup where Status = 'Y'",'0');?>
            </select>
        </td>
     </tr>
     <tr>
        <td rowspan="20" valign="middle"></td>
        <th rowspan="20"></th>
        <td rowspan="20"></td>
        <td rowspan="20"><b>Post Notice To :</b></br>
        <select name="post_noticeto[]" id="post_noticeto" multiple="multiple"  style=" width:250px; height:450px;" class="dropdown" >
                <?php echo $combo_unit = $obj_notice->combobox2("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order ", $_REQUEST['unit_id'],'0');
					?>
             </select>
             <input type="text" name="notice_creation_type" id="notice_creation_type" value="2" style="visibility: hidden;">
        </td>
     
      </tr>
      <tr>
        <td><?php echo $star;?></td>
        <td><b>Notice Type</b></td>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="notice_type" id="notice_type">
        		<?php echo $combo_doc = $obj_notice->combobox("select ID, doc_type from document_type",'0');
						?>
        	</select>
        </td>
	</tr>
<tr>
    	<td>&nbsp;
        </td>
    </tr>
    
	<tr id="template_notice">
        <td><?php //echo $star;?></td>
        <td><b>Choose Template</b></td>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="notice_template" id="notice_template" onChange="FetchTemplateData(this)">
        		<?php echo $combo_doc = $obj_notice->comboboxRoot("select id, template_name from document_templates where show_in_notice = 1",'0');
						?>
        	</select>
        </td>
	</tr>


<tr>
    	<td>&nbsp;
        </td>
    </tr>
    
    <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Issued By</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><input type="text" name="issueby" id="issueby" value="<?php echo $_SESSION['name']; ?>" /></td>
    </tr>
<tr>
    	<td>&nbsp;
        </td>
    </tr>
    
    <tr>
        <td valign="top"><?php echo $star;?></td>
        <th><b>Subject</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><textarea name="subject" id="subject" style="width:200px; resize:none;" onKeyDown="limitText(this.form.subject,this.form.countdown,152)"  onKeyUp="limitText(this.form.subject,this.form.countdown,152)" onBlur="SetSMS();"  onkeypress="return ((event.charCode > 57 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8 || event.charCode == 32 || event.charCode == 33 || event.charCode == 35|| event.charCode == 38|| event.charCode == 40 || event.charCode == 41||event.charCode == 44|| event.charCode == 45 || event.charCode == 91|| event.charCode == 93|| event.charCode == 95|| (event.charCode >= 48 && event.charCode <= 57));" ></textarea> <font size="1" color="#CC0000">
        	
				<span id="textCounter" style="display:none;">You have <input type="text"  name="countdown" id = "countdown" size="3" value="100" style="width:35px;text-align:center;border:none;box-shadow:none" readonly /> characters left.</span>
                </font><br>
                <span style="color: red;">Note : Special characters not allowed.</span></td>
                
	</tr>   
    
<tr>
    	<td>&nbsp;
        </td>
    </tr>
    <tr>
        	<td valign="middle"><?php echo $star;?></td>
			<th><b>Post Date</b></th>
            <td>&nbsp;<b>:<b>&nbsp;</td>
			<td><input type="text" name="post_date" id="post_date" value="<?php echo date('d-m-Y');?>"  class="basics" size="10" readonly  style="width:80px;"/></td>
     </tr>
<tr>
    	<td>&nbsp;
        </td>
    </tr>
     
     <tr>
            <td valign="middle"><?php echo $star;?></td>
			<th><b>Expiry Date</b></th>
            <td>&nbsp;<b>:<b>&nbsp;</td>
			<td><input type="text" name="exp_date" id="exp_date"  class="basics" size="10" readonly  style="width:80px;"/></td>
	</tr>  
   
    <tr align="left" style="display:none;">
        <td valign="left"></td>
        <th><b>Notice Type</b></th>
        <td>&nbsp;<b>:<b>&nbsp;</td>
        <td><select name="notice_type_id" id="notice_type_id" style=" width:135px; height:20;">
                <OPTION VALUE="<?php echo NOTICE_TYPE_ADMINISTRATION; ?>" selected>Administration Notice</OPTION>
                <OPTION VALUE="<?php echo NOTICE_TYPE_GENERAL; ?>">General Notice</OPTION>
               <!-- <OPTION VALUE="<?php //echo NOTICE_TYPE_BUY_SELL_RENT; ?>">Buy/Rent/Sell Notice</OPTION> -->
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
        <td><input name="userfile" id="userfile" type="file" onChange="uploadText(true)"/> <a id="noticename" style="visibility:hidden;" target="_blank"> View Attachment </a></td>
    </tr>    
    
    
     <tr align="left" >
    	<td valign="left"></td> 
        <td><b> Send SMS to Members </b></td>  
        <td>&nbsp;<b>:<b>&nbsp;</td>     
        <td><input type="checkbox" name="sms_notify" id="sms_notify" value="1" onChange="SetSMS();">&nbsp;&nbsp;<b>( Charges Apply )</b><div id="smsTest" name="smsTest" style="display:none;"><br/><textarea name="SMSTemplate" id="SMSTemplate" rows="4" cols="50" style="resize::none;float:left;font:bold;"></textarea><br><input type="button" onClick="showTestSMS()" value="Send Test SMS" style="float: right;margin-right: 40%;margin-top: 3%;background-color: cornflowerblue;color: black;border: none;"></div></td>
   </tr>
     <tr>
    	<td>&nbsp;
        </td>
    </tr>
    
    <tr align="left" >
    	<td valign="left"></td> 
        <td><b> Notify Members By Email </b></td>  
        <td>&nbsp;<b>:<b>&nbsp;</td>     
        <td><input type="checkbox" name="notify" id="notify" value="1" checked onChange="uploadText(this.checked);">   </td>
     </tr>
     <tr>
    	<td>&nbsp;
        </td>
    </tr>
    
     <tr align="left">
        <td valign="left"></td> 
            <td ><b> Send Mobile Notification </b></td>  
            <td>&nbsp;<b>:<b>&nbsp;</td>     
            <td><input type="checkbox" name="mobile_notify" id="mobile_notify" value="1" checked></td>
    </tr>
    <tr>
    	<td colspan="4">&nbsp;</td>
    </tr>
   	<tr>
    	<td colspan="4">&nbsp;</td>
    </tr>
    <tr>
    	<td></td>
   		<th colspan="3" style="text-align:right">Save as Template?</th>
        <td style="text-align:left"><input type="checkbox" id="template_check" name="template_check" onClick="show_template();" value="1"/></td>
    </tr>
    <tr>
    	<td colspan="4">&nbsp;</td>
    </tr>
    <tr id="template_hide" style="display:none">
    	<td></td>
        <th colspan="3" style="text-align:right">Show template in Notice? <input type="checkbox" id="notice_check" name="notice_check" value="1"/></th>
        <td></td>
        <th colspan="4" style="text-align:left">Show to Members? <input type="checkbox" id="member_check" name="member_check" value="1"/></th>
   	</tr>
    <tr id="">
    	<td valign="middle"></td>
    	<th style="text-align:left;"></th>
    	<td>&nbsp;  &nbsp;</td>
    	<td colspan="6"><b>Notice Description :</b><textarea name="description" id="description" rows="5" cols="50"></textarea></td>
    </tr>
    
    <!--<tr id=""><td colspan="4"><textarea name="description" id="description" rows="5" cols="50"></textarea></td></tr>-->
       	<script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify,table';
			//CKEDITOR.config.extraPlugins = 'table';
			CKEDITOR.config.entities_processNumerical = true;
			CKEDITOR.config.entities_processNumerical = 'force';
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
    
	<input type="hidden" id="userMobileNo" name="userMobileNo" value="<?php echo $Mobile[0]['mob'] ?>">
	<?php
	if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['id']; ?>" />
    <input type= "hidden" id="NoticeSubject" name="NoticeSubject" value="">
    <?php	
	}
	else if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="<?php echo $_REQUEST['deleteid']; ?>" />
	<?php	
	}
	else
	{ ?>
	<input type="hidden" name="updaterowid" id="updaterowid" value="0" />
	<?php }?>
    <tr>
		<td colspan="10" align="center"><input type="submit" name="insert" id="insert" value="Submit"  class="submitButton">&nbsp;&nbsp;&nbsp;<input type="button" name="resend_notification" id="resend_notification" class="btn btn-primary" value="Resend Notification" onClick="resend_Notification();" style="display:none;"><input type="button" name="insertMeeting" id="insertMeeting" value="Submit"  class="btn btn-primary" onClick="changeMeetingStatus()" style="display:none"></td>
    </tr>
</table>    
</form>

</div>
</center>
</div>
<script>
	//console.log('test');
	document.getElementById("template_notice").style.display = "none";
	<?php if(isset($_REQUEST['module'])) 
		  {
			  	//console.log('test1');
	?>			document.getElementById("template_notice").style.display = "table-row";
	<?php }
	?>
function limitText(limitField, limitCount, limitNum) {
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			} else {
				limitCount.value = limitNum - limitField.value.length;
			}
			if(limitCount.value == 0)
			{
				document.getElementById('textCounter').style.display = "block";
			}
			else if(limitCount.value != 0)
			{
				document.getElementById('textCounter').style.display = "none";
			}
		}
</script>
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