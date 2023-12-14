<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Voucher Counter</title>
</head>

<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
include_once("classes/defaults.class.php");
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_utility = new utility($m_dbConn);
$obj_defaults = new defaults($m_dbConn,$m_dbConnRoot);

$CashLedgerDetails = $obj_utility->GetBankLedger($_SESSION['default_cash_account']);
$BnkLedgerDetails = $obj_utility->GetBankLedger($_SESSION['default_bank_account']);


$IsSameCntApply = $obj_utility->IsSameCounterApply();
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <style>
	input[type='checkbox']
	{
		margin-left:15%;
	}
	th
	{
		text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;border-radius: 10px; padding-bottom: 5px;
	}
	#VoucherCounter th{
		background-color:white;color:#000000;font-size:11px;text-align:left;
	}
	</style>
</head>
<body onLoad="go_error()">

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
else{}
?>
<div class="panel panel-info" id="panel" style="display:none;">
     <div class="panel-heading" id="pageheader">Voucher Counter </div><br />
 	<center>
  		<div> 
    		<div style="border:1px solid  #666; width:90%;  border-radius: 5px;;">
				<form name="voucherCounterForm" id="voucherCounterForm" method="post" action="process/voucherCounter.process.php">
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
                
                    <tr><BR></tr>
                    <tr>
                       <td><input type="button"  name=""  id="" value="Counter Validation" onclick = "window.open('voucherValidation.php')" class="btn btn-primary"   style="box-shadow: none;" /></td>
                    </tr> 
                    <br><br>  
    <tr>
   	<td align="center"  id="GSTDefault" name="GSTDefault"><span style="text-align:center;font-weight:bold;font-size:20px;padding-top:15px;color:#656565;">Voucher Counter Setup</span></td>
   </tr>
   <br/><br/>
   <tr>
       <table align='center'>    
        <tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Year Defaults</td></tr>
        <tr>
			<td>Current Year : &nbsp;</td>
            
            
            <!-- Change Voucher Counter Method is for load the change year VoucherCounter so change Counters will update on save for change year from dropdown-->
			<td><select name="default_year" id="default_year" onChange="CheckCounterData(this.value)">
            	<?php
					if($default_year <> 0)
					{ 
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $_SESSION['default_year']); 
                    }
                    else
                    {
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $_SESSION['default_year'], "Please Select"); 
                    }
				?>		
            </select>
            </td>
		</tr>
       </table>
	</tr>		
   <table width="95%" align="center" id="VoucherCounter">
   <BR>
   <tr>
   <th></th>
   <th></th>
   <th>Starting Counter</th>
   <th>Next Counter</th>
   <th></th>
   <th>Starting Counter</th>
   <th>Next Counter</th>
   </tr>
   
   <tr>
   		<?php $Counter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0,false);?>
        <input type="hidden" id="JVVoucherType" name="JVVoucherType" value="<?php echo VOUCHER_JOURNAL;?>">
       <td id="JVNumber" name="JVNumber" style="width:40%;">Journal Voucher : &nbsp;</td>
       <td></td>
       <td style="padding-right:10px"><input type="text" name="JVstart" id="JVstart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:30px"><input type="text" name="JVcurrent" id="JVcurrent" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
	</tr>
    
    <tr>
   		<?php $Counter = $obj_utility->GetCounter(VOUCHER_INVOICE,0,false);?>
        <input type="hidden" id="InvoiceVoucherType" name="InvoiceVoucherType" value="<?php echo VOUCHER_INVOICE;?>">
       <td id="InvoiceNumber" name="InvoiceNumber" style="width:40%;">Invoice : &nbsp;</td>
       <td></td>
       <td style="padding-right:10px"><input type="text" name="InvoiceStart" id="InvoiceStart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:30px"><input type="text" name="InvoiceCurrent" id="InvoiceCurrent" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
	</tr>
    <tr>
	   <?php $Counter = $obj_utility->GetCounter(VOUCHER_DEBIT_NOTE,0,false);?> 
       <input type="hidden" id="DebitNoteVoucher" name="DebitNoteVoucher" value="<?php echo VOUCHER_DEBIT_NOTE;?>">
       <td id="DebitNote" name="DebitNote" style="width:40%;">Notes</td>
       <td style="width:10%;">Debit : &nbsp;</td>
       <td style="padding-right:10px"><input type="text" name="DebitNotestart" id="DebitNotestart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:30px"><input type="text" name="DebitNotecurrent" id="DebitNotecurrent" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
        
       <?php $Counter = $obj_utility->GetCounter(VOUCHER_CREDIT_NOTE,0,false);?>
       <input type="hidden" id="CreditNoteVoucher" name="CreditNoteVoucher" value="<?php echo VOUCHER_CREDIT_NOTE;?>">
       <td id="CreditNote" name="CreditNote" style="width:15%;">Credit :&nbsp;</td>
       <td style="padding-right:10px"><input type="text" name="CreditNotestart" id="CreditNotestart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:10px"><input type="text" name="CreditNotecurrent" id="CreditNotecurrent" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
      
    </tr>
       		
    
    

        <?php 
        $CashLedgerDetails = $obj_utility->GetBankLedger($_SESSION['default_cash_account']);
		//var_dump($CashLedgerDetails);
		?>
		 <input type="hidden" id="SizeOfCashLedger" name="SizeOfCashLedger" value="<?php echo sizeof($CashLedgerDetails);?>">		
		<?php for($i = 0 ; $i < sizeof($CashLedgerDetails); $i++)
		{ ?>
			<tr>
			   <?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT,$CashLedgerDetails[$i]['id'],false);?> 
               <input type="hidden" id="CashReceiveVoucherType" name="CashReceiveVoucherType" value="<?php echo VOUCHER_RECEIPT;?>">
               <input type="hidden" id="CashLedgerID<?php echo $i;?>" name="CashLedgerID<?php echo $i;?>" value="<?php echo $CashLedgerDetails[$i]['id'];?>"> 
               <td id="CashLedgerName" name="CashLedgerName" style="width:40%;"><?php echo $CashLedgerDetails[$i]['ledger_name'];?></td>
               <td style="width:10%;">Receipts : &nbsp;</td>
               <td style="padding-right:10px"><input type="text" name="CashReceivestart" id="CashReceivestart<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
               <td style="padding-right:30px"><input type="text" name="CashReceivedcurrent" id="CashReceivedcurrent<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
                
               <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,$CashLedgerDetails[$i]['id'],false);?>
               <input type="hidden" id="CashPayVoucherType" name="CashPayVoucherType" value="<?php echo VOUCHER_PAYMENT;?>">
               <td id="CashNumber" name="CashNumber" style="width:15%;">Payments :&nbsp;</td>
               <td style="padding-right:10px"><input type="text" name="CashPaystart" id="CashPaystart<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
               <td style="padding-right:10px"><input type="text" name="CashPaycurrent" id="CashPaycurrent<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
              
            </tr>
       		
			
		<?php }?>
      	
   		 <tr>
        <td><input type="checkbox" id="BankCounter" name="BankCounter" onChange="ShowCommonCounter();" style="margin-left:0%"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Use Same Counter for all Bank &nbsp;&nbsp;&nbsp;</label></td>
        </tr>
        </table>
        <BR>
        <table id = "CommonCounter" name = "CommonCounter" style="display:none; width:95%;">
        
        <tr>
        <td style="width:40%;">Single Counter for all banks</td>
      	
    	<?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT,0,false);?>
       <input type="hidden" id="SingleBnkRcptVoucher" name="SingleBnkRcptVoucher" value="<?php echo VOUCHER_RECEIPT;?>">
       <td id="SingleRcptCnt" name="SingleRcptCnt"  style="width:10%">Receipts : &nbsp;</td>
       
       <td style="padding-right:10px"><input type="text" name="SingleVcrCntRcptSrt" id="SingleVcrCntRcptSrt" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:30px"><input type="text" name="SingleVcrCntRcptCrt" id="SingleVcrCntRcptCrt" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
      
       <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,0,false);?> 
       <input type="hidden" id="SingleBnkPayVoucher" name="SingleBnkPayVoucher" value="<?php echo VOUCHER_PAYMENT;?>">
       <td id="SinglePayCnt" name="SinglePayCnt" style="width:15%">Payments : &nbsp;</td>
       <td style="padding-right:10px"><input type="text" name="SingleVcrCntPaySrt" id="SingleVcrCntPaySrt" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;"/></td>
       <td style="padding-right:10px"><input type="text" name="SingleVcrCntPayCrt" id="SingleVcrCntPayCrt" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
 	   </tr>
        
      </table>
			      
      
        <input type="hidden" id="LedgerSize" name="LedgerSize" value="<?php echo sizeof($BnkLedgerDetails);?>">
 
        <table id="bankledgers" name="bankledgers" style="width:95%;" align="center">
        <?php for($i = 0; $i < sizeof($BnkLedgerDetails); $i++ ){?>
      
       	<?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT,$BnkLedgerDetails[$i]['id'],false);?>
        <tr>
        <input type="hidden" id="BankRcpVoucherType" name="BankRcpVoucherType" value="<?php echo VOUCHER_RECEIPT;?>">
        <input type="hidden" id="LedgerID<?php echo $i;?>" name="LedgerID<?php echo $i;?>" value="<?php echo $BnkLedgerDetails[$i]['id'];?>"> 
        <td  id="LedgerName<?php echo $i;?>" name="LedgerName<?php echo $i;?>" style="width:40%;"><?php echo $BnkLedgerDetails[$i]['ledger_name'];?> : &nbsp;</td>
        <td id="CashNumber" name="CashNumber" style="width:10%">Receipts : &nbsp;</td>
        <td style="padding-right:10px"><input type="text" name="LedgerStartRcpValue<?php echo $i;?>" id="LedgerStartRcpValue<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;" /></td>
        <td style="padding-right:30px"><input type="text" name="LedgerCurrentRcpValue<?php echo $i;?>" id="LedgerCurrentRcpValue<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
		
        
        <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,$BnkLedgerDetails[$i]['id'],false);?> 
        
        <input type="hidden" id="BankPayVoucherType" name="BankPayVoucherType" value="<?php echo VOUCHER_PAYMENT;?>">
       	<td id="Pay_Neft" name="Pay_Neft" style="width: 15%;">Payments : &nbsp;</td>
        <td style="padding-right:10px"><input type="text" name="LedgerStartPayValue<?php echo $i;?>" id="LedgerStartPayValue<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;" /></td>
        <td style="padding-right:10px"><input type="text" name="LedgerCurrentPayValue<?php echo $i;?>" id="LedgerCurrentPayValue<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['CurrentCounter'];?>" style="width:75px;"/></td>
		</tr>
       
        <?php }?>
        </table>
        <br/><br/>
        <table>
        <tr>
			<td colspan="2" align="center"><input type="button" name="insert" id="insert" value="Save" onClick="UpdateCounter();" style="width:120px; color:#FFF;background-color: #337ab7;" class="btn btn-primary"></td>
		</tr>
		</table>
                 <hr style="border-top:0.5px solid black"/>
                
                
     <table>           
    <tr align="center">
   	<td><span style="text-align:center;font-weight:bold;font-size:20px;padding-top:15px;color:#656565;">Voucher Counter Renumbering</span></td>
   </tr>
   </table>
   
   <table style='width:80%'>
