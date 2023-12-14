<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
require_once("classes/CsvOperations.class.php");
include_once("classes/import_bank_statement.class.php");
include_once("classes/bank_statement.class.php");
include_once("classes/utility.class.php");

$validator = new CsvOperations();
$m_dbConn = new dbop();
$import_bank_statement = new Import_Bank_Statement();
$bank_statement = new bank_statement($m_dbConn);
$obj_utility = new utility($m_dbConn);

if(isset($_REQUEST['submit']) && (isset($_REQUEST['from_date']) || isset($_REQUEST['to_date'])))
{
	$BeginDate = getDBFormatDate($_REQUEST['from_date']);
	$EndDate = getDBFormatDate($_REQUEST['to_date']);
	$FinalData = $import_bank_statement->Compare_Bank_Statement_Data($_REQUEST['LedgerID'],$BeginDate,$EndDate);	
}
else
{
	$FinalData = $import_bank_statement->Compare_Bank_Statement_Data($_REQUEST['LedgerID']);	
}

$BankName = $obj_utility->getLedgerName($_REQUEST['LedgerID']);
$MatchData = $FinalData[MATCH_ENTRY];
$AMOUNT_MATCH_Data = $FinalData[AMOUNT_MATCH];
$PRESENT_IN_BANK_Data = $FinalData[PRESENT_IN_BANK];
$PRESENT_IN_W2S_Data = $FinalData[PRESENT_IN_W2S];
?>
	
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<meta charset="UTF-8">
<style>
.tab-content{
	margin:0px;
	}
</style>
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery_min.js"></script>
<script type="text/javascript" src="js/jquery_min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>

<script language="javascript" type="text/javascript">



$(document).ready(function(e) {
    
	if(<?php echo $_SESSION['default_suspense_account'];?> == 0)
	{
		alert("Please first set the suspense ledger on default setting page");
		window.location.href = 'defaults.php';
		return false;
	}
	
	
	 
	// #myInput is a <input type="text"> element
	
	
});
/*var table = $('#example').DataTable();

$('#myInput').on( 'keyup', function () {
		console.log("Test");
		table.search( this.value ).draw();
	} );
*/


function selectradiobutton(chkBox,lineno)
{
	if((chkBox).checked == true)
	{
		$("#amount_match_radio_"+lineno+"_0").prop("checked", true);		
	}
	else
	{
		$('input[name=amount_match_radio_'+lineno+']').prop("checked", false);
	}
}

function SelectAll(chkBox)
{
	if(chkBox.id == 'chk_all_match')
	{
		var length = $('#match_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			document.getElementById('chk_match_' + iCnt).checked = chkBox.checked?true:false;
		}
	}
	else if(chkBox.id == 'chk_all_bank_amount_match')
	{
		
		var length = $('#amount_match_count').val();
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			document.getElementById('chk_bank_amount_match_' + iCnt).checked = chkBox.checked?true:false;
			selectradiobutton(chkBox,iCnt)
		}
	}
	else if(chkBox.id == 'chk_all_present_in_bank')
	{
		var length = $('#bank_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			document.getElementById('chk_present_in_bank_' + iCnt).checked = chkBox.checked?true:false;
		}
	}
	else if(chkBox.id == 'chk_all_present_in_w2s')
	{
		var length = $('#w2s_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			document.getElementById('chk_present_in_w2s_' + iCnt).checked = chkBox.checked?true:false;
		}
	}
}


