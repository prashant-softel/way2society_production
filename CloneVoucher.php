<?php
	include_once("includes/head_s.php");
	include_once("classes/createvoucher.class.php");
	include_once("classes/utility.class.php");
	include_once("classes/dbconst.class.php");
	include_once("classes/PaymentDetails.class.php");
	$obj_utility = new utility($m_dbConn,$m_dbConnRoot );
	$obj_editvoucher=new createVoucher($m_dbConn);
	$obj_PaymentDetails = new PaymentDetails($m_dbConn);
	$datepicker=$obj_editvoucher->StartEndDate($_SESSION['default_year']);
	$minDate=$datepicker[0]['BeginingDate'];
	$maxDate=$datepicker[0]['EndingDate'];
	$Note = '';
	$Data=$obj_editvoucher->FetchData($_REQUEST['Vno']);
	$RowID = $Data[0]['RefNo'];
	$TABLE = $Data[0]['RefTableID'];	   
	$dropDownlist =  "select `id`,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.categoryid != ".DUE_FROM_MEMBERS." AND society_id='".$_SESSION['society_id']."' ";
	
	if(!isset($_REQUEST['bankid']))
	{
		$Counter = $obj_utility->GetCounter(VOUCHER_JOURNAL,0);
		//var_dump($Counter);
		$UpdatePayment = 0;
		if($TABLE <> TABLE_FD_MASTER) 
		{
			$dropDownlist .= " and ledgertable.categoryid NOT IN(".BANK_ACCOUNT.",".CASH_ACCOUNT.") ";	
		}
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
		
		
		$ReconcileDetails = $obj_PaymentDetails->getReconcileStatus($_REQUEST['Vno'],$_REQUEST['bankid']);	
		$IsSameCntApply = $obj_utility->IsSameCounterApply();
		if($IsSameCntApply == 1)
		{
			$Counter = $obj_utility->GetCounter(VOUCHER_PAYMENT,0);	
		}
		else
		{
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
	
	$dropDownlist .=" ORDER BY ledgertable.ledger_name ASC"; 

	$DateString = 'Voucher Date';
	$width = '25';
	if(isset($_REQUEST['bankid']))
	{
		if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
		{
			$TABLE = TABLE_PAYMENT_DETAILS;
			$DateString = 'Cheque Date';
			
			if(isset($_REQUEST['LeafID']) && $_REQUEST['LeafID'] == -1)
			{
				$DateString = 'Voucher Date:';	
			}
			
			$Head = 'Payment';
			$RefID = $Data[0]['RefNo'];
			$chequeNumber = $Data[0]['ChequeNumber']; 
			$PaymentMode = $Data[0]['ModeOfPayment'];
			$Data = $obj_utility->reverseVoucherEntry($Data);
			$Data = array_reverse($Data);
			//var_dump($Data);
			
		}
		else if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
		{
			$TABLE = TABLE_CHEQUE_DETAILS;
			$Head = 'Receipt';
			$receiptData =  $obj_editvoucher->getChequeDate($Data[0]['RefNo']);
			$width = '50';
		}
	}
	else
	{
		$Head = 'Journal';	
	}
	
	if($Data[0]['RefTableID'] == TABLE_FIXEDASSETLIST)
	{
		$TABLE = TABLE_FIXEDASSETLIST;
	}
	
	
?>


<html>
<head>
	
     
    <script type="text/javascript" src="js/jscreatevoucher_20190706.js"></script>
   	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
   <script>SetAry_ExitingExCounter(<?php echo json_encode($Counter[0]['ExitingCounter']);?>)</script>
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
		
		 $(function()
       	 {
			 var VoucherCounter = <?php echo $Data[0]['ExternalCounter']; ?>;
             document.getElementById('Current_Counter').value = <?php echo $Counter[0]['CurrentCounter']?>;
			 document.getElementById('OnPageLoadTimeVoucherNumber').value = VoucherCounter;
			 <?php if(isset($_REQUEST['bankid']))
			{ ?>
				<?php if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
				{
					if(isset($_REQUEST['CustomLeaf']) && $_REQUEST['CustomLeaf'] == 1)
					{ ?>
						document.getElementById('ModeOfPayment').value = <?php echo $PaymentMode?> ;						
					<?php }
					if($ReconcileDetails[0]['ReconcileStatus'] == 1)
					{ ?>
						document.getElementById('ChequeNumber').readOnly = true;
						document.getElementById('ChequeNumber').style.backgroundColor = 'lightgray';
						document.getElementById('voucher_date').readOnly = true;	
						document.getElementById('voucher_date').style.backgroundColor = 'lightgray';
					<?php }
					?>
	
				<?php }
				else if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
				{ ?>
					document.getElementById('BillType').value = <?php echo $receiptData[0]['BillType']?> ;
				<?php }
			} ?>
			
			<?php if($_SESSION['role'] <> ROLE_SUPER_ADMIN)
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
			document.getElementById('ChequeNumber').parentElement.style.display = 'none';
			
		<?php } ?>
			
			
		});
		 
    </script>
   
	<script type="application/javascript">
	
	
	<?php
	$totalrows=$obj_editvoucher->Totalrows($_REQUEST['Vno']);
	//echo 'totalros'.$totalrows;
		?>
	//document.getElementById('maxrows').value=<?php //echo json_encode($totalrows); ?>;
	
	var rowCounter=<?php echo json_encode($totalrows); ?>;
	
	var VoucherNo=<?php echo json_encode($_REQUEST['Vno']); ?>;
	//alert(VoucherNo);
	
	var PageName=<?php echo json_encode($_REQUEST['pg']); ?>;
	//alert(PageName);
	
	
	
	
	function showbutton(status)
	{		
	//document.getElementById('Vno').value=VoucherNo;
	document.getElementById('page').value=PageName;		
					
			if(status==true) 
			{
            document.getElementById('submit').disabled=false;
			document.getElementById('submit').style.backgroundColor = '#BDD8F4';
			}
			 else 
			{
				document.getElementById('submit').disabled=true;
				document.getElementById('submit').style.backgroundColor = 'lightgray';
			}
			
			 for(var i=1;i<=rowCounter;i++)
					{
						//alert(rowCounter);
						
						//alert("for");
						var ByTo = document.getElementById('byto'+ i).value;
			
						//alert(ByTo);
						if(ByTo=='BY')
						{
						//alert("5");
						
						
						document.getElementById("Debit" + i).disabled=false;
						document.getElementById("Credit" + i).disabled=true;
						document.getElementById("Credit" + i).style.backgroundColor = 'lightgray';
						
						// If It is Payment transaction and Reconcile then user can't change the amount
						<?php if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1 && $ReconcileDetails[0]['ReconcileStatus'] == 1){
						?>
						document.getElementById("Debit" + i).readOnly = true;
						document.getElementById("Debit" + i).style.backgroundColor = 'lightgray';	
							
						<?php } ?>
						
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
							
							// If It is Payment transaction and Reconcile then user can't change the amount
							<?php if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1 && $ReconcileDetails[0]['ReconcileStatus'] == 1){
							?>
							document.getElementById("Credit" + i).readOnly = true;
							document.getElementById("Credit" + i).style.backgroundColor = 'lightgray';	
								
							<?php } ?>
						}
					}
	}
	
	
	
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
	
	
	function AddNewRow()
	{
		//alert("1");
		rowCounter++;	
		
		//alert("2");
		var varRow  ="<tr><td><select id='byto"+ rowCounter+"' name='byto"+ rowCounter+"'  style='width:100px;' onChange='changesValues("+ rowCounter +")'><option value='BY'>BY</option><option value='TO' selected >TO</option> </select></td>";	
		
		varRow += "<td><select id='To" + rowCounter+"' name='To"+ rowCounter+"' style='width:250px;' > <?php echo $By = $obj_editvoucher->combobox($dropDownlist,0);?></select></td>";
		
		varRow +="<td><input type='text' id='Debit"+ rowCounter+"' name='Debit"+ rowCounter+"' style='width:120px' onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";
		
		varRow +="<td><input type='text' id='Credit" + rowCounter+"' name='Credit" + rowCounter+"' style='width:120px' onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";	
		<!--varRow +="<td ><input type='text' id='Note" + rowCounter+"' name='Note" + rowCounter+"' style='width:200px' ></td>";-->
		
		varRow +="<td><p id='label"+ rowCounter +"' name='label"+ rowCounter +"' style='color:#00FF00' readonly></p></td></tr>";
	
		document.getElementById('maxrows').value = rowCounter;
		$("#table_details > tbody").append(varRow);
		//document.getElementById("Credit" + rowCounter).focus();
		
	
	
	}
	
	
	function AddValues(addRowFlag)
	{
		var Credit=0;
		var Debit=0;
		
		for(var i=1;i<=rowCounter;i++)
		{
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
		
		document.getElementById("credittotal").innerHTML='<b>' + Credit + '</b>';
		document.getElementById("debittotal").innerHTML= '<b>' +Debit + '</b>';
		
		if( Debit!=Credit )
		{
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
			document.getElementById("credittotal").style.backgroundColor = '#7FE817';
			document.getElementById("debittotal").style.backgroundColor = '#7FE817';
			
			document.getElementById('maxrows').value=rowCounter;
			showbutton(true);
			return true;
		}
		
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
	
	</script>

    
</head>
<?php if(isset($_REQUEST['ShowData']) ){ ?>
<body onLoad="go_error()">
<?php } ?>

<div id="middle">
    <br><div class="panel panel-info" id="panel" style="display:none; min-height:450px;">
    <div class="panel-heading" id="pageheader">Clone <?php echo $Head;?></div>	

<center>
<br>

<form name="createvoucher" id="createvoucher" method="post"  action="process/createvoucher.process.php" onSubmit="return doFormSubmit(minStartDate,maxEndDate);" >
<table  id="table_details" width="100%" style="text-align:center;" >

    <tbody>


   
            <input type="hidden" name="maxrows" id="maxrows" />
            
            <input type="hidden" name="Vno" id="Vno" />
            <input type="hidden" name="mode" id="mode" value=<?php echo ADD; ?>>
              <input type="hidden" name="Current_Counter" id="Current_Counter" value="" />
            <input type="hidden" name="IsCallUpdtCnt" id="IsCallUpdtCnt" value="1" />
	    	<input type="hidden" name="OnPageLoadTimeVoucherNumber" id="OnPageLoadTimeVoucherNumber" value="" />
          	<input type="hidden" name="LeafID" id="LeafID" value="<?php echo $_REQUEST['LeafID'];?>" />
            <input type="hidden" name="CustomLeaf" id="CustomLeaf" value="<?php echo $_REQUEST['CustomLeaf'];?>" />
			<input type="hidden" name="Updatetable" id="Updatetable" value="<?php echo $TABLE;?>" />
            <input type="hidden" name="BankID" id="BankID" value="<?php echo $_REQUEST['bankid'];?>" />
            
            
            <!-- Below hidden input only when any payment entry update-->
            
          	
            <input type="hidden" name="Updatetable" id="Updatetable" value="<?php echo $TABLE;?>" />
            <input type="hidden" name="RowID" id="RowID" value="<?php echo $RowID;?>" />
            
             <input type="hidden" name="page" id="page" />
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
           
            <?php 
           
		   if($_REQUEST['Vno'] > 0)
		   {
			$GrossAmount=0;  
			$IGST=0;
			$CGST=0;
			$SGST=0;
			$CESS=0;
			   
		  // $Data=$obj_editvoucher->FetchData($_REQUEST['Vno']);
		   $Note = $Data[0]['Note'];
		   $IGST= $Data[0]['IGST_Amount'];
		   $CGST= $Data[0]['CGST_Amount'];
		   $SGST= $Data[0]['SGST_Amount'];
		   $CESS= $Data[0]['CESS_Amount'];
		   $AmountDebit=$Data[0]['Debit'];
		   $TotalAmount += (float)$Data[0]['IGST_Amount']+(float)$Data[0]['CGST_Amount']+(float)$Data[0]['SGST_Amount']+(float)$Data[0]['CESS_Amount'];
		   $GrossAmount=(float)$Data[0]['Debit']-(float)$TotalAmount;
		  
		  
		    echo "<tr align='center'>
				  <td colspan='4'>
                  <table width='100%'>";
				  
				  if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
				  {
					  echo "<tr style='font-size:14px;'><td colspan='4' align = 'center' style=' padding-bottom: 15px;'>".$DateString.": <input type='text' name='voucher_date' id='voucher_date'  class='basics' size='10'   style='width:100px;' value=''  placeholder='dd-mm-yyyy'></td></tr>";
				  }
                 echo "<tr>";
				 if(!isset($_REQUEST['receipt']))
				  {
					   echo "<td style='font-size:14px; width:".$width."%;' align='left'>".$DateString.": <input type='text' name='voucher_date' id='voucher_date'  class='basics' size='10'   value='' style='width:100px;'  placeholder='dd-mm-yyyy'>";
				  }
				  else
				  {
					  echo "<td style='font-size:14px; width:".$width."%;' align='left'>";
				  }
                   
					if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
					{
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cheque Date : <input type='text' name='cheque_date' id='cheque_date'  class='basics' size='10'   style='width:100px;' value=''  placeholder='dd-mm-yyyy'>";
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bill Type : <select style='width:140px;' name='BillType' id='BillType'><option value='0'>Regular bill</option><option value='1'>Supplementry bill</option><option value='2'>Invoice bill</option></select>";
					}
	
				echo "	<input type='hidden' id='InvoiceStatusID' name='InvoiceStatusID' value=".$Data[0]['InvoiceStatusID'].">
</td>";
					echo "<td style='font-size:14px; width:25%;' align='left'>Voucher Number: <input type='text' name='VoucherNumber' id='VoucherNumber' size='7'   style='width:100px;'  value=".$Counter[0]['CurrentCounter']." required /></td>";		  
					 if(isset($_REQUEST['bankid']))
					  {
						  if(isset($_REQUEST['payment']) && $_REQUEST['payment'] == 1)
						  {
							  if(isset($_REQUEST['CustomLeaf']) && $_REQUEST['CustomLeaf'] == 1)
							  {
							  	echo "<td style='width:25%;font-size:14px'>Mode of Payment : <select id='ModeOfPayment' style='width:100px;' name='ModeOfPayment' onChange='ModeOfPaymentChanged(this)'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>";
							  }
                              echo "<td style='font-size:14px'>ChequeNumber : <input type='text'  id='ChequeNumber' name='ChequeNumber'  value='".$chequeNumber."'style='width:70px;'></td>"; 
							}
						  else if(isset($_REQUEST['receipt']) && $_REQUEST['receipt'] == 1)
						  {
							  echo "<td style='font-size:14px'>ChequeNumber : <input type='text'  id='ChequeNumber' name='ChequeNumber'  value='".$receiptData[0]['ChequeNumber']."'style='width:70px;'></td>"; 	  
						  }  
					  }

							  
					  if($Data[0]['is_invoice'] > 0)
					  {
            		echo "<td  style='font-size:14px; width:15%' align='left'>  Is Invoice: <input type='checkbox' id='is_invoice' name='is_invoice'  value=".$Data[0]['is_invoice']." style='margin-top: -2px;box-shadow: 1px 1px 4px #f9f9f9;' checked  onChange='AddInvice(this.value);'></td>";
           		    echo "<td>
						 <table style='width: 15%;margin-top: -20px; display:block;'  id='InvoiceStatus'>
                         <tr>
						 <th style='text-align: left; width:15%'>Invoice No</th>";
						 if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
						 {
                        echo" <th style='text-align: center; width:10%'>Gross Amount</th>
                         <th style='text-align: center; width:10%'>IGST</th>
                         <th style='text-align: center; width:10%'>CGST</th>
                         <th style='text-align: center; width:10%'>SGST</th>
                         <th style='text-align: center; width:10%'>CESS</th>
						 </tr>";
						 }
						 echo "<tr>
                         <td><input type='text' id='invoice_no' name='invoice_no' style='width: 100px;' value=".$Data[0]['NewInvoiceNo']."></td>";
						 if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
						 {
						 
                         echo "<td><input type='text' name='igst_amount' id='igst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$GrossAmount." readonly></td>
                         <td><input type='text' name='igst_amount' id='igst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['IGST_Amount']."></td>
                         <td><input type='text' name='cgst_amount' id='cgst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['CGST_Amount']." ></td>
                         <td><input type='text' name='sgst_amount' id='sgst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['SGST_Amount']." ></td>
                         <td><input type='text' name='cess_amount' id='cess_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['CESS_Amount']." ></td>";
						 }
			echo "</tr></table>
                         </td>";
					  }
					  else{
						  	if(!isset($_REQUEST['bankid']))
							{
								echo "<td  style='font-size:14px; width:15%' align='left'>  Is Invoice: <input type='checkbox' id='is_invoice' name='is_invoice'  value='0' style='margin-top: -2px;box-shadow: 1px 1px 4px #f9f9f9;'  onChange='AddInvice(this.value);'></td>";  
								echo "<td>
								 <table style='width: 80%;margin-top: -20px; display:block;'  id='InvoiceStatus'>
								 <tr>
								 <th style='text-align:left;width:15%;'>Invoice No</th>";
								 if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
								 { 
									echo "<th style='text-align: center; width:10%'>&nbsp;</th>
									 <th style='text-align: center; width:10%'>IGST</th>
									<th style='text-align: center; width:10%'>CGST</th>
									<th style='text-align: center; width:10%'>SGST</th>
									<th style='text-align: center; width:10%'>CESS</th>";
								 }
								 echo " </tr><tr>
								 <td><input type='text' id='invoice_no' name='invoice_no' style='width: 100px;'  value=".$Data[0]['NewInvoiceNo']."></td>";
								  if($_SESSION['sgst_input']!=0 && $_SESSION['cgst_input']!= 0)
								 { 
								 echo "<td>&nbsp;</td>
								 <td><input type='text' name='igst_amount' id='igst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['IGST_Amount']." ></td>
								 <td><input type='text' name='cgst_amount' id='cgst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['CGST_Amount']."></td>
								 <td><input type='text' name='sgst_amount' id='sgst_amount' style='width:80px;' onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['SGST_Amount']."></td>
								 <td><input type='text' name='cess_amount' id='cess_amount' style='width:80px;'  onKeyUp='extractNumber(this,2,true);' value=".$Data[0]['CESS_Amount']." ></td>";
								 }
								echo "</tr></table>
											 </td>";
							}
						}
					  echo "</tr></table></tr>";
			
		   /* echo "<tr align='center'>";
                        
                        echo "<td colspan='5' style='font-size:14px;' align='left'>Due Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='text' name='due_date' id='due_date'  class='basics' size='10'   style='width:100px;' value=".$Data[0]['Date']."></td>";
            echo "</tr>";*/
			
           /* 
            echo "<tr> <td colspan='5'><br/></td></tr>";
    
            //print_r($Data);
            
            
            
        
            echo "<tr align='center'>";
            echo "<td colspan='5' style='font-size:14px;'> Entry Details Note:<textarea id='Note' name='Note' style='width:400px'  maxlength='1000' rows='2' cols='50'  required>".$Data[0]['Note']."</textarea></td> </tr>";*/
           
		 	  echo "<tr> <td colspan='5'><br/></td></tr>"; 
		   	if($Data <> 0)
			{
				
				
				
				echo "</tr>";	
				echo "<tr align='center' height='30px'>";
                echo "<th style='width:15%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;'>By(Debit)/To(Credit)</th>";     	
                echo "<th style='width:40%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;'>Ledger Name</th>";
                echo "<th style='width:25%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;'>Debit</th>";
                echo "<th style='width:25%;text-align:center;background-color:#337ab7;font-size:14px;color: #fff;padding-top: 5px;'>Credit</th>";
                echo "</tr>";
				echo "<tr> <td colspan='5'><br/></td></tr>"; 
				$rowCounter=0;
				
				//print_r($Data);
				foreach($Data as $key=>$val)
				{
					
					//print_r($Data);
							$rowCounter++;
							if($Data[$key]['By'] <> '')
							{
								
								
									echo "<tr><td><select id='byto".$rowCounter."' name='byto".$rowCounter."'  style='width:100px;' onChange='changesValues(".$rowCounter.")'><option value='BY' selected>BY</option><option value='TO'  >TO</option> </select></td>";	
									
									echo "<td><select id='To".$rowCounter."' name='To".$rowCounter."' style='width:250px;' >". $obj_editvoucher->combobox($dropDownlist, $Data[$key]['By'])."</select></td>";
						
									echo"<td><input type='text' id='Debit".$rowCounter."' name='Debit".$rowCounter."' style='width:120px'  value=".$Data[$key]['Debit']." onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";
						
									echo"<td><input type='text' id='Credit".$rowCounter."' name='Credit".$rowCounter."' style='width:120px' onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";	
						
									echo"<td><p id='label".$rowCounter."' name='label".$rowCounter."' style='color:#00FF00' readonly></p></td></tr>";
									
									//$obj_editvoucher->DeletePreviousRecords($Data[$key]['By'],$_REQUEST['Vno'],$Data[$key]['Debit'],$Data[$key]['Date'],$Data[$key]['id'],'By');
							}
							else
							{
								
								
								
									echo "<tr><td><select id='byto".$rowCounter."' name='byto".$rowCounter."'  style='width:100px;' onChange='changesValues(".$rowCounter.")'><option value='BY' >BY</option><option value='TO' selected >TO</option> </select></td>";	
						
									echo "<td><select id='To".$rowCounter."' name='To".$rowCounter."' style='width:250px;' >". $obj_editvoucher->combobox($dropDownlist, $Data[$key]['To'])."</select></td>";
						
									echo"<td><input type='text' id='Debit".$rowCounter."' name='Debit".$rowCounter."' style='width:120px'   onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";
						
									echo"<td><input type='text' id='Credit".$rowCounter."' name='Credit".$rowCounter."' style='width:120px' value=".$Data[$key]['Credit']." onBlur='AddValues(true);' onKeyUp='extractNumber(this,2,true);'></td>";	
						
									echo"<td><p id='label".$rowCounter."' name='label".$rowCounter."' style='color:#00FF00' readonly></p></td></tr>";
								
								    //$obj_editvoucher->DeletePreviousRecords($Data[$key]['To'],$_REQUEST['Vno'],$Data[$key]['Credit'],$Data[$key]['Date'],$Data[$key]['id'],'To');
							}
					
					?>
                    
					<script>
                    		document.getElementById('maxrows').value="<?php echo $rowCounter ?>";
							
                     </script>
			<?php
				}
				?>
                
						
		<?php			
			}
		   
		  
		   }
            ?>
           
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
                       <td colspan="5" style="font-size:14px;"> Entry Details Note:<textarea id="Note" name="Note" style="width:400px; height:40px"  maxlength="1000" rows="2" cols="50"  required><?php echo  $Note ?></textarea></td>
          </tr>
          
     	<tr> <td colspan="5"><br/></td></tr>   
        
        <tr align="center"><td colspan="5" style="font-size:12px;"><input type="submit" name="submit" id="submit" value="Submit"  style="width:120px; height:30px; background-color:lightgray;" ></td>
        </tr>
        
    </tbody>

</table>
</form>
<script>
 showbutton(true);
 AddValues(false);
  AddInvice();
 function AddInvice(Value)
{ 
//alert(Value);
    if(document.getElementById('is_invoice').checked==false)
	{
	document.getElementById('InvoiceStatus').style.display='none';	
	//document.getElementById('is_invoice').value='0';
	//document.getElementById('is_invoice').value='0';	
	
	}
	else
	{
		document.getElementById('InvoiceStatus').style.display='table';
		//document.getElementById('is_invoice').value='1';
	}
	
}
</script>

</form>

<?php
if(IsReadonlyPage() == true)
{
	echo '<center><div style="padding-top: 150px;"><font  color="#FF0000" style="font-weight:bold;font-size:16px;">Selected financial year has been locked,hence editing journal voucher entry is not allowed.</font></div></center>';
?>
	<script>
		document.getElementById('editvoucher').style.display = 'none';
	</script>
<?php			
}
?>
</center>
</div>
</div>

</body>

</html>
<?php include_once "includes/foot.php"; ?>