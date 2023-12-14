<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - I Register</title>
</head>
<?php
include_once "ses_set_s.php"; 
include_once("includes/head_s.php");  
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include_once("classes/include/fetch_data.php");
include_once("classes/genbill.class.php");
include_once("classes/utility.class.php");
$obj_Utility =  new utility($dbConn);
$obj_genbill = new genbill($dbConn);
$objFetchData = new FetchData($dbConn);
	$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$memberIDS = $obj_Utility->getMemberIDs($_SESSION['default_year_end_date']);

//ALTER TABLE `member_main` ADD `iid` INT(11) NOT NULL AFTER `member_id`;
//UPDATE `member_main` SET `iid` = `member_id`;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>I - Register</title>

<style>
select.dropdown {
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

.hover
{
	cursor:pointer;
}

.tr_iid
{
	outline:thick;
	outline-color:#000;
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
</style>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jsContributionLedgerDetailed.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/status.js"></script>

	<script type="text/javascript" src="js/jsunit_08112018.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>


<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
<script type="text/javascript" src="javascript/ui.core.js"></script>
<script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script>

    
    <script>
	jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

function ShowSearchElement()
{
		document.getElementById('msgDiv').style.display = 'none';
   		var w =  $('#searchbox').val();
        if (w)
		 {
				if($('#unit_no li:Contains('+w+')').length == 0)
				{
					$('#unit_no li').hide();
					document.getElementById('msgDiv').style.display = 'block';
					document.getElementById('msgDiv').innerHTML = '<font style="color:#F00;"><b>No Match Found...</b></font> ';
				}
				else
				{
					$('#unit_no li').hide();
					$('#unit_no li:Contains('+w+')').show();	
				}
		} 
		else 
		{
			 $('#unit_no li').show();                  
        }
}

function uncheckDefaultCheckBox(id)
{
	if(document.getElementById(id).checked  == true)
	{
		document.getElementsByClassName('chekAll')[0].checked = false;
	}
	else
	{
		var count = 0;
		var checks = document.getElementsByClassName('checkBox');
		checks.forEach(function(val, index, ar) {
			if(ar[index].checked) 
			{
				count++;
			}
		});
		if(count == 0)
		{
			document.getElementsByClassName('chekAll')[0].checked = true;			
		}
		else
		{
			document.getElementsByClassName('chekAll')[0].checked = false;
		}
	}
	
}

function uncheckothers(id)
{
	var checks = document.getElementsByClassName('checkBox');
	checks.forEach(function(val, index, ar) {
		ar[index].checked = false;
	});
}
	</script>
<script>
function Expoort()
{
	//$("#btnExport").click(function(e) {
		document.getElementById('societyname').style.display ='block';	
	  window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	  //e.preventDefault();
	  document.getElementById('societyname').style.display ='none';	
			 
	//});  
}

function add_new_iid()
{
	//document.getElementById('add_new').value = "Update";
	document.getElementById('add_new').disabled = true;
	document.getElementById('tr_insert_unit').style.display = "table-row";
	document.getElementById('tr_insert_iid').style.display = "table-row";
	document.getElementById('tr_insert_name').style.display = "table-row";
	document.getElementById('tr_insert_date').style.display = "table-row";
	document.getElementById('tr_insert').style.display = "table-row";
	//document.getElementById('tr_insert_status').style.display = "table-row";
}

function insert_new_iid()
{
	document.getElementById('add_new').disabled = false;
	var unit_id = document.getElementById('unit_no').value;
	var iid = document.getElementById('insert_iid').value;
	var name = document.getElementById('insert_name').value;
	var date = document.getElementById('insert_date').value;
	
	/*if(document.getElementById('active').checked == true)
	{
		var status = 1;
	}
	else if(document.getElementById('ex-member').checked == true)
	{
		var status = 0;
	}*/
	
	$.ajax({
	url : "ajax/ajaxregisters.ajax.php",
	type : "POST",
	dataType : "json",
	data: {"method": "insert_new_iid", "unit_id":unit_id, "iid":iid, "name":name, "date":date},
	success: function(data)
	{
		//alert(data);
		if(data == 0)
		{
			alert('Ex - Member at IID ' + iid + ' added successfully.');
		}
		else
		{
			alert('IID ' + iid + ' already exists.');
		}
		location.reload();
	}
	});
}

function rename_iid(iid,mid)
{
	if(document.getElementById('Rename_' + mid).innerHTML == "Rename")
	{
		var new_iid = document.getElementById('iid_' + mid).value;
		if(new_iid == iid)
		{
			var new_owner_name = document.getElementById('owner_name_' + mid).value;
			var ownership_date = document.getElementById('ownership_date_' + mid).value;
		}
		document.getElementById('Edit_' + mid).innerHTML = "Edit";
		$.ajax({
		url : "ajax/ajaxregisters.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "rename_iid", "old_iid":iid, "new_iid":new_iid,"member_id":mid, "new_owner_name":new_owner_name, "ownership_date":ownership_date},
		success: function(data)
		{
			//alert(data);
			location.reload();
		}
		});
	}
}

function update_iid(iid,mid)
{
	if(document.getElementById('Edit_' + mid).innerHTML == "Edit" || document.getElementById('Edit_' + mid).innerHTML == "Add Owner Name")
	{
		document.getElementById('td_iid_' + mid).innerHTML = "<input type='text' id='iid_" + mid + "' name='iid_" + mid + "' value='" + iid + "' style='width:70px' />";
		document.getElementById('Edit_' + mid).innerHTML = "Update";
		document.getElementById('Rename_' + mid).style.display = "block";
		document.getElementById('Rename_' + mid).innerHTML = "Rename";
		document.getElementById('cancel_' + mid).style.display = "block";
		if(document.getElementById('name_' + mid).innerHTML == "")
		{
			document.getElementById('td_iid_' + mid).innerHTML = iid;
			document.getElementById('td_name_' + mid).innerHTML = "New Owner Name: <input type='text' id='owner_name_" + mid + "' name='owner_name_" + mid + "' /> Ownership Date: <input type='text' id='ownership_date_" + mid + "' name='ownership_date_" + mid + "' class='basics' />";
			
			setDatePicker('ownership_date_' + mid);
		}
	}
	else if(document.getElementById('Edit_' + mid).innerHTML == "Update")
	{
		var new_iid = document.getElementById('iid_' + mid).value;
		if(new_iid == iid)
		{
			var new_owner_name = document.getElementById('owner_name_' + mid).value;
			var ownership_date = document.getElementById('ownership_date_' + mid).value;
		}
		document.getElementById('Edit_' + mid).innerHTML = "Edit";
		
		$.ajax({
		url : "ajax/ajaxregisters.ajax.php",
		type : "POST",
		dataType : "json",
		data: {"method": "update_iid", "old_iid":iid, "new_iid":new_iid, "new_owner_name":new_owner_name, "ownership_date":ownership_date},
		success: function(data)
		{
			//alert(data);
			location.reload();
		}
		});
	}
}

function setDatePicker(fieldName)
{
	//alert(fieldName);
	$(function() {
		$('#' + fieldName).datepicker({
			dateFormat: "dd-mm-yy",
			yearRange: '-10:+10'
			//minDate: minGlobalCurrentYearStartDate,
			//maxDate: maxGlobalCurrentYearEndDate
			});
		});
}


/*$( document ).ready(function() {
    var name_link = document.getElementsByName('name_link');
	var len = name_link.length;
	for(var i = 0; i < len; i++)
	{
		if(name_link[i] == '')
		{
			name_link[i] = "";
		}
	}*/
	/*
	var coll = document.getElementsByName("check");
	var len = coll.length;
	//alert(len);
	var VoucherNos = new Array();
	var k = 0;
	for(var i = 0; i < len; i++)
	{
		if(coll[i].checked == true)
		{
			var id = coll[i].id;
			//alert(id);
			var VoucherNo = id.substring(6);
			//alert(VoucherID);
			VoucherNos[k] = VoucherNo;
			k++;
		}
	}
	*/
//});
</script>

<script type="application/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-10:+10', // Range of years to display in drop-down,
        })});
		
	</script>
