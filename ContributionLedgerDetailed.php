<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Member Bill Register</title>
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Member Bill Register</title>

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
<script type="text/javascript" src="js/jsContributionLedgerDetailed.js?08092023"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
    
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
			document.getElementsByClassName('chekAll')[0].checked = true;
	}
	
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
</script>
</head>

<body>
<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">Member Bill Register</div>
<br />
<br />
	<form name="accounting_report" id="accounting_report" method="post">
        <table>
        <tr> <td valign="middle"></td>
                <td><b>Bill Type</b></td>
                <td>&nbsp; : &nbsp;</td>
                <td>
                <select id="billType" name="billType">
                <option value="0">Maintenace</option>
                <option value="1">Supplymentry</option>
                <option value="2">Invoces</option>
                </select></td> </tr>
            <tr> <td colspan="3"> <br /> </td> </tr>
            <tr align="left">
                <td valign="middle"></td>
                <td><b>Unit No</b></td>
                <td>&nbsp; : &nbsp;</td>
                <!--<td>
                	<select name="unit_no[]" id="unit_no" multiple="multiple" style=" height:150px;overflow-y:visible;overflow-y:scroll;width:300px;padding: 15px 15px 15px 15px;">
						<?php //echo $combo_unit = $obj_genbill->combobox("select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' ORDER BY CAST(SUBSTRING(unit.unit_no,LOCATE(' ',unit.unit_no)+1) AS SIGNED) ",0,'All','0');?>
                     </select>
                    
                </td>-->
                <td>
                 <div class="input-group input-group-unstyled" style="width:355px; ">
    				<input type="text" class="form-control" style="width:355px; height:30px;"  id="searchbox" placeholder="Search Unit No Or Member Name"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
    			</div>
            	<div style="overflow-y:scroll;overflow-x:hidden;width:355px; height:150px; border:solid #CCCCCC 2px;" name="unit_no[]" id="unit_no" >
                	<p id="msgDiv" style="display:none;"></p>
                	<?php //echo $combo_unit = $obj_genbill->comboboxForLedgerReport("select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "'    and  member_main.member_id IN (SELECT `member_id` FROM (select  `member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit) Group BY unit.unit_id ORDER BY unit.sort_order ASC",0,'All','0');
					echo $combo_unit = $obj_genbill->comboboxForLedgerReport("select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "'    and  member_main.member_id IN ($memberIDS) ORDER BY unit.sort_order ASC",0,'All','0');?>
				</div>
            </td>
                <td  align="center">    
                  &nbsp;&nbsp;                                   	                         
                     <input type="checkbox" name="ignore_zero" id="ignore_zero" value="1" />
                       &nbsp;&nbsp;        
              </td>
               <td  align="center"   style="padding-top:3px;"> 
                 <b>Ignore Zero Values</b>
                &nbsp;&nbsp;
              </td>
                <td  align="center">                               	                         
                    &nbsp;&nbsp;
                    <input type="button" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  onclick="FetchBillRegisterSummary(<?php echo $_SESSION['society_id']?>);" /> 
                 </td>
                <td>
                	
                	<?php 
					
					//if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1)
					//{?>
                    <input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()"  style="display:none;"/>
                	 <?php 
					 //}
					 //else
					//{?>
							<input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()" style="display:none;visibility:hidden;"/>
					<?php 
					//}
					?>	
                </td>
                <td>
                <?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
                 <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;"/>
                  <?php //}
					//else
					//{?>
						  <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;visibility:hidden;"/>
					<?php //}?>	
                </td>
              
           </tr>
           <tr><td colspan="6"><br /></td></tr>
           <!--<tr align="right">
           <td colspan="6" align="left"><font>(Note : You can select multiple units by pressing ctrl key on keyboard + unit no.)</font></td>
           </tr> -->
            
        </table>
        <input type="text" style="visibility:hidden" name="AllowExport" id="AllowExport" value="<?php echo $_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE]; ?>" />
     </form>


<div id='showTable' style="font-weight:lighter;">
</div>



</div>
<?php include_once "includes/foot.php"; ?>