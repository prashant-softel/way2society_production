<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Journal</title>
</head>
<?php
	include_once("includes/head_s.php");
	include_once("classes/createvoucher.class.php");
	include_once("classes/defaults.class.php");
	include_once("classes/utility.class.php");
	include_once("classes/dbconst.class.php");
	$obj_createvoucher=new createVoucher($m_dbConn);
	$obj_utility = new utility($m_dbConn,$m_dbConnRoot );
	//print_r($_SESSION);
	$datepicker=$obj_createvoucher->StartEndDate($_SESSION['default_year']);
	$minDate=$datepicker[0]['BeginingDate'];
	$maxDate=$datepicker[0]['EndingDate'];
	//$Counter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0);
	
   $dropDownlist =  "select `id`,concat(ledgertable.ledger_name, '  [ ', categorytbl.category_name,' ]')  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid != ".DUE_FROM_MEMBERS." AND society_id='".$_SESSION['society_id']."' ";
	
	if(!isset($_REQUEST['bankid']))
	{
		$Counter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0);
		$UpdatePayment = 0;
		$dropDownlist .= " and ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") ";
	}
	else
	{
		if($_REQUEST['LeafID'] == '-1')
		{
			$dropDownlist .= " and ledgertable.categoryid NOT IN(".BANK_ACCOUNT.") ";			
		}
		else
		{
			$dropDownlist .= " and ledgertable.categoryid NOT IN(".CASH_ACCOUNT.") ";
		}

		$ListOfCashLedger = $obj_utility->GetBankLedger($_SESSION['default_cash_account']); // return list of bank ledgers
		$ListOfBankLedger = $obj_utility->GetBankLedger($_SESSION['default_bank_account']); // return list of cash ledgers
		$ListOfAccounts = array_merge($ListOfBankLedger,$ListOfCashLedger); // combination of bank and cash ledger
		$BankLedgers = array_column($ListOfAccounts, 'id');
		
		
		$IsSameCntApply = $obj_utility->IsSameCounterApply();
		if($IsSameCntApply == 1)
		{
			$Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,0);	
		}
		else
		{
			if(isset($_REQUEST['bankid']))
			if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
			{
				$Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT, $_REQUEST['bankid']);					
			}
			else
			{
				$Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT, $_REQUEST['bankid']);	
			}
	
		}
	}
	//else if(isset($_REQUEST['bankid']))
	{
		
	}
	
	$dropDownlist .=" ORDER BY ledgertable.ledger_name ASC"; 

	$DateString = 'Voucher Date:';
	$width = '25';
	
	//echo "test1";
	if(isset($_REQUEST['bankid']))
	{
		if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
		{
			//echo "test3";
			$TABLE = TABLE_PAYMENT_DETAILS;
			$DateString = 'Cheque Date:';
			
			if(isset($_REQUEST['LeafID']) && $_REQUEST['LeafID'] == -1)
			{
				$DateString = 'Voucher Date:';	
			}
			$Head = 'Multiple Ledger Entry';
			$CheckNumber = $_REQUEST['Chk_No'];
		}
		else if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
		{
			//echo "test4";
			$TABLE = TABLE_CHEQUE_DETAILS;
			$Head = 'Receipt';
			$receiptData =  $obj_editvoucher->getChequeDate($Data[0]['RefNo']);
			$width = '50';
		}
	}
	else
	{
		//echo "test5";
		$Head = 'Journal';	
	}
	//var_dump($Counter);
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript" src="js/jscreatevoucher_20190706.js"></script>
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
    
    <script>SetAry_ExitingExCounter(<?php echo json_encode($Counter[0]['ExitingCounter']);?>)
    
     var BankLedgers = '<?php echo json_encode($BankLedgers);?>';
	 BankLedgers = JSON.parse(BankLedgers);
	 console.log(BankLedgers);
	
    
    
    </script>
	<script type="text/javascript">
		minStartDate = '<?php echo $minDate;?>'
    	maxEndDate = '<?php echo $maxDate;?>'
		 
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minStartDate,
          	maxDate: maxEndDate 
        })});
    </script>
    <script type="application/javascript">
	
	var rowCounter=2;
	
	$(function()
        {
			 var VoucherCounter = getExCounter(<?php echo $Counter[0]['CurrentCounter'];?>);
			 document.getElementById('VoucherNumber').value = VoucherCounter;
			 document.getElementById('Current_Counter').value = <?php echo $Counter[0]['CurrentCounter'];?>;
			 document.getElementById('OnPageLoadTimeVoucherNumber').value = VoucherCounter;
		});

	function changesValues(iCounter)
	{
		
		var Byto=document.getElementById('byto' + iCounter).value;
		//alert(Byto);
		if(Byto=='BY')
		{
			/*document.getElementById("Note" + iCounter).disabled=false;
			document.getElementById("Note" + iCounter).style.backgroundColor='white';*/
			
			document.getElementById("Debit" + iCounter).disabled=false;
			document.getElementById("Debit" + iCounter).style.backgroundColor='white';
			
			document.getElementById("Credit" + iCounter).disabled=true;
			//alert("1");
			document.getElementById("Credit" + iCounter).value="";
			//alert("2");
			document.getElementById("Credit" + iCounter).style.backgroundColor = 'lightgray';	
			
		}
		else
		{
			/*document.getElementById("Note" + iCounter).disabled=true;
			document.getElementById("Note" + iCounter).value="";
			document.getElementById("Note" + iCounter).style.backgroundColor = 'lightgray';*/	
			
			document.getElementById("Credit" + iCounter).disabled=false;
			document.getElementById("Credit" + iCounter).style.backgroundColor = 'white';
			
			document.getElementById("Debit" + iCounter).disabled=true;
			document.getElementById("Debit" + iCounter).value="";
			document.getElementById("Debit" + iCounter).style.backgroundColor = 'lightgray';	
			
		}
		
	}	
	function showbutton(status)
	{	
			
			if(status==true) 
			{
			document.getElementById('submit').disabled=false;
			document.getElementById('submit').style.backgroundColor = '#337ab7';
			document.getElementById('submit').style.color = '#fff';
			document.getElementById('submit').style.borderColor = '#2e6da4';
			
			
			}
			 else 
			{
				document.getElementById('submit').disabled=true;
				document.getElementById('submit').style.backgroundColor = 'lightgray';
				
			}
			
			 for(var i=1;i<=rowCounter;i++)
					{
						//alert(rowCounter);
						var ByTo = document.getElementById('byto'+ i).value;
			
						//alert(ByTo);
						if(ByTo=='BY')
						{
						//alert("3");
						
						
						document.getElementById("Debit" + i).disabled=false;
						document.getElementById("Credit" + i).disabled=true;
						document.getElementById("Credit" + i).style.backgroundColor = 'lightgray';
						/*document.getElementById("Note" + i).disabled=false;*/
						//document.getElementById("Note").disabled=true;
						
						}
						else
						{
							//alert("4");
							
							document.getElementById("Credit" + i).disabled=false;
							document.getElementById("Debit" + i).disabled=true;
							document.getElementById("Debit" + i).style.backgroundColor = 'lightgray';
							
							/*document.getElementById("Note" + i).disabled=true;
							document.getElementById("Note" + i).style.backgroundColor = 'lightgray';*/
						
						}
					}
	}
	
	function AddNewRow()
	{
		//alert("1");
		rowCounter++;	
		
		//alert("2");
		var varRow  ="<tr><td><select id='byto"+ rowCounter+"' name='byto"+ rowCounter+"'  style='width:100px;' onChange='changesValues("+ rowCounter +")'><option value='BY'>BY</option><option value='TO' selected >TO</option> </select></td>";	
		
		//varRow += "<td><select id='To" + rowCounter+"' name='To"+ rowCounter+"' style='width:250px;' > <?php //echo $By = $obj_createvoucher->combobox("select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid NOT IN(".DUE_FROM_MEMBERS.",".BANK_ACCOUNT.",".CASH_ACCOUNT.") and   society_id=".$_SESSION['society_id']." ",0);?></select></td>";
		
		varRow += "<td><select id='To" + rowCounter+"' name='To"+ rowCounter+"' style='width:250px;' > <?php echo $By = $obj_createvoucher->combobox($dropDownlist,0);?></select></td>";
		
		varRow +="<td><input type='text' id='Debit"+ rowCounter+"' name='Debit"+ rowCounter+"' style='width:120px' onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'/></td>";
		
		varRow +="<td><input type='text' id='Credit" + rowCounter+"' name='Credit" + rowCounter+"' style='width:120px' onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);' /></td>";	
		<!--varRow +="<td ><input type='text' id='Note" + rowCounter+"' name='Note" + rowCounter+"' style='width:200px' ></td>";-->
		
		varRow +="<td><p id='label"+ rowCounter +"' name='label"+ rowCounter +"' style='color:#00FF00' readonly></p></td></tr>";
		
	
		document.getElementById('maxrows').value = rowCounter;
		$("#table_details > tbody").append(varRow);
		//document.getElementById("Credit" + rowCounter).focus();
	
	
	}
	function go_error()
    {
        setTimeout('hide_error()',3000);	
    }
	
    function hide_error()
    {
		document.getElementById('error').innerHTML = '';
        document.getElementById('error').style.display = 'none';	
    }
	
	document.body.onload= function()
	{
		<?php if(isset($_REQUEST['ShowData']) ){ ?>	
		go_error();
		showbutton(false);
		<?php }else{ ?>
		showbutton(false);
		<?php }
		if($_SESSION['role'] <> ROLE_SUPER_ADMIN)
		{ ?>
			document.getElementById('VoucherNumber').disabled = true;
			document.getElementById('VoucherNumber').style.background = "#d1d1d1";
		<?php }
		
		
		if(isset($_REQUEST['CustomLeaf']) && $_REQUEST['CustomLeaf'] == 0)
		{ ?>
		
			document.getElementById('ChequeNumber').readOnly = true;
			document.getElementById('ChequeNumber').style.background = "#d1d1d1";
			document.getElementById('ChequeNumber').setAttribute("tabIndex", "-1");
			
		<?php }
		
		if(isset($_REQUEST['LeafID']) && $_REQUEST['LeafID'] == -1)
		{ ?>
			
			document.getElementById('ChequeNumber').parentElement.style.display = 'none';
			
		<?php } ?>
	}
	</script>