</head>

<body>
<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">I - Register</div>
<br />
<br />
	<form name="accounting_report" id="accounting_report" method="post" action="i_register.php" target="_blank">
        <center>
        <table>
        	<tr>
            	<td style="font-size:12px; text-align:left"><i class="fa fa-circle fa-1x" style="font-size:10px;font-size:1vw;color:green;"></i></td>
                <td>&nbsp; &nbsp;</td>
                <td style="font-size:12px; text-align:left">Active Member</td>
            </tr>
            <tr>
                <td style="font-size:12px; text-align:left"><i class="fa fa-circle fa-1x" style="font-size:10px;font-size:1vw;color:red;"></i></td>
                <td>&nbsp; &nbsp;</td>
                <td style="font-size:12px; text-align:left">Ex - Member</td>
            </tr>
        </table>
        <table width="100%" style="border:1px solid; border-color:#000" rules="all"> <!--style="border:1px solid; border-color:#000"-->
        	<tr>
            	<th width="5%" style="font-size:14px; border:1px solid; border-color:#000;"><center>Ownership Status</center></th>
                <th width="5%" style="font-size:14px; border:1px solid; border-color:#000; border-left:none;"><center>Flat No.</center></th>
            	<th width="5%" style="font-size:14px; border:1px solid; border-color:#000; border-left:none; border-right:none"><center>IID</center></th>
                <th width="10%" style="font-size:14px; border:1px solid; border-color:#000; border-left:none; "><center>Edit</center></th>     	
                <th width="75%" style="font-size:14px; border:1px solid; border-color:#000; border-left:none;"><center>Owner Name</center></th>
            </tr>
            <?php 
			//echo $sql01 = "select unit.unit_no, unit.unit_id, member_main.owner_name, member_main.member_id from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' and  member_main.member_id IN ($memberIDS) ORDER BY member_main.member_id ASC";
			$sql01 = "SELECT mm.member_id as mid, mm.`iid`, mm.`unit`, mm.`owner_name`, u.`unit_id`, u.`unit_no`,mm.`ownership_status` FROM `member_main` mm, `unit` u WHERE u.`unit_id` = mm.`unit` AND mm.`society_id` = '".$_SESSION['society_id']."' ORDER BY mm.`iid`";
			$sql11 = $m_dbConn->select($sql01);
			
			for($z=0;$z<sizeof($sql11);$z++)
			{
				
				?>
                                
                <tr id="tr_iid_<?php echo $sql11[$z]['mid']; ?>" class="tr_iid">
                	 <td style="font-size:12px; border:1px solid; border-color:#000; border-top:none; text-align:center"><?php if($sql11[$z]['ownership_status'] == 1) { ?><i class="fa fa-circle fa-1x" style="font-size:10px;font-size:1vw;color:green;"></i><?php } else { ?><i class="fa fa-circle fa-1x" style="font-size:10px;font-size:1vw;color:red;"><?php } ?></i></td>            
                     <td style="font-size:12px; border:1px solid; border-color:#000; border-left:none; border-top:none"><center><a class="hover" onclick="viewMemberStatus(<?php echo $sql11[$z]['unit_id']; ?>);"><?php echo $sql11[$z]['unit_no']; ?></a></center></td>
                     <td style="font-size:12px; border:1px solid; border-color:#000; border-top:none; border-left:none; border-right:none; text-align:center" id="td_iid_<?php echo $sql11[$z]['mid']; ?>"><?php echo $sql11[$z]['iid']; ?></td>
                   	 <td style="font-size:12px; border:1px solid; border-color:#000;  border-left:none; border-top:none;"><center><a class="hover" id="Edit_<?php echo $sql11[$z]['mid']; ?>" onclick="update_iid(<?php echo $sql11[$z]['iid']; ?>,<?php echo $sql11[$z]['mid']; ?>);"><?php if ($sql11[$z]['owner_name'] != '') { ?>Edit<?php } else { ?>Add Owner Name<?php } ?></a><a id="Rename_<?php echo $sql11[$z]['mid']; ?>" class= "hover" onclick="rename_iid(<?php echo $sql11[$z]['iid']; ?>,<?php echo $sql11[$z]['mid']; ?>);" style="display:none">Rename</a><a class="hover" onclick="location.reload();" style="display:none" id="cancel_<?php echo $sql11[$z]['mid']; ?>">Cancel</a></center></td>                    
                     <td style="font-size:12px; border:1px solid; border-color:#000; border-left:none; border-top:none" id="td_name_<?php echo $sql11[$z]['mid']; ?>" class="name_link" >&nbsp; &nbsp;<a id="name_<?php echo $sql11[$z]['mid']; ?>" href="i_register.php?iid=<?php echo $sql11[$z]['iid']; ?>" target="_blank"><?php echo $sql11[$z]['owner_name']; ?></a></td>
                 </tr>
                <?php
			}
			?>              
            