function ReconcileSubmit(flag)
{
	var transactionNo = [];
	var SelectedData = [];
	var RadioSelection = [];
	var text = '';
	var bankid = document.getElementById('bankid').value;
	
	if(flag == 1)
	{
		text = "Reconcile";
        var Data = <?php echo $obj_utility->safe_json_encode($MatchData); ?>;
		
		var length = $('#match_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			if(document.getElementById('chk_match_' + iCnt).checked == true)
			{
				SelectedData.push(Data[iCnt]);
			}
		}
	}
	else if(flag == 4)
	{
		var MATCH_AMOUNT = 4
		text = "Reconcile";
		var Data = <?php echo $obj_utility->safe_json_encode($AMOUNT_MATCH_Data); ?>;	
		var length = $('#amount_match_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			if(document.getElementById('chk_bank_amount_match_' + iCnt).checked == true)
			{
				SelectedData.push(Data[iCnt]);
				var selectRadioValue = $('input[name=amount_match_radio_'+iCnt+']:checked').val();
				RadioSelection.push(selectRadioValue);
			}
		}
	}
	else if(flag == 2)
	{
        text = "Added";
        
		var Data = <?php echo $obj_utility->safe_json_encode($PRESENT_IN_BANK_Data); ?>;	
		var length = $('#bank_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			if(document.getElementById('chk_present_in_bank_' + iCnt).checked == true)
			{
			    SelectedData.push(Data[iCnt]);	
			}
		}
	}
	else if(flag == 3)
	{
		text = "Deleted";
		var Data = <?php echo $obj_utility->safe_json_encode($PRESENT_IN_W2S_Data); ?>;
		var length = $('#w2s_count').val();
		
		for(var iCnt = 0 ; iCnt < length ; iCnt++)
		{
			if(document.getElementById('chk_present_in_w2s_' + iCnt).checked == true)
			{
				SelectedData.push(Data[iCnt]);				
			}
		}
	}
	
	
	console.log(SelectedData);
	
	//return false;
	
	if(SelectedData == "")
	{
		alert("Please select the checkbox");
		return false;
	}
	
	
	//console.log(SelectedData);
	
	
	$.ajax({
			url : "ajax/ajaxBankDetails.php",
			type : "POST",
			data:  {"method" : 'ReconcileBankRegister', 'data' : JSON.stringify(SelectedData), 'RadioSelection':JSON.stringify(RadioSelection), "flag": flag,"bankid":bankid},
			success : function(data)
			{	
				var result = data.split('###');
				console.log("Result : ",result);
				console.log("Result ",result[2]);
				
				if(result[2] != 0 && result[2] != null)
				{
					if(result[1] > 0)
					{
						$('.formSubmitResponse').text(result[1]+" entries "+text+" successfully, But "+result[3]+" not reconciled due to: "+result[2]+" Cheque Number already exits in payment");		
					}
					else
					{
						$('.formSubmitResponse').text(result[3]+" entries not reconciled due to: "+result[2]+" Cheque Number already exits in payment");	
					}
					$('#myModal').modal();
				}
				else if(result[1] > 0)
				{
					$('.formSubmitResponse').text(result[1]+" Entries "+text+" Successfully");
					$('#myModal').modal();
				}
				else
				{
					$('.formSubmitResponse').text("Entries Not "+text+" Successfully");						
					$('#myModal').modal();
				}
			}
		});
	
	
	
	
}

 $(function()
		{
			minGlobalCurrentYearStartDate = '<?php echo $_SESSION['from_date'];?>'; 
			maxGlobalCurrentYearEndDate = '<?php echo $_SESSION['to_date'];?>'; 
			$.datepicker.setDefaults($.datepicker.regional['']);
			$(".basics").datepicker({ 
			dateFormat: "dd-mm-yy", 
			showOn: "both", 
			buttonImage: "images/calendar.gif", 
			buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
		})});

function closedialoge()
{
	window.location.reload();
}

function go_error()
{
    setTimeout('hide_error()',10000);   
}
function hide_error()
{
    document.getElementById('error').style.display = 'none';
}
</script>
</head>