</head>



<body>
<div id="middle">
    <br><div class="panel panel-info" id="panel" style="display:none; min-height:450px;" >
    <div class="panel-heading" id="pageheader"><?php echo $Head; ?></div>	
<div style="float:right;margin-right: 15px;margin-top: 20px;">
<?php 
if($_SESSION['is_year_freeze'] == 1)
{?>
	<button type="button" class="btn btn-primary" onClick="window.open('import_JV.php');" id=""  disabled="disabled">Import Journal Voucher</button>
    
 <?php 
 }
 else
 {?>
 <button type="button" class="btn btn-primary" onClick="window.open('import_JV.php');" id="" >Import Journal Voucher</button>
 <?php
 }?>   </div>
<center>
<br>

<form name="createvoucher" id="createvoucher" method="post"  action="process/createvoucher.process.php" onSubmit="return doFormSubmit(minStartDate,maxEndDate);">
<table  id="table_details" width="100%" style="text-align:center;margin-top: -15px;" >

    <tbody>
            <input type="hidden" name="maxrows" id="maxrows" />
            <input type="hidden" name="mode" id="mode" value=<?php echo ADD; ?>>
            <input type="hidden" name="Current_Counter" id="Current_Counter" value="" />
            <input type="hidden" name="IsCallUpdtCnt" id="IsCallUpdtCnt" value="1" />
            <input type="hidden" name="OnPageLoadTimeVoucherNumber" id="OnPageLoadTimeVoucherNumber" value="" />
            <input type="hidden" name="Updatetable" id="Updatetable" value="<?php echo $TABLE;?>" />
            <input type="hidden" name="BankID" id="BankID" value="<?php echo $_REQUEST['bankid'];?>" />
            <input type="hidden" name="LeafID" id="LeafID" value="<?php echo $_REQUEST['LeafID'];?>" />
            <input type="hidden" name="CustomLeaf" id="CustomLeaf" value="<?php echo $_REQUEST['CustomLeaf'];?>" />
            <?php
            
                if(isset($_POST['ShowData']))
                {
            ?>
                    <tr height='30'><td colspan='10' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
            <?php
                }
				else
				{
				?>	
						<tr height="30"><td colspan="10" align="center"><font color="red" size='-1'><b id="error" style='display:block;'></b></font></td></tr>
				<?php
				}
				?>	
           
            
            <tr align="center">
                        <td colspan="4">
                        <table width="100%">
                        <tr>
                        
                        <td style="font-size:14px; width:25%;" align="left"><?php echo $DateString;?> <input type="text" name="voucher_date" id="voucher_date"  class="basics" size="10"   style="width:100px;" placeholder="dd-mm-yyyy"/></td>
                        <td style="font-size:14px; width:25%;" align="left">Voucher Number: <input type="text" name="VoucherNumber" id="VoucherNumber" size="7"  style="width:100px;" value="" required /></td>
                       
                         <?php if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
						 		{
								 if(isset($_REQUEST['CustomLeaf']) && $_REQUEST['CustomLeaf'] == 1)
								 {?>
                        				<td style='width:25%;font-size:14px'>Mode of Payment : <select id='ModeOfPayment' style='width:100px;' name='ModeOfPayment' onChange='ModeOfPaymentChanged(this)'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>									 
								<?php }
							 
							 ?>
	
                            <td style='width:25%;font-size:14px'>ChequeNumber : <input type='text'  id='ChequeNumber' name='ChequeNumber'  value='<?php echo $CheckNumber;?>' style='width:90px;'></td>

						 <?php }?>
    
                        <td style="font-size:14px; width:15%" align="left">
                        <?php if(!isset($_REQUEST['bankid']))
						{?> 
                        Is Invoice: <input type="checkbox" id="is_invoice" name="is_invoice"  value="0" style="margin-top: -2px;box-shadow: 1px 1px 4px #f9f9f9;" onChange="AddInvice(this.value);">
                         <?php }?>
                         <!--<span style="margin-left: 35px; visibility:hidden" id="invoice"> Invoice No: <input type="text" id="invoice_no" name="invoice_no" style="width: 120px;">
                         IGST : <input type="text" name="igst_amount" id="igst_amount" style="width:80px;"/>
                         CGST :<input type="text" name="cgst_amount" id="cgst_amount" style="width:80px;"/>
                         SGST :<input type="text" name="sgst_amount" id="sgst_amount" style="width:80px;"/>
                         CESS :<input type="text" name="cess_amount" id="cess_amount" style="width:80px;"/>
                         </span>--></td>
                         <td  colspan="3">
                         <table style="width: 80%;margin-top: -20px; display:none;" id="InvoiceStatus">
                         <tr>
                         <th style="text-align: left; width:15%">Invoice No</th>
                       <?php 
                       if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
						 { ?>
                         <th style="text-align: center; width:10%">&nbsp;</th>
                         <th style="text-align: center; width:10%"">IGST</th>
                         <th style="text-align: center; width:10%"">CGST</th>
                         <th style="text-align: center; width:10%"">SGST</th>
                         <th style="text-align: center; width:10%"">CESS</th>
                         <?php }?>
                         </tr>
                         <tr>
                         <td><input type="text" id="invoice_no" name="invoice_no" style="width: 100px;"></td>
                         <?php  
                       		if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
						 { ?>
                         <td>&nbsp;</td>
                         <td><input type="text" name="igst_amount" id="igst_amount" style="width:80px;" value="0" onKeyUp="extractNumber(this,2,true);"/></td>
                         <td><input type="text" name="cgst_amount" id="cgst_amount" style="width:80px;" value="0" onKeyUp="extractNumber(this,2,true);"/></td>
                         <td><input type="text" name="sgst_amount" id="sgst_amount" style="width:80px;" value="0" onKeyUp="extractNumber(this,2,true);"/></td>
                         <td><input type="text" name="cess_amount" id="cess_amount" style="width:80px;" value="0" onKeyUp="extractNumber(this,2,true);"/></td>
                         <?php }?>
                         </tr></table>
                         </td>
                         </tr></table>
                         <!--<td><input type="text" name="voucher_date" id="voucher_date" style="width:80px;"/></td>
                         <td><input type="text" name="voucher_date" id="voucher_date" style="width:80px;"/></td>
                         <td><input type="text" name="voucher_date" id="voucher_date" style="width:80px;" /></td>
                         <td><input type="text" name="voucher_date" id="voucher_date" style="width:80px;"/></td>-->
            			
            </tr>
             <tr>
              <?php if(!isset($_REQUEST['bankid'])){?> 
              <td colspan="4"><span style="color:#00F;font-weight:bold;">Note :</span><span style="color:#00F"> Please check invioce checkbox if you want to map payment entry against invoice entry on payments screen later.</span></td></tr>
            <?php }?>
            <!--<tr> <td colspan="5"><br/></td></tr>-->
         <!--   
           <tr align="center">
                        
                        <td colspan="5" style="font-size:14px;" align="left">Due Date:&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="due_date" id="due_date"  class="basics" size="10"   style="width:100px;"/></td>
           </tr>-->
            
            <!--
            
            <tr> <td colspan="5"><br/></td></tr>
            

            <tr align="center">
            <td colspan="5" style="font-size:14px;"> Entry Details Note:<textarea id="Note" name="Note" style="width:250px; height:40px"  maxlength="1000" rows="2" cols="50" required></textarea></td>
            </tr>-->
            
            <tr> <td colspan="5"><br/></td></tr>
            
            <tr align="center" height="30px">
               <th style="width:15%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;">By(Debit)/To(Credit)</th>     	
               <th style="width:40%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;">Ledger Name</th>
               <th style="width:25%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;">Debit</th>
               <th style="width:25%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;">Credit</th>
                     
            </tr>
            
            <tr> <td colspan="5"><br/></td></tr>
            
            <tr align="center">
            	<?php $id = 0;
				 if(isset($_REQUEST['lid'])) 
				 {  $id = $_REQUEST['lid']; } ?>
               <td><select id="byto1" style="width:100px;" name="byto1"  onChange="changesValues(1)"><option value="BY" selected>BY</option> </select></td>     	
               <td><select id="To1" style="width:250px;" name="To1"> <?php echo $By = $obj_createvoucher->combobox($dropDownlist,$id);?></select></td>
               <td><input type="text" id="Debit1" name="Debit1" style="width:120px" onBlur="AddValues(false);" onKeyUp="extractNumber(this,2,true);" /></td>
               <td><input type="text" id="Credit1" name="Credit1" style="width:120px" onKeyUp="extractNumber(this,2,true);"/></td>    
               <!--<td ><input type="text" id="Note1" name="Note1" style="width:200px" ></td>-->
               <td><p id='label1' name='label1' style='color:#00FF00' readonly></p></td>         
            </tr>
            
            <tr align="center">
               <td><select id="byto2" style="width:100px;" name="byto2" onChange="changesValues(2)"><option value="BY">BY</option><option value="TO" selected>TO</option> </select></td>     	
               <td><select id="To2" style="width:250px;" name="To2"> <?php echo $By = $obj_createvoucher->combobox($dropDownlist,0);?></select></td>
               <td><input type="text" id="Debit2" name="Debit2" style="width:120px"  onBlur="AddValues(true);" onKeyUp="extractNumber(this,2,true);"/></td>
               <td><input type="text" id="Credit2" name="Credit2" style="width:120px" onBlur="AddValues(true);"  onKeyUp="extractNumber(this,2,true);"/></td>
              <!-- <td ><input type="text" id="Note2" name="Note2" style="width:200px" ></td>-->
               <td><p id='label2' name='label2' style='color:#00FF00' readonly></p></td>         
            </tr>
            
    </tbody>

