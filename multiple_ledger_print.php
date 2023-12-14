<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Multiple Ledger Report</title>
</head>

<?php
set_time_limit(0);
include_once "ses_set_s.php"; 
include_once("includes/head_s.php"); 
include_once "classes/include/dbop.class.php"; 
include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
$m_dbConn = new dbop();
 ?>
<?php
include_once("classes/view_ledger_details.class.php");
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn);
?>
<html>

<style>
#ledgerfetch
{
	display:none;
}
	body{
		width: 100%;
	}
	div.maindivToExport table {
    	border: 1px solid #cccccc;
		border-collapse: collapse;
		text-align:left;
	}
	div.maindivToExport th{
   		text-align:left;
		border: 1px solid #cccccc;
		border-collapse: collapse;
		padding-top:0px;
		padding-bottom:0px;
	}	
	
		div.maindivToExport td {
   		text-align:left;
		border: 1px solid #cccccc;
		border-collapse: collapse;
		padding-top:0px;
		padding-bottom:0px;
	}	
	div.maindivToExport 
	{
	}
	
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
		
	}
	
	table tr{page-break-inside: avoid;}
	table { page-break-inside:auto; }
   	div  table tr    { page-break-inside:avoid; page-break-after:auto }
    div  table thead { display:table-header-group }
   div  table tfoot { display:table-row-group } 
	div  table tr td{ page-break-inside:avoid; }
	
	p{ font-size: 4vmin; margin: 0px; padding: 0px;}
</style>
<script type="text/javascript" src="js/multiple_ledger_print.js?20230823a"></script>
      
<script>
$ledgertodisplay = "";
function PrintPage(divId) 
{
	var printContents =  document.getElementById(divId).innerHTML;
     var originalContents = document.body.innerHTML;
	document.body.innerHTML = printContents;
     window.print();

    document.body.innerHTML = originalContents;
}
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
		
	function  ExportFileToExcel()
	{ 
		 
		 var myBlob =  new Blob( [$("#maindivToExport").html()] , {type:'application/vnd.ms-excel'});
		 var url = window.URL.createObjectURL(myBlob);
		 
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "Memberledger.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);
		
		//***Below commented code not working for large file so we using blob 
		//window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html())); 
		//header('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html())); 
		e.preventDefault();
		location.reload();
	
	}
	
	function setFontSize()
	{
		if(document.getElementById('size').value == "")
		{
				document.getElementById('innerDiv').style.fontSize = "10px";		
		}
		else
		{
			document.getElementById('innerDiv').style.fontSize = document.getElementById('size').value + "px";	
		}
		document.getElementById('FontDiv').style.fontSize="22px";
		
	}
	
	 </script>
