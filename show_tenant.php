<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Leave & license History</title>
</head>
<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php");
//include_once("classes/home_s.class.php");
include_once "classes/include/dbop.class.php";
include_once("classes/dbconst.class.php");
include_once("classes/tenant.class.php");
include_once("classes/include/fetch_data.php");
include_once("classes/utility.class.php");

$obj_Utility =  new utility($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_tenant = new tenant($m_dbConn);
$details = $obj_tenant->getRecords();
//print_r($details);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Member Bill Register</title>

<style>
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
<script type="text/javascript" src="js/tenant_20190424.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script>
function Expoort()
{
	document.getElementById('societyname').style.display ='block';	
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	document.getElementById('societyname').style.display ='none';	
}
</script>
</head>

<body>
<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">Leave & license History</div>
<br />
<br />
<center>
	<form name="show_tenant" id="show_tenant" method="post">
        <table style="border:1px solid #ddd; border-radius:10px;padding:10px;">
            <tr> <td colspan="3"> <br /> </td> </tr>
            <tr align="left">
                <td valign="middle"></td>
                <td valign="middle"><b>Select</b></td>
                <td valign="middle">&nbsp; : &nbsp;</td>
                <td valign="middle"><select id="TenantList" name="TenantList" style="width:8vw;" value= <?php echo $_REQUEST['TenantList']?>>
                <option value="0">All</option>
                <option value="1">Lease Active </option>
                <option value="2">Lease Expired </option>
                <option value="3">Lease Expiring in one month</option>
               	<option value="4">Waiting For Approval</option>
                <option value="5">Lease Expired but not renewed</option>
             	
                </td>
               
                <td  align="center">                               	                         
                    &nbsp;&nbsp;
                    <input type="button" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  onclick="FetchTenantHistory(<?php echo $_SESSION['society_id']?>);" /> 
                 </td>
                <td>
                	
                	<?php 
					
					if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1)
					{?>
                    <input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()"  style="display:none;"/>
                	 <?php 
					 }
					 else
					{?>
							<input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()" style="display:none;visibility:hidden;"/>
					<?php 
					}
					?>	
                </td>
                <td>
                <?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
                 <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;"/>
                  <?php }
					else
					{?>
						  <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;visibility:hidden;"/>
					<?php }?>	
                </td>
              
           </tr>
           <tr><td colspan="6"><br /></td></tr>
          </table>
           <input type="text" style="visibility:hidden" name="AllowExport" id="AllowExport" value="<?php echo $_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE]; ?>" />
       </form>

</center>
<div id='showTable' style="font-weight:lighter;">
<input type="hidden" name="deleteid" value="deleteid" value="<?php echo $_REQUEST['tenant_id']?>"/>
</div>



</div>
<script>
$( document ).ready(function() {
//alert("GetData");
    document.getElementById('TenantList').value='<?php echo $_REQUEST['TenantList']?>';
	FetchTenantHistory(<?php echo $_SESSION['society_id']?>);
});
</script>
<?php include_once "includes/foot.php"; ?>