</table>

<br />

<table id="table_details2" width="100%" style="text-align:center;">
    <tbody>
    
        <tr align="center">
            <td style="width:15%;"></td>
            <td  style="width:27%;font-size:14px;">Total Amount</td>
              <td style="width:15%;"></td>
            <td id="debittotal" style="width:20%; color:#FFF;padding-top: 3px;" ></td>
            <td id="credittotal" style="width:21%;color:#FFF;padding-top: 3px;"></td>
            <td style="width:15%;"></td>
           
        </tr>
        <tr> <td colspan="5"><br/></td></tr> 
         <tr>
                       <td colspan="5" style="font-size:14px;"> Entry Details Note:<textarea id="Note" name="Note" style="width:400px; height:40px"  maxlength="1000" rows="2" cols="50" required></textarea></td>
          </tr>
        
     	<tr> <td colspan="5"><br/></td></tr>   
        
        <tr align="center"><td colspan="5" style="font-size:12px;">
        <?php if($_SESSION['is_year_freeze'] == 1)
		{?>
        	<input type="submit" name="submit"  value="Submit"  style="width:120px; height:30px; background-color:lightgray;" disabled="disabled" >
        <?php 
		}
		else
		{?>
			<input type="submit" name="submit" id="submit" value="Submit"  style="width:120px; height:30px; background-color:lightgray;" >
		<?php }?>
        </td></tr>
        
    </tbody>