<!--            <tr> <td colspan="3"> <br /> </td> </tr>
            <tr align="left">
                <td valign="middle"></td>
                <td><b>Unit No</b></td>
                <td>&nbsp; : &nbsp;</td>
                
                <td>
                 <div class="input-group input-group-unstyled" style="width:355px; ">
    				<input type="text" class="form-control" style="width:355px; height:30px;"  id="searchbox" placeholder="Search Unit No Or Member Name"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
    			</div>
            	<div style="overflow-y:scroll;overflow-x:hidden;width:355px; height:150px; border:solid #CCCCCC 2px;" name="unit_no[]" id="unit_no" >
                	<p id="msgDiv" style="display:none;"></p>
                	<?php //echo $combo_unit = $obj_genbill->comboboxForLedgerReport("select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "'    and  member_main.member_id IN (SELECT `member_id` FROM (select  `member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit) Group BY unit.unit_id ORDER BY unit.sort_order ASC",0,'All','0');
					//echo $sql = "select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' and  member_main.member_id IN ($memberIDS) ORDER BY unit.sort_order ASC";
					//echo $combo_unit = $obj_genbill->comboboxForIRegister($sql,0,'All','0');?>
				</div>
            </td>
                <td  align="center">                               	                         
                    &nbsp;&nbsp;
                    <input type="submit" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  /> 
                 </td>
              
           </tr>
           <tr><td colspan="6"><br /></td></tr>