<body onLoad="go_error();">  
<div class="panel panel-info" style="border:none;width:100%">
  
    <div class="panel-heading" style="font-size:20px;text-align: center;">
       Bank Reconcilation Report (<?php echo $BankName?>)
    </div>
    <br />
   		<form name="AutoRecoDateForm" id="AutoRecoDateForm" action="import_bank_statement_preview.php?LedgerID=<?php echo $_REQUEST['LedgerID'];?>" method="post">
	      <table style="width:100%; border:1px solid black; border-radius: 10px;background-color:transparent; ">
            <tr> <td colspan="3"><br/> </td></tr>
            <tr>
                <td style="width:25%;"></td> 
                <td><b> From : </b></td>                      
                <td><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php echo $_SESSION['from_date']; ?>"/></td>
                <td><b> To :</b></td>                     
                <td><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;" value="<?php echo $_SESSION['to_date']; ?>"/></td>
                <td><input type="submit" name="submit" value="Submit" class="btn btn-primary" /> </td>
                <td style="width:25%;"></td>	
            </tr>        
            <tr> <td colspan="3"><br/> </td></tr>
        </table>
	</form>
    <div class="panel-body">                        
        <div class="table-responsive">
         <ul class="nav nav-tabs" role="tablist">
            <li class="active"> 
            	<a href="#fwv-1" role="tab" data-toggle="tab">Match Statement</a>
    		</li>
            <li>
            	<a href="#fwv-2" role="tab" data-toggle="tab">Only Amount Match</a>
    		</li>
            <li>
            	<a href="#fwv-3" role="tab" data-toggle="tab">Present In Bank</a>
    		</li>
            <li>
            	<a href="#fwv-4" role="tab" data-toggle="tab">Present In W2s</a>
    		</li>
         </ul>
         <br />
         
        <div class="tab-content no-margin">
        <div class="tab-content">
            <div class="tab-pane active" id="fwv-1">
                <center>
                <br>
             <center><div class="btn btn-primary" style="color:white"><i class="fa fa-check" style="color:white;margin-top: 3px;margin-right: 6px;"></i><a target="_blank" name="auto_reconcile_match" id="auto_reconcile_match" onClick="ReconcileSubmit(<?php echo MATCH_ENTRY;?>)" style="text-decoration: none;color: white;">Submit Reconcilation</a></div></center>
             <br/>
                <div class="panel panel-info" id="panel" style="display:none">
                    <div class="panel-heading" id="pageheader">Match Statement</div>
                     <form name="bank_reconciliation" id="bank_reconciliation" method="post">
                <br />

                
                <style>
                td{vertical-align:middle}
                .tooltip2 {
                    position: relative;
                    border-bottom: 1px dotted black;
                    opacity: inherit;
                }
                
                .tooltip2 .tooltiptext {
                    visibility: hidden;
                    width: 155px;
                    background-color: #fff;
                    color: black;
                    text-align: center;
                    border-radius: 6px;
                    padding: 5px 0;
                
                    /* Position the tooltip */
                    position: absolute;
                    z-index: 1;
                    border: 1px solid black;
                    left: 10px;
                    top: 26px;
                }
                
                .tooltip2:hover .tooltiptext {
                    visibility: visible;
                }
                </style>
                <style type="text/css">
                 
                  /*table.cruises { 
                    font-family: verdana, arial, helvetica, sans-serif;
                    font-size: 11px;
                    cellspacing: 0; 
                    border-collapse: collapse; 
                    width: 535px;    
                    }*/
                  table.cruises td { 
                    border-left: 1px solid #999; 
                    border-top: 1px solid #999;  
                    padding: 2px 4px;
                    }
                  table.cruises tr:first-child td {
                    border-top: none;
                  }
                  table.cruises th { 
                    border-left: 1px solid #999; 
                    padding: 2px 4px;
                    background: #6b6164;
                    color: white;
                    font-variant: small-caps;
                    }
                  table.cruises td { background: #eee; overflow: hidden; }
                  
                  div.scrollableContainer { 
                    position: relative; 
                    padding-top: 6em;
                    width:100%;
                    margin: 0px; 
                    border: 1px solid #999;   
                   }
                  div.scrollingArea { 
                    height: 600px; 
                    overflow: auto; 
                    }
                
                  table.scrollable thead tr {
                    left: -1px; top: 0;
                    position: absolute;
                    }
                
                 
                
                </style>
                
                <!-- <table width="90%"> -->
                <div class="scrollableContainer">
                 <div class="scrollingArea">
                <table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped display" id="example">
                <?php if(isset($_POST['ShowData']))
                    {
                ?>
                        <tr height='30'><td colspan='12' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
                <?php
                    }?>
                <thead style="left: -1px; top: 0;
                    position: absolute;">    
                <tr>
                    <th style="width:4%;text-align:center;border:1px solid black;"><input type="checkbox" id="chk_all_match" onClick="SelectAll(this);"></th>
                    <th style="width:4%;text-align:center;border:1px solid black;">Sr No.</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Cheque Date</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Ref.</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Ledger Name</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Voucher Type</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Withdrawals</th>
                    <th style="width:8%;text-align:center;border:1px solid black;">Deposits</th>  
                    <th style="width:8%;text-align:center;border:1px solid black;">Bank Statement Desc</th> 
                    <th style="width:8%;text-align:center;border:1px solid black;">W2S Desc</th>  
                    <th style="width:8%;text-align:center;border:1px solid black;">Status</th>     
                </tr>
                </thead>
                
                <!--<tr> <td colspan="10"><br/> </td></tr>-->
                <tbody>
                
                    
                    <?php for($i = 0; $i < count($MatchData); $i++)
                    { ?>
                    
                    <tr>
                        <td style="width:8%;text-align:center;"><input type="checkbox" value="1" id="chk_match_<?php echo $i; ?>"></td>
                        <td style="width:8%;text-align:center;"><?php echo $i+1; ?></td>
                        <td style="width:10%;text-align:center;"><?php echo getDisplayFormatDate($MatchData[$i]['Date']);?></td>
                        <td style="width:8%;text-align:center;"><?php echo $MatchData[$i]['ChequeNumber'];?></td>
                        <td style="width:8%;text-align:center;"><?php echo $MatchData[$i]['LedgerName'];?></td>                         
                        <td style="width:8%;text-align:center;"><?php echo $import_bank_statement->getVoucherName($MatchData[$i]['VoucherType']);?></td>
                        <td style="width:8%;text-align:center;"><?php echo number_format($MatchData[$i]['Debit'],2);?></td>
                        <td style="width:8%;text-align:center;"><?php echo number_format($MatchData[$i]['Credit'],2);?></td>
                        <td style="width:8%;text-align:center;"><?php echo $MatchData[$i]['Bank_Description'];?></td>
                        <td style="width:8%;text-align:center;"><?php echo $MatchData[$i]['W2s_Comments'];?></td>
                        <td style="width:8%;text-align:center;"><?php echo $MatchData[$i]['error_msg'];?></td>
                    </tr>	
                        
                        
                    <?php }?>
                </tbody>
                </table>
                </div>
                </div>
                </form>
                </div>
                </center>
                
                            
                 <div class="clear m-b"></div>
            </div>
            <div class="tab-pane fade" id="fwv-2">
                 <center>
                    <br>
                    <div class="panel panel-info" id="panel" style="display:block">
                        <div class="panel-heading" id="pageheader">Bank Statement Amount Matched With W2s</div>
                         <form name="bank_reconciliation" id="bank_reconciliation" method="post">
                    <br />
                    <center><div class="btn btn-primary" style="color:white"><i class="fa fa-check" style="color:white;margin-top: 3px;margin-right: 6px;"></i><a target="_blank" name="bank_amount_match" id="bank_amount_match" onClick="ReconcileSubmit(<?php echo AMOUNT_MATCH;?>)" style="text-decoration: none;color: white;">Submit Reconcilation</a></div></center>
             <br/>
                    <!-- <table width="90%"> -->
                    <div class="scrollableContainer">
                     <div class="scrollingArea">
                    <table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped " id="example">
                    <?php if(isset($_POST['ShowData']))
                        {
                    ?>
                            <tr height='30'><td colspan='12' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
                    <?php
                        }?>
                    <thead style="left: -1px; top: 0;
                        position: absolute;">    
                    <tr>
                        <th style="width:4%;text-align:center;border:1px solid black;"><input type="checkbox" id="chk_all_bank_amount_match" onClick="SelectAll(this);"></th>
                        <th style="width:4%;text-align:center;border:1px solid black;">Sr No.</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Cheque Date</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Ref.</th>
                   		<th style="width:8%;text-align:center;border:1px solid black;">Voucher Type</th>                        
                        <th style="width:8%;text-align:center;border:1px solid black;">Withdrawals</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Deposits</th>
                        <th colspan="2" style="width:8%;text-align:center;border:1px solid black;">Match Entries</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Bank Statement Desc</th>   
<!--                        <th style="width:8%;text-align:center;border:1px solid black;">Status</th>  -->   
                    </tr>
                    </thead>
                    
                    <!--<tr> <td colspan="10"><br/> </td></tr>-->
                    <tbody>
                    
                        
                        <?php 
                        
						for($i = 0; $i < count($AMOUNT_MATCH_Data); $i++)
                        { 
							//var_dump($AMOUNT_MATCH_Data[$i]);
							$length = count($AMOUNT_MATCH_Data[$i][AMOUNT_MATCH]);
						
						?>
                        
                        <tr>
                            <td  style="width:8%;text-align:center;"><input type="checkbox" value="1" id="chk_bank_amount_match_<?php echo $i; ?>" onClick="selectradiobutton(this,<?php echo $i?>)"></td>
                            <td  style="width:8%;text-align:center;"><?php echo $i+1; ?></td>
                            <td  style="width:8%;text-align:center;"><?php echo getDisplayFormatDate($AMOUNT_MATCH_Data[$i]['Date']);?></td>
                            <td  style="width:8%;text-align:center;"><?php echo $AMOUNT_MATCH_Data[$i]['ChequeNumber'];?></td>
                            <td style="width:8%;text-align:center;"><?php echo $import_bank_statement->getVoucherName($AMOUNT_MATCH_Data[$i]['VoucherType']);?></td>
                            <td  style="width:8%;text-align:center;"><?php echo number_format($AMOUNT_MATCH_Data[$i]['Debit'],2);?></td>
                            <td  style="width:8%;text-align:center;"><?php echo number_format($AMOUNT_MATCH_Data[$i]['Credit'],2);?></td>
                            <td  style="width:8%;text-align:center;"><?php echo $AMOUNT_MATCH_Data[$i]['Bank_Description'];?></td>
                            <td  style="width:8%;text-align:center;">
                            <table>
                            	<?php for($j = 0; $j < $length; $j++){ 
                                if($j > 0)
                                { ?>
                                	<tr></tr>
                               	<?php } ?>
                                <tr>
                                <td><input type="radio"name = "amount_match_radio_<?php echo $i?>" id="amount_match_radio_<?php echo $i?>_<?php echo $j;?>" value="<?php echo $j;?>"></td>
                                <td><?php echo $AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['LedgerName']."  (".getDisplayFormatDate($AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['Date']);
                                    

                                    if(!empty($AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['ChequeNumber']))
									{
										echo " #Chk -".$AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['ChequeNumber'];
                                    }
                                    if(!empty($AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['comments']))
									{
										echo " #Comment :".$AMOUNT_MATCH_Data[$i][AMOUNT_MATCH][$j]['comments'];
									}
									?></td>
                                </tr>
                                <?php }?>
                            </table>
                            </td>
<!--                            <td style="width:8%;text-align:center;word-break: break-word;"><?php //echo $AMOUNT_MATCH_Data[$i]['error_msg'];?></td>-->
                        </tr>	
                        <?php }?>
                    </tbody>
                    </table>
                    </div>
                    </div>
                    </form>
                    </div>
                    </center>
            
            <div class="clear m-b"></div>
            </div>
            <div class="tab-pane fade" id="fwv-3">
                 <center>
                    <br>
                    <div class="panel panel-info" id="panel" style="display:block">
                        <div class="panel-heading" id="pageheader">Present in Bank but not in W2S</div>
                         <form name="bank_reconciliation" id="bank_reconciliation" method="post">
                    <br />
                    <center><div class="btn btn-primary" style="color:white"><i class="fa fa-plus" style="color:white;margin-top: 3px;margin-right: 6px;"></i><a target="_blank" name="auto_reconcile_present_in_bank" id="auto_reconcile_present_in_bank" onClick="ReconcileSubmit(<?php echo PRESENT_IN_BANK;?>)" style="text-decoration: none;color: white;">Add transaction to W2S</a></div></center>
             <br/>
                    <!-- <table width="90%"> -->
                    <div class="scrollableContainer">
                     <div class="scrollingArea">
                    <table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped " id="example">
                    <?php if(isset($_POST['ShowData']))
                        {
                    ?>
                            <tr height='30'><td colspan='12' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
                    <?php
                        }?>
                    <thead style="left: -1px; top: 0;
                        position: absolute;">    
                    <tr>
                        <th style="width:4%;text-align:center;border:1px solid black;"><input type="checkbox" id="chk_all_present_in_bank" onClick="SelectAll(this);"></th>
                        <th style="width:4%;text-align:center;border:1px solid black;">Sr No.</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Cheque Date</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Ref.</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Withdrawals</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Deposits</th>  
                        <th style="width:8%;text-align:center;border:1px solid black;">Bank Statement Desc</th>   
                        <th style="width:8%;text-align:center;border:1px solid black;">Status</th>     
                    </tr>
                    </thead>
                    
                    <!--<tr> <td colspan="10"><br/> </td></tr>-->
                    <tbody>
                    
                        
                        <?php 
                        
						for($i = 0; $i < count($PRESENT_IN_BANK_Data); $i++)
                        { ?>
                        
                        <tr>
                            <td style="width:8%;text-align:center;"><input type="checkbox" value="1" id="chk_present_in_bank_<?php echo $i; ?>"></td>
                            <td style="width:8%;text-align:center;"><?php echo $i+1; ?></td>
                            <td style="width:8%;text-align:center;"><?php echo getDisplayFormatDate($PRESENT_IN_BANK_Data[$i]['Date']);?></td>
                            <td style="width:8%;text-align:center;"><?php echo $PRESENT_IN_BANK_Data[$i]['ChequeNumber'];?></td>
                            <td style="width:8%;text-align:center;"><?php echo number_format($PRESENT_IN_BANK_Data[$i]['Debit'],2);?></td>
                            <td style="width:8%;text-align:center;"><?php echo number_format($PRESENT_IN_BANK_Data[$i]['Credit'],2);?></td>
                            <td style="width:8%;text-align:center;"><?php echo $PRESENT_IN_BANK_Data[$i]['Bank_Description'];?></td>
                            <td style="width:8%;text-align:center;word-break: break-word;"><?php echo $PRESENT_IN_BANK_Data[$i]['error_msg'];?></td>
                        </tr>	
                        <?php }?>
                    </tbody>
                    </table>
                    </div>
                    </div>
                    </form>
                    </div>
                    </center>
            
            <div class="clear m-b"></div>
            </div>
            <div class="tab-pane fade" id="fwv-4">
                 <center>
                    <br>
                    <div class="panel panel-info" id="panel" style="display:block">
                        <div class="panel-heading" id="pageheader">Present in w2s but not in Bank Statement</div>
                         <form name="bank_reconciliation" id="bank_reconciliation" method="post">
                    <br />
                     <center><div class="btn btn-primary" style="color:white"><i class="fa fa-trash" style="color:white;margin-top: 3px;margin-right: 6px;"></i><a target="_blank" name="auto_reconcile" id="auto_reconcile" style="text-decoration: none;color: white;" onClick="ReconcileSubmit(<?php echo PRESENT_IN_W2S;?>)">Delete transaction from W2S</a></div></center>
             <br/>
                    <!-- <table width="90%"> -->
                    <div class="scrollableContainer">
                     <div class="scrollingArea">
                    <table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped" id="example">
                    <?php if(isset($_POST['ShowData']))
                        {
                    ?>
                            <tr height='30'><td colspan='12' align='center'><font color='red' size='-1'><b id='error' style='display:block;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
                    <?php
                        }?>
                    <thead style="left: -1px; top: 0;
                        position: absolute;">    
                    <tr>
                        <th style="width:4%;text-align:center;border:1px solid black;"><input type="checkbox" id="chk_all_present_in_w2s" onClick="SelectAll(this);"></th>
                        <th style="width:4%;text-align:center;border:1px solid black;">Sr No.</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Cheque Date</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Ref.</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Ledger Name</th>
    	                <th style="width:8%;text-align:center;border:1px solid black;">Voucher Name</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Withdrawals</th>
                        <th style="width:8%;text-align:center;border:1px solid black;">Deposits</th>  
                        <th style="width:8%;text-align:center;border:1px solid black;">W2s Desc</th>       
                    </tr>
                    </thead>
                    
                    <!--<tr> <td colspan="10"><br/> </td></tr>-->
                    <tbody>
                    
                        
                        <?php 
                        
                        for($i = 0; $i < count($PRESENT_IN_W2S_Data); $i++)
                        { ?>
                        
                        <tr>
                            <td style="width:8%;text-align:center;"><input type="checkbox" value="1" id="chk_present_in_w2s_<?php echo $i; ?>"></td>
                            <td style="width:8%;text-align:center;"><?php echo $i+1; ?></td>
                            <td style="width:8%;text-align:center;"><?php echo getDisplayFormatDate($PRESENT_IN_W2S_Data[$i]['Date']);?></td>
                            <td style="width:8%;text-align:center;"><?php echo $PRESENT_IN_W2S_Data[$i]['ChequeNumber'];?></td>
                            <td style="width:8%;text-align:center;"><?php echo $PRESENT_IN_W2S_Data[$i]['LedgerName'];?></td>                            
                        	<td style="width:8%;text-align:center;"><?php echo $import_bank_statement->getVoucherName($PRESENT_IN_W2S_Data[$i]['VoucherType']);?></td>                           
                            <td style="width:8%;text-align:center;"><?php echo number_format($PRESENT_IN_W2S_Data[$i]['Debit'],2);?></td>
                            <td style="width:8%;text-align:center;"><?php echo number_format($PRESENT_IN_W2S_Data[$i]['Credit'],2);?></td>
                            <td style="width:8%;text-align:center;"><?php echo $PRESENT_IN_W2S_Data[$i]['W2s_Comments'];?></td>

                        </tr>	
                        <?php }?>
                    </tbody>
                    </table>
                    </div>
                    </div>
                    </form>
                    </div>
                    </center>
         
                <div class="clear m-b "></div>
            </div>
        </div>
        </div>  
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content" style="border-radius: 15px;">
        <div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px;border-top-left-radius: 10px;border-top-right-radius: 10px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="margin: 0px;padding: 13px">Match Statement for Reconcilation</h4>
        </div>
        <div class="modal-body">
           <div class="formSubmitResponse" align="left" style="font-size: 15px;font-weight: 400;"></div>
        </div>
        <div class="modal-footer">
        	<a href="bank_statement.php?LedgerID=<?php echo $_REQUEST['LedgerID'];?>" target="_blank" onClick="closedialoge();"><span class="btn btn-success">View Bank Statement</span></a>
			<a data-dismiss="modal"  onClick='window.location.reload();'><span class="btn btn-primary">Done</span></a>
        </div>
      </div>
      
    </div>
  </div>
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>   -->
</div>                                   
		<input type="hidden" id="match_count" name="match_count" value="<?php echo count($MatchData);?>">
        <input type="hidden" id="bank_count" name="bank_count" value="<?php echo count($PRESENT_IN_BANK_Data);?>">
        <input type="hidden" id="w2s_count" name="w2s_count" value="<?php echo count($PRESENT_IN_W2S_Data);?>">	
        <input type="hidden" id="amount_match_count" name="amount_match_count" value="<?php echo count($AMOUNT_MATCH_Data);?>">	
        <input type="hidden" name="bankid" id="bankid" value="<?php echo $_REQUEST['LedgerID'];?>"/>					


<?php include_once "includes/foot.php"; ?>

<?php 
if(isset($_POST['from_date']) && isset($_POST['to_date']) )
{?>
	<script>
	document.getElementById('from_date').value = "<?php echo $_POST['from_date']; ?>";
	document.getElementById('to_date').value = "<?php echo $_POST['to_date']; ?>";
	</script>
<?php }
?>