<title>Multiple Ledger Report</title>
</head>
<body id="bodyid">
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader"> Multiple Ledger Report</div>
    <br />

 
 <center>
  <div >
    <form method="post"  action="multiple_ledger_print.php?ck" onSubmit="return setArray();" class="no-print"  >
    <div style="border:1px solid  #666; width:74%;  border-radius: 15px;">
       <table  cellpadding="10px" cellspacing="10px">
            <tr style="border: none;"><td style="border: none;"><br></td></tr>
             <tr align="left" style="border: none;">
                <td style="border: none;">Select Ledgers To Display</td>
                </tr>
                <tr>
                <td  style="border: none;">
                     <?php $attr = 'selected="selected"'; ?>
        	   <select name="ledger_type" id="ledger_type" value="<?php echo $_REQUEST['ledger_type'];?>" onChange = "view(this.value)">
                        <OPTION VALUE="0"  <?php echo $_REQUEST['ledger_type'] == 0 ? $attr : ''; ?>>All</OPTION>
                        <OPTION VALUE="1"  <?php echo $_REQUEST['ledger_type'] == 1 ? $attr : ''; ?>>Member Ledgers</OPTION>
                         <OPTION VALUE="2"  <?php echo $_REQUEST['ledger_type'] == 2 ? $attr : ''; ?>>General Ledgers</OPTION>
                         <OPTION VALUE="3"  <?php echo $_REQUEST['ledger_type'] == 3 ? $attr : ''; ?>>Bank Account Ledgers</OPTION>
                         <OPTION VALUE="4"  <?php echo $_REQUEST['ledger_type'] == 4 ? $attr : ''; ?>>Cash Account Ledgers</OPTION>
                         <OPTION VALUE="5"  <?php echo $_REQUEST['ledger_type'] == 5 ? $attr : ''; ?>>Liability Ledgers</OPTION>
                         <OPTION VALUE="6"  <?php echo $_REQUEST['ledger_type'] == 6 ? $attr : ''; ?>>Asset Ledger</OPTION>
                         <OPTION VALUE="7"  <?php echo $_REQUEST['ledger_type'] == 7 ? $attr : ''; ?>>Income Ledger</OPTION>
                         <OPTION VALUE="8"  <?php echo $_REQUEST['ledger_type'] == 8 ? $attr : ''; ?>>Expense Ledger</OPTION>
                    </select>
                </td>
               
                </tr>
                
                  <tr id="ledgerfetch">
                 <td>                     	
                       <!-- <ul id="unit" data-role="listview" style="border: solid black 1px;" data-autodividers="false" data-filter="false" data-inset="true" data-theme="a" data-divider-theme="a"> -->
						<select name="ledger" id="ledger" multiple="multiple"  style="width: 270px;height: 200px;overflow-x: auto;" class="dropdown">                   
                      
                        </select>
							<p style="font-size:10px;padding-top:5px">(Note : You can select multiple units by pressing ctrl key + ledger.)</p>
                       <!-- </ul> -->
                   	</td>
                   <td align="center" style="padding:15px;">
                    	
                        <input type="button" name="addall" id="addall" value="Add All" onClick="addAllLedger();" style = "width:92px"/> <br /> <br />
                        <input type="button" name="add" id="add" value="Add Selected" onClick="addLedger();" style = "width:92px" /> <br /> <br />
                          <input type="button" name="remove" id="remove" value="Remove Selected" onClick="removeLedger();" style = "width:92px" /> <br /> <br />
                        <input type="button" name="addall" id="addall" value="Remove All" onClick="removeAllLedger();" style = "width:92px" /> 
                      
                    </td>
                      <td>
                     <select name="selectedledger" id="selectedledger" multiple="multiple"  style="width: 270px;height:200px;overflow-x: auto;" class="dropdown"> 
                        
                        </select> 
                       <td>
                    <input type="hidden" name="ledgerid" value="" id="ledgerid">                                        
                    </td>                          
                    </td>          	
                 </tr>
                
                <tr>
                
                <td  style="border: none;">
                <table>
                <tr>
                <td>
                From </td>
                 <td  style="border: none;">&nbsp; : &nbsp;</td>
                <td  style="border: none;"><input type="text" name="from_date" id="from_date" size="10"  class="basics"  style="width: 71px;" value="<?php if($_REQUEST['from_date'] <> ""){echo $_REQUEST['from_date'];}else{echo getDisplayFormatDate($_SESSION['default_year_start_date']);}?>"/></td>
                </tr>
            
              
                <tr>
                 <td  style="border: none;padding-top:5px">To </td>
                 <td  style="border: none;padding-top:5px""> &nbsp; : &nbsp; </td>
                 <td  style="border: none;padding-top:5px""> <input type="text" name="to_date" id="to_date"  class="basics" size="10"   style="width: 71px;"  value="<?php  if($_REQUEST['to_date'] <> ""){echo $_REQUEST['to_date'];}else{echo getDisplayFormatDate($_SESSION['default_year_end_date']); }?>"  width="10px;"/> </td>
                
               </tr>
               <tr>
                 <td  style="border: none;padding-top:5px">Font Size (In pixel) </td>
                 <td  style="border: none;padding-top:5px">&nbsp; : &nbsp;</td>
                <td  style="border: none;padding-top:5px"> <input type="number" name="size" id="size" min="8"  value="<?php echo $_REQUEST['size']; ?>" style="width:80px;" onBlur="setFontSize();" onKeyUp="setFontSize();"/></td>
               </tr>
               </table>
               </td>
               <td>
               </td>
                <td>
                <table id = "tbl_showheader1"> 
                <tr>
              <td>
               <input type="checkbox" name="show_header" id="show_header" value="1" <?php if($_REQUEST['show_header']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td>
                <td style="padding-top: 2px;">Show Header  </td>
              
               </tr>
               <tr>
                <td><input type="checkbox" name="show_zero" id="show_zero" value="1"  <?php if($_REQUEST['show_zero']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td>
                <td  style="padding-top: 2px;">Show Zero  </td>
                </tr>
                <tr>
                <td><input type="checkbox" name="add_pg_br" id="add_pg_br" value="1"  <?php if($_REQUEST['add_pg_br']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td>
                <td  style="padding-top: 2px;">Each Report On Seperate Page(For Print)  </td>
                </tr>
                <tr>
                <td><input type="checkbox" name="merge" id="merge" value="1"  <?php if($_REQUEST['merge']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td>
                <td  style="padding-top: 2px;">Combined Contribution Ledgers  </td>
                </tr>
               
                <tr >
                <td><input type="checkbox" name="exp_group" id="exp_group" value="1"  <?php if($_REQUEST['exp_group']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td>
                <td  style="padding-top: 2px;">Group by Vendor</td>
                </tr>
            </tr>
               <tr style="border: none;"><td style="border: none;"><br></td></tr>
                 </table>
               </td>
               </tr>
              
               
               
                 </table>
                   <table id = "tbl_showheader" style="padding-left: 30px;"> 
                <tr>
               <td><input type="checkbox" name="show_header" id="show_header" value="1" <?php if($_REQUEST['show_header']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td><td style="padding-top: 2px;">Show Header &nbsp;&nbsp;&nbsp; </td>
                <td><input type="checkbox" name="show_zero" id="show_zero" value="1"  <?php if($_REQUEST['show_zero']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td><td  style="padding-top: 2px;">Show Zero &nbsp;&nbsp;&nbsp; </td>
                <td><input type="checkbox" name="add_pg_br" id="add_pg_br" value="1"  <?php if($_REQUEST['add_pg_br']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td><td  style="padding-top: 2px;">Each Report On Seperate Page(For Print) &nbsp;&nbsp;&nbsp; </td>
                <td><input type="checkbox" name="merge" id="merge" value="1"  <?php if($_REQUEST['merge']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td><td  style="padding-top: 2px;">Combined Contribution Ledgers &nbsp;&nbsp;&nbsp; </td>
               <td ><input type="checkbox" name="exp_group" id="exp_group" value="1"  <?php if($_REQUEST['exp_group']=='1'){echo "checked";}?>/>&nbsp;&nbsp;&nbsp;</td><td  style="padding-top: 2px;">Group by Vendor</td>
            </tr>
               <tr style="border: none;"><td style="border: none;"><br></td></tr>
                 </table>
                 </div>
                <table>
                <tr   style="border: none;"><td><br></td></tr>
                <tr   style="border: none;">
                <?php  
				if(isset($_GET['ck']))
				{
			
				?>
                
                <td style="border: none;visibility:hidden;"  id="btnSubmit"><input type="submit"  name="submit"  id="submit" value="Fetch" class="btn btn-primary"  style="box-shadow: none;" /> </td>
                <td></td>
                <?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1)
				{?>
                <td style="border: none;visibility:visible;"  id="btnPrint" ><INPUT TYPE="button" id="Print" onClick="PrintPage('maindivToExport')" name="Print!" value="Print"   style="box-shadow: none;"  class="btn btn-primary"/></td>
                  <td></td>
                <td style="border: none;visibility:visible;" id="btnExportToExcel"><INPUT TYPE="button" id="ExportToExcel" onClick="ExportFileToExcel()" name="ExportToExcel" value="ExportToExcel"   style="box-shadow: none;"  class="btn btn-primary"/></td>
              
        	<?php }}
				else
				{
			 ?>
             		 <td style="border: none;visibility:hidden;"  id="btnSubmit" colspan="5"><input type="submit"  name="submit"  id="submit" value="Fetch" class="btn btn-primary"   style="box-shadow: none;" /> </td>
             <?php }?>
              </tr> 
    </table>
    </form>	
  </div>
  </center>
 <br>
   
 <?php 
 if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Fetch' &&  $_REQUEST['ledger_type'] <> "")
{
	if($_REQUEST['ledger_type'] == "0")
	{
	$ledgerIDArray = $obj_ledger_details->fetchLedgerIDArray($_REQUEST['ledger_type']);
	}
	else
	{
		$ledgerdecode = json_decode($_REQUEST['ledgerid']);
		 $ledgerdetail = "";
		for($i = 0; $i < sizeof($ledgerdecode); $i++)
          	{
				$ledgerdetail .= $ledgerdecode[$i] . ",";						
		    }
			
			$ledgerdetail=substr($ledgerdetail,0,length-1);
			$ledgerIDArray = $obj_ledger_details->fetchLedgerIDArray1($ledgerdetail);
	}
}

$date = getDisplayFormatDate($_REQUEST['from_date']);		
?>

<div id="maindivToExport" style="font-size:10px;" >
<div id = "innerDiv">
<?php
$GroupID = '';
for($m = 0;$m < sizeof($ledgerIDArray); $m++)
{
$LedgerGroupID = $ledgerIDArray[$m]['group_id'] ;
$LedgerCategoryID = $ledgerIDArray[$m]['categoryid'] ;	
$data = array();

if($_REQUEST['merge']=='1')
{
	$get_details = $obj_ledger_details->details($ledgerIDArray[$m]['group_id'],$ledgerIDArray[$m]['id'],getDBFormatDate( $_REQUEST['from_date']) ,getDBFormatDate($_REQUEST['to_date']),true,true);
}
else if($_REQUEST['exp_group']=='1')
{
	//echo "Inside exp 1";
	$get_details = $obj_ledger_details->details1($ledgerIDArray[$m]['group_id'],$ledgerIDArray[$m]['id'],getDBFormatDate( $_REQUEST['from_date']) ,getDBFormatDate($_REQUEST['to_date']),true,true);
}
else
{
	$get_details = $obj_ledger_details->details($ledgerIDArray[$m]['group_id'],$ledgerIDArray[$m]['id'],getDBFormatDate( $_REQUEST['from_date']) ,getDBFormatDate($_REQUEST['to_date']),true,false);	
}

if($GroupID == '' || $GroupID <> $ledgerIDArray[$m]['group_id'])
{
	$GroupID = $ledgerIDArray[$m]['group_id'];
	if($GroupID == LIABILITY)
	{
		echo "<center><p><b><font color='#0066CC' id='FontDiv' >LIABILITY</font></b></p></center>";	
	}
	else if($GroupID == ASSET)
	{
		echo "<center><p><b><font color='#0066CC'  id='FontDiv'>ASSET</font></b></p></center>";
	}
	else if($GroupID == EXPENSE)
	{
		echo "<center><p><b><font color='#0066CC' id='FontDiv'>EXPENSE</font></b></p></center>";
	}
	else
	{
		echo "<center><p><b><font color='#0066CC'  id='FontDiv'>INCOME</font></b></p></center>";
	}
}
if($ledgerIDArray[$m]['group_id'] == LIABILITY|| $ledgerIDArray[$m]['group_id'] == ASSET)
{		
	if($date <> "")
	{
		$res = $m_objUtility->getOpeningBalance($ledgerIDArray[$m]['id'],$date);
		/*$arLedgerParentDetails = $m_objUtility->getParentOfLedger($ledgerIDArray[$m]['id']);
		if(!(empty($arLedgerParentDetails)))
		{
			$LedgerGroupID = $arLedgerParentDetails['group'];
			$LedgerCategoryID = $arLedgerParentDetails['category'];
		}*/
		
		
		if($res <> "")
		{
			if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT)
			{
				$data[0] = array("id" => $ledgerIDArray[$m]['id'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "particular_category_name" => $res['Ledger_Category'], "Debit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1,"owner_name" =>"");
			}
			else
			{
				$data[0] = array("id" => $ledgerIDArray[$m]['id'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "particular_category_name" => $res['Ledger_Category'] , "Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1,"owner_name" =>"");		
			}
			if($get_details <> "" && sizeof($get_details) > 0 && is_array($get_details) && !empty($get_details))
			{
				/*for($i = 0 ; $i < sizeof($get_details); $i++)
				{
					$data[$i + 1] = $get_details[$i];
				}*/
				 if(is_array($get_details) & !empty($get_details))
                {
					$data = array_merge($data,$get_details);
				}
			}
		}
		else
		{
			$data = $get_details;	
		}
	}
}
else
{
	$data = $get_details;		
}
//print_r($data);
//$IsCreditor = $obj_ledger_details->IsCreditor($ledgerIDArray[$m]['id']);
$IsCreditor = false;
if($LedgerGroupID == LIABILITY && $ledgerIDArray[$m]['payment'] ==1)
{
	$IsCreditor = true;
}

$BalanceAmt=0;
$DebitTotal = 0;
$CreditTotal = 0;
$CreditAmt = 0;
$DebitAmt = 0;

if(sizeof($data) > 0 && ($_REQUEST['show_zero'] == '1')  ||  (sizeof($data) > 1 || (sizeof($data) == 1 && ($data[0]['Credit'] <> 0 ||  $data[0]['Debit'] <> 0))))
{?>
 
  <div  style="border: 1px solid #cccccc;border-bottom:none; text-align:center;">
         
     <center>        
<?php   
if(isset($_REQUEST['show_header']) && $_REQUEST['show_header'] == '1')
{?> 
  <table   border="0" width="100%"   style="text-align:center;border: 0px solid #cccccc; border-collapse:collapse; padding-bottom:0px;page-break-inside:avoid;" class="AppyCssFont" > 
  <!--<tbody>-->
  		<tr   style="text-align:center; border-bottom:none;"><td  style="text-align:center;border: 0px solid #cccccc;"  colspan="6"><b><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></b></td></tr>
        <tr   style="text-align:center; border-bottom:none;"><td  style="text-align:center;border: 0px solid #cccccc;"   colspan="6"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
                    {
                        echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
                    }
                    ?>
        </td></tr>
        <tr  style="text-align:center; border-bottom:none;"><td  style="text-align:center; border-bottom:none;border: 0px solid #cccccc;"   colspan="6"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></td></tr>
        <tr  style="text-align:center; border-bottom:none;"><td  style="text-align:center; border-bottom:none;border: 0px solid #cccccc;"   colspan="6"><b><?php if($_REQUEST['ledger_type'] == 0){echo "Ledger  Report";}else if($_REQUEST['ledger_type'] == 1){echo "Member Ledger  Report";}else if($_REQUEST['ledger_type'] == 2){echo "General Ledger  Report";} ?></b></td></tr>
        <tr  style="text-align:center; border-bottom:none;"><td style="text-align:center; border-bottom:none;border: 0px solid #cccccc;"   colspan="6">From<?php echo '   '.$_REQUEST['from_date'].' ';?> To<?php echo '   '.$_REQUEST['to_date'];?></td></tr>
  <!--</tbody>-->
  </table>    
  <?php }
  if(isset($_REQUEST['show_header']) && $_REQUEST['show_header'] == '1')
	{?> 
      <table  width="100%"   style="text-align:center;border: 1px solid #cccccc; border-collapse:collapse;border-bottom:none; border-left:none;border-right:none;padding-bottom:0px;" >
       <tbody>
        <tr style="text-align:left;border-bottom:none;"><td  style="text-align:left;border: 0px solid #cccccc; border-bottom:none;"   colspan="6"><b><?php echo $data[0]['Particular']; if(!empty($data[0]['particular_category_name'])){ echo ' ( '.$data[0]['particular_category_name'].' )'; } if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}?></b></td></tr>
      </tbody>
        
      </table>     
 <?php }
 		else
		{
 ?>
 	 <table   border="0" width="100%"   style="text-align:center;border: 0px solid #cccccc; border-collapse:collapse;padding-bottom:0px;" >
      <tbody>
  			<tr style="text-align:left; border-bottom:none;"><td  style="text-align:left;border: 0px solid #cccccc; border-bottom:none;"   colspan="6"><b><?php echo $data[0]['Particular']; if(!empty($data[0]['particular_category_name'])){ echo ' ( '.$data[0]['particular_category_name'].' )'; }  if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}?></b></td></tr>
 		 </tbody>
	  </table>    
     <?php }?>
 

<table  width="100%"  style="border-left:none; border-right:none;border-collapse:collapse;padding-bottom:0px;"  class="table table-bordered table-hover table-striped" >
	
        <tr style="text-align:center;border:0px solid #cccccc; border-top:none;">
        	 <th  style="text-align:center;border:1px solid #cccccc; width:80px; border-left:none;padding-top:0px;padding-bottom:0px;"><?php if($ledgerIDArray[$m]['group_id'] == INCOME){echo "Month";}else{echo "Date";}?></th>
            <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; ">Particulars</th>
            <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; ">Cheque/Bill Number</th>
            
            <?php if($_REQUEST['ledger_type'] == 3 || $_REQUEST['ledger_type'] == 4)
			{?>
            <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; ">Deposits(Rs.)</th>
            <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px;">Withdrawals(Rs.)</th>
           <?php }
			else
			{?>
            <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; ">Debit (Rs.)</th>
             <th style="text-align:center;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px;">Credit (Rs.)</th>
            
            <?php }?>
            <th style="text-align:center;border:1px solid #cccccc; border-right:none;padding-top:0px;padding-bottom:0px;">Balance (Rs.)</th>
        </tr>
   
   	<tbody>  
       <?php	
				foreach($data as $k => $v)
				{
					if(isset($data[$k]['id']))
					{
					//$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					$categoryid = $LedgerCategoryID;
					$Is_Opening_Balance=$data[$k]['Is_Opening_Balance'];
					
					if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT )
					{ 
							$CreditAmt = $data[$k]['Debit'];
							$DebitAmt = $data[$k]['Credit'];	
							
							if($Is_Opening_Balance==1 && $CreditAmt <> 0)
							{
								$BalanceAmt -= $CreditAmt;
							}
							else if($Is_Opening_Balance==1 && $DebitAmt <> 0)
							{
								$BalanceAmt += $DebitAmt;
							}
							else
							{
								if($DebitAmt <> 0 )
								{
									$BalanceAmt += $DebitAmt;
								}
								
								if($CreditAmt <> 0)
								{
									$BalanceAmt -= $CreditAmt;
								}																	
							}
					
					}
					else
					{
						$DebitAmt = $data[$k]['Debit'];
						$CreditAmt = $data[$k]['Credit'];
						$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					}
					$DebitTotal = $DebitTotal + $DebitAmt;
					$CreditTotal = $CreditTotal + $CreditAmt;
				?>
				<tr >
                <?php //$voucher_details=$obj_ledger_details->get_voucher_details('',$data[$k]['VoucherID']);?>
         			<?php  if($_REQUEST['exp_group']=='1')
					{?>
					<td  style="text-align:center;border:1px solid #cccccc;width:80px;border-left:none;"><?php if($data[$k]['Date'] == 'Various'){ echo $data[$k]['Date'];}else{echo getDisplayFormatDate($data[$k]['Date']);}?>
               
                    </td>
                    <?php }
                    else
                    {?>
                    <td  style="text-align:center;border:1px solid #cccccc;width:80px;border-left:none;"><?php if($ledgerIDArray[$m]['group_id'] == INCOME && $_REQUEST['merge']=='1'){ echo $data[$k]['Date'];}else{echo getDisplayFormatDate($data[$k]['Date']);}?>
                  
                   <?php }?>
                    
					<td style="border:1px solid #cccccc;padding-top:0px;padding-bottom:0px;">
					<?php
						if(( $ledgerIDArray[$m]['group_id'] == LIABILITY ||  $ledgerIDArray[$m]['group_id']== ASSET) && $data[$k]['Is_Opening_Balance'] ==1)
						{
								echo 'Opening Balance';
						}
						else if($data[$k]['VoucherType'] <> "" && $data[$k]['VoucherNo'] <> 0)
						{
								if($ledgerIDArray[$m]['group_id'] == INCOME && isset($_REQUEST['merge']) && $_REQUEST['merge']=='1')
								{
									//skip	
								}
								else if($data[$k]['VoucherTypeID'] != VOUCHER_SALES)
								{
									echo "[".$data[$k]['VoucherType']." - ".$data[$k]['ExternalCounter']."]<br>";
								}
						}
						
						if($data[$k]['VoucherTypeID'] <> VOUCHER_SALES)
						{
							echo  '<b>'.$data[$k]['ParticularLedgerName'].'</b>';
							
							if(!empty($data[$k]['category_name']))
							{
								echo '( ' .$data[$k]['category_name'].' - '.$data[$k]['group_name']. ' )';
							}
						}
						
						if($data[$k]['VoucherTypeID'] == VOUCHER_RECEIPT && $data[$k]['PayerBank'] <> "")
						{
							echo "<br>[Payer Bank : ".$data[$k]['PayerBank']."]";
						}
						
						if(( $ledgerIDArray[$m]['group_id']== LIABILITY ||  $ledgerIDArray[$m]['group_id']== ASSET) && $data[$k]['Is_Opening_Balance'] ==1)
						{
								//echo 'Opening Balance';
						}
						else if($data[$k]['Note'] <> "-")
						{
								if($data[$k]['VoucherTypeID']==VOUCHER_SALES)
								{
									if( $ledgerIDArray[$m]['group_id'] == INCOME && isset($_REQUEST['merge']) && $_REQUEST['merge']=='1')
									{
										$startIndex = strpos($data[$k]['Note'], '<br>')+4;
										echo substr($data[$k]['Note'], $startIndex, strlen($data[$k]['Note']));
									}
									else
									{
										echo "       ".strip_tags($data[$k]['Note']);	
									}
								}
								else
								{
									echo "<br>".strip_tags($data[$k]['Note']);
								}
						}
						?>
                    </td>
					
                     <td style="text-align:center;border:1px solid #cccccc;">
					 <?php if($ledgerIDArray[$m]['group_id'] == INCOME && isset($_REQUEST['merge']) && $_REQUEST['merge']=='1')
								{
									echo "-";
								}
								else
								{
									echo $data[$k]['ChequeNumber'];
								}
					?></td>
                    <td style="text-align:right;border:1px solid #cccccc;"><?php if($DebitAmt <> 0){echo number_format($DebitAmt, 2);}else{echo '0.00';} ?></td>
					<td  style="text-align:right;border:1px solid #cccccc;"><?php if($CreditAmt <> 0){echo number_format($CreditAmt, 2);}else{echo '0.00';} ?></td>
                    <td style="text-align:right;border:1px solid #cccccc;  border-right:none;"><?php if($IsCreditor==true){echo number_format(abs($BalanceAmt), 2);} else{echo number_format($BalanceAmt, 2);}?></td>
                
                </tr>
				 <?php
					}//break;
				}
				?>
                <tr><td colspan="3" style="text-align:center;border:1px solid #cccccc;  border-left:none;"> <b>Total (Rs.)</b></td><td style="text-align:right;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; "><b><?php echo number_format($DebitTotal, 2);?></b></td><td style="text-align:right;border:1px solid #cccccc;padding-top:0px;padding-bottom:0px; "><b><?php echo number_format($CreditTotal, 2);?></b></td><td style="text-align:right;border:1px solid #cccccc; border-right:none;padding-top:0px;padding-bottom:0px; "><b><?php echo number_format($BalanceAmt, 2);?></b></td></tr>
				 </tbody>
        </table>
          </center>          
</div>
<?php
if($_REQUEST['add_pg_br']=='1')
{	 ?>
<div style='page-break-after:always;'>&nbsp;</div>
<?php }
}
}//for loop end ?>

</div> 
</div>
</div>

 <?php
 if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Fetch' &&  $_REQUEST['ledger_type'] <> "")
{?>
<form  action="export.php" method="post" id="myForm">
	 <input type="hidden" name="data"  id="data"/>
</form>
<iframe name="my-iframe" height="0"  width="0" style="visibility:hidden;"></iframe>
<?php }

 if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Fetch' &&  $_REQUEST['ledger_type'] <> "")
{
?>
<script>
setFontSize(); 
</script>
<?php 
}
?>
<script>

document.getElementById("tbl_showheader1").style.display = "none";
function addLedger() 
{ 
 	var ledger = document.getElementById('ledger');	
	for(i = 0; i < ledger.length; i++)
	{						
		if (ledger.options[i].selected)
		{					
			var text = ledger.options[i].text;	
			var val = ledger.options[i].value;		
			//var val = document.getElementById('unit').value;	
			var newDD = document.getElementsByName('selectedledger'); 					
			$(newDD).append("<option value="+val+">"+text+"</option>");
			ledger.remove(i);
			i--;			
		}
	}		
}
function addAllLedger() 
{ 
 	var ledger = document.getElementById('ledger');	
	for(i = 0; i < ledger.length; i++)
	{						
		var text = ledger.options[i].text;	
			var val = ledger.options[i].value;		
			//var val = document.getElementById('unit').value;	
			var newDD = document.getElementsByName('selectedledger'); 					
			$(newDD).append("<option value="+val+">"+text+"</option>");
			ledger.remove(i);
			i--;			
		
	}		
}
function removeLedger()
{		
	var selectedUnits = document.getElementById('selectedledger');	
	for(i = 0; i < selectedUnits.length; i++)
	{		
		if (selectedUnits.options[i].selected)
		{	
			var text = selectedUnits.options[i].text;		
			var val = selectedUnits.options[i].value;							
			var newData = document.getElementsByName('ledger'); 			
			$(newData).append("<option value="+val+">"+text+"</option>");
			selectedUnits.remove(i);
			i--;
		}
	}		
}
function removeAllLedger() 
{ 
 	var ledger = document.getElementById('selectedledger');	
	for(i = 0; i < ledger.length; i++)
	{						
		var text = ledger.options[i].text;	
			var val = ledger.options[i].value;		
			//var val = document.getElementById('unit').value;	
			var newDD = document.getElementsByName('ledger'); 					
			$(newDD).append("<option value="+val+">"+text+"</option>");
			ledger.remove(i);
			i--;			
		
	}		
}

showLoader();

$(window).load(function() 
{
	hideLoader();
	document.getElementById('btnSubmit').style.visibility = "visible";	
});
</script>
</body>
<?php include_once "includes/foot.php"; ?>