-->        </table>

			<table width="100%" align="center">
            	<tr style="text-align:center">
                	<td id="td_insert_button" colspan="2"><input type="button" id="add_new" name="add_new" value="Add Ex - Member" onclick="add_new_iid();" class="btn btn-primary" /></td>
                </tr>
                <tr>
                	<td colspan="2"><br /></td>
                </tr>
               	<tr id="tr_insert_unit" style="display:none">
                	<td width="50%" style="text-align:right">Unit No: </td>
                    <td width="50%"><select name="unit_no" id="unit_no" ><?php echo $combo_unit = $obj_genbill->combobox("SELECT `unit_id`, `unit_no` FROM `unit` WHERE `society_id` = '".$_SESSION['society_id']."'","0","Please Select","0"); ?></select></td>
                </tr>                
                <tr id="tr_insert_iid" style="display:none">
                	<td style="text-align:right">IID: </td>
                    <td><input type="text" id="insert_iid" name="insert_iid" size="10" /></td>
                </tr>
                <tr id="tr_insert_name" style="display:none">
                	<td style="text-align:right">Owner Name: </td>
                    <td><input type="text" id="insert_name" name="insert_name" size="10" /></td>
                </tr>
                <tr id="tr_insert_date" style="display:none">
                	<td style="text-align:right">Ownership Date: </td>
                    <td><input type="text" id="insert_date" name="insert_date" size="10" class="basics" /></td>
                </tr>
                <!--<tr id="tr_insert_status" style="display:none">
                	<td style="text-align:right">Ownership Status: </td>
                    <td><input type="radio" name="ownership_status" id="active" value="Active" />&nbsp; Active &nbsp; &nbsp; &nbsp;<input type="radio" name="ownership_status" id="ex-member" value="Ex - Member" checked="checked" />&nbsp; Ex - Member </td>                    
                </tr>	-->		
                <tr id="tr_insert" style="display:none">
                	<td colspan="2" style="text-align:center"><input type="button" id="insert_button" name="insert_button" value="Insert" onclick="insert_new_iid();" class="btn btn-primary"/></td>
                </tr>	
            </table>
			
		</center>
                <input type="text" style="visibility:hidden" name="AllowExport" id="AllowExport" value="<?php echo $_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE]; ?>" />

     </form>


<div id='showTable' style="font-weight:lighter;">
</div>



</div>
<?php include_once "includes/foot.php"; ?>
<div id="openDialogOk" class="modalDialog" >
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>