</table>


</form>

</center>

<script>
//var check=document.getElementById('is_invoice').value;
//alert(check);
 
function AddInvice(Value)
{ 
//alert(Value);
    if(document.getElementById('is_invoice').checked==true)
	{
	document.getElementById('InvoiceStatus').style.display='table';	
	document.getElementById('is_invoice').value='1';	
	//alert(Value);
	// var invoice_no=document.getElementById('invoice_no').value;
		// if(invoice_no=='' && document.getElementById('is_invoice').checked==true )
 		//{
   		//alert("Please Enter Invoice Number !");
   		//return false;	 
 		//}
	}
	else
	{
		document.getElementById('InvoiceStatus').style.display='none';
		document.getElementById('is_invoice').value='0';
	}
	
}
//var invoice_no=document.getElementById('invoice_no').innerHTML='table';
	//if(invoice_no=='')
 	//{
   	//alert("Please Enter Invoice Number !");
   //return false;	 
 	//}

	function AddValues(addRowFlag)
	{
		//alert(rowCounter);
		var Credit=0;
		var Debit=0;
		
		
		for(var i=1;i<=rowCounter;i++)
		{
			//alert("hii");
			if(document.getElementById("Debit" + i).value!="")
			{
				
				Debit += parseFloat(document.getElementById("Debit" +i).value);
			}
			
			if(document.getElementById("Credit" + i).value!="")
			{
				Credit += parseFloat(document.getElementById("Credit" + i).value);
				
			}
		
		}

		Debit = Debit.toFixed(2);
		Credit = Credit.toFixed(2);
		//alert("me");
		document.getElementById("credittotal").innerHTML='<b>' + Credit + '</b>';
		document.getElementById("debittotal").innerHTML= '<b>' +Debit + '</b>';
		
		if( Debit!=Credit )
		{
			//alert("AddNewRow");
			
			document.getElementById("credittotal").style.backgroundColor = '#F70D1A';
			document.getElementById("debittotal").style.backgroundColor = '#F70D1A';
			
			if(checkForEmptyRowAlreadyExists(rowCounter) == true)
			{
				if(addRowFlag==true)
				{
						AddNewRow();
				}
				
				showbutton(false);
			}
			document.getElementById('submit').disabled=true;
			document.getElementById('submit').style.backgroundColor = 'lightgray';
			return false;
		}
		else
		{
			//alert("showbutton");
			document.getElementById("credittotal").style.backgroundColor = '#7FE817';
			document.getElementById("debittotal").style.backgroundColor = '#7FE817';
			document.getElementById('maxrows').value=rowCounter;
			showbutton(true);
			return true;
		}
		
	}
</script>
<?php
if(IsReadonlyPage() == true)
{
	echo '<center><div style="padding-top: 150px;"><font  color="#FF0000" style="font-weight:bold;font-size:16px;">Selected financial year has been locked,hence passing journal voucher entry is not allowed.</font></div></center>';
?>
	<script>
		document.getElementById('createvoucher').style.display = 'none';
	</script>
<?php			
}
?>
</div>
</div>
</body>

</html>
<?php include_once "includes/foot.php"; ?>