<tr><td><font color="#0033CC">NOTE: </font></td></tr>
<tr><td><font color="#0033CC">1.Please check the checkbox for voucher you want to renumber then select start date,end date and enter your starting voucher number.</td></tr>
<tr><td><font color="#0033CC">2.If you want prefix of bank or cash in voucher print, then please update prefix from <a style="color:#fb031a" href="BankDetails.php" target="_blank">Bank Details page</a>.</td></tr>
</table>
  			 <table>
                	<tr>
                		<td  style="padding:10px; font-size:12px;"><label style="padding-right:10px;">Start Date</label><input type="text" name="from_date" id="from_date" class="basics" size="10" value="<?php echo $_SESSION['from_date'];?>" style="width:80px;" /></td>
                        <td style="padding:10px;font-size:12px"><label  style="padding-right:10px;">End Date</label><input type="text" name="end_date" id="end_date"  class="basics" size="10" readonly   value=<?php if(isset($_SESSION['to_date'])){ echo $_SESSION['to_date']; } else { echo date("d-m-Y"); } ?>  style="width:80px;"/></td>
                   </tr>
                </table>
                    <table  cellpadding="10px" cellspacing="10px" style="width:100%;font-size:15px;">
                              <tr align='center' height='30px'>
                                     <th style='width:45%;'>Voucher Name</th>   	
                                     <th style='width:20%;'>Start voucher from</th>
                                     <th style='width:20%;'>Start voucher from</th>
                              </tr>
                             <!--  <tr align='center' height='30px'>
                                     <td></td>  	
                                     <td style='width:20%;'><span style="width:10%;padding: 5px;background: #cccac6;padding-left: 10px;padding-right: 10px;border-radius: 8px;margin-left:1%;"></span></td>
                                     <td style='width:20%;'><span style="width:10%;padding: 5px;background: #cccac6;padding-left: 10px;padding-right: 10px;border-radius: 8px;margin-left:1%;"></span></td>
                              </tr>-->
                              <tr>
                                    <td style="width:45%;font-size: 15px;" align="left">Journal Voucher</td>
                                    
                                    <td>
                                    <input type="checkbox"  id="Journal" name="Journal" value="<?php echo VOUCHER_JOURNAL;?>" />
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0);?>
                                    <span><input type="text" name="JVstart" id="JVstart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                    <td style="width:10%;"></td>

                              </tr>
                               <tr>
                                    <td style="width:45%;font-size: 15px;" align="left">Invoice Voucher</td>
                                    <td>
                                    <input type="checkbox"  id="Invoice" name="Invoice" value="<?php echo VOUCHER_INVOICE;?>" />
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_INVOICE,0);?>
                                    <span><input type="text" name="InvoiceStart" id="InvoiceStart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                    <td style="width:10%;"></td>

                              </tr>
                             
                              <tr>
                              <th style="background-color:#FFF;"></th>
                              <th>Debit Note</th>
                              <th>Credit Note</th>
                              </tr>
                              
                                <tr>
                                    <td  style="width:45%;font-size: 15px;" align="left">Note's</td>
                                    <td style="width:10%;"><input type="checkbox"  id="Chk_DebitNote" name="Chk_DebitNote" value="<?php echo VOUCHER_DEBIT_NOTE;?>"/>
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_DEBIT_NOTE, 0);?>
                                    <span><input type="text" name="DebitNotestart" id="DebitNotestart" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                    <td><input type="checkbox"  id="Chk_CreditNote" name="Chk_CreditNote" value="<?php echo VOUCHER_CREDIT_NOTE;?>"/>
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_CREDIT_NOTE, 0);?>
                                    <span><input type="text" name="CreditNote" id="CreditNote" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                </tr>
                              
                              <tr>
                              <th style="background-color:#FFF;"></th>
                              <th>Receipt</th>
                              <th>Payments</th>
                              </tr>  
                              <?php 
							  		
									for($i = 0 ; $i < sizeof($CashLedgerDetails); $i++)
							  		{ ?>
                                        <tr>
                                            <td  style="width:45%;font-size: 15px;" align="left"><?php echo $CashLedgerDetails[$i]['ledger_name']?></td>
                                            <td style="width:10%;"><input type="checkbox"  id="CashReceive<?php echo $i;?>" name="CashReceive<?php echo $i;?>" value="<?php echo VOUCHER_CASHRECEIVE;?>"/>
                                            <?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT, $CashLedgerDetails[$i]['id']);?>
                                            <span><input type="text" name="CashReceivestart<?php echo $i;?>" id="CashReceivestart<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                            </td>
                                            <td><input type="checkbox"  id="CashPay<?php echo $i;?>" name="CashPay<?php echo $i;?>" value="<?php echo VOUCHER_CASHPAY;?>"/>
                                            <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT, $CashLedgerDetails[$i]['id']);?>
                                            <span><input type="text" name="CashPaystart<?php echo $i;?>" id="CashPaystart<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                            </td>
                                        </tr>    
										
								<?php	}	?>
                                
                                   <?php if($IsSameCntApply == 1)
                                   { ?>
                                   <tr>
                                   <td style="width:45%">Single Counter for all banks</td>
                                   <td style="width:10%;"><input type="checkbox" id="SingleRcptCnt" name="SingleRcptCnt" value="<?php echo VOUCHER_RECEIPT;?>"/>
                                   <?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT,0);?>
                                   <span><input type="text" name="SingleVcrCntRcptSrt" id="SingleVcrCntRcptSrt" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                   </td>
                                   <td><input type="checkbox"  id="SinglePayCnt" name="SinglePayCnt" value="<?php echo VOUCHER_PAYMENT;?>"/>
                                   <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,0);?>
                                   <span><input type="text" name="SingleVcrCntPaySrt" id="SingleVcrCntPaySrt" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                   </td> 
                                   </tr>
                                   <?php }
                                   else
                                   { ?>
								   	 <tr>
                                    	<td  style="width:45%;font-size:15px;"><label style="padding-left:5px;">Check All Banks</label></td>
                                        <td><input type="checkbox"  id="CheckAll" name="CheckAll" onClick="CheckAllBankChkbox()" style="margin:0px;margin-left: 30px;"/></td>

									</tr> 
                                	<?php for($i = 0; $i < sizeof($BnkLedgerDetails); $i++ )
                                        { 
                                   ?>
                                   <tr>
                                    <td  id="LedgerName<?php echo $i;?>" name="LedgerName<?php echo $i;?>" style="width:45%;"><?php echo $BnkLedgerDetails[$i]['ledger_name'];?> : &nbsp;</td>
                                    <input type="hidden" id="LedgerID<?php echo $i;?>" name="LedgerID<?php echo $i;?>" value="<?php echo $BnkLedgerDetails[$i]['id'];?>"> 
                                    <td style="width:10%;"><input type="checkbox"  id="BankReceipt<?php echo $i;?>" name="BankReceipt<?php echo $i;?>" />
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_RECEIPT,$BnkLedgerDetails[$i]['id']);?>
                                    <span><input type="text" name="BnkLedgerStartRcpValue<?php echo $i;?>" id="BnkLedgerStartRcpValue<?php echo $i;?>" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                    <td><input type="checkbox"  id="BankPayment<?php echo $i;?>" name="BankPayment<?php echo $i;?>"/>
                                    <?php $Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,$BnkLedgerDetails[$i]['id']);?>
                                    <span><input type="text" name="BnkLedgerStartPayValue<?php echo $i;?>" id="BnkLedgerStartPayValue" class="field_input" value="<?php echo $Counter[0]['StartCounter'];?>" style="width:75px;margin-left:10%;"/></span>
                                    </td>
                                    </tr>
                                    <?php
                                        }
                                    }?>
                                 </td>
                             </tr>
                             <br />
                         </table>
                         <center> 
                         <table align="center">
                            <tr>
                                <td><input type="submit"  name="btn_submit"  id="btn_submit" value="Submit" class="btn btn-primary"   style="box-shadow: none;" /></td>
                            </tr>
                         </table>
                        </center>
                        <br/>
                        <input type="hidden" name="IsSameCntApply" id="IsSameCntApply" value="<?php echo $IsSameCntApply;?>">
                        <input type="hidden" name="NumberOfBank" id="NumberOfBank" value="<?php echo count($BnkLedgerDetails);?>">
                 </form>
			 </div>
             <br/>
  		 </div>
  	 </center>
   </div>
<?php if($_SESSION['APP_DEFAULT_SINGLE_COUNTER'] == 1)
	{ ?>
		<script>
			document.getElementById('BankCounter').checked = true;
        	document.getElementById('bankledgers').style.display = "none";
			document.getElementById('CommonCounter').style.display = "block";
			
       </script>
	<?php } ?>
<script type="text/javascript" src="lib/js/jquery.min.js"></script>	
<script type="text/javascript" src="lib/js/ajax.js"></script>
<script type="text/javascript" src="js/voucherCounter.js"></script>
<script language="javascript" type="application/javascript">


	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',3000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
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
	
	
	document.getElementById('default_year').disabled = true;
	document.getElementById('default_year').style.background = 'lightgray';
		
	</script>
    <script type="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/defaults_20190913.js"></script>
    <table width="200" border="1">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
</body>
</html>
<?php include_once "includes/foot.php"; ?>