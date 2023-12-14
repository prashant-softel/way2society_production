<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Report Period</title>
</head>




<?php 
include_once("includes/head_s.php");
//include_once "ses_set_as.php";
include_once("classes/home_s.class.php"); 
include_once("classes/utility.class.php");
include_once "classes/dbconst.class.php";
?>
<?php
 // Turn off all error reporting
        error_reporting(0);


$obj_AdminPanel = new CAdminPanel($m_dbConn);
$obj_Utility = new utility($m_dbConn);
/*if($_REQUEST["bankid"] == "")
{
	echo "<script>alert('Error ! Please pass LedgerID to generate Report');</script>";
	exit;
}*/
//echo "duesadvance:".isset($_REQUEST["duesadvance"]);
$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance();

if($_SESSION['default_year'] == 0)
{?>
	<script> alert("Please Set Default Value For Current Year.");window.location.href="defaults.php";</script>
<?php	
}
else{
$CurrentYearBeginingDate = $obj_Utility->getCurrentYearBeginingDate($_SESSION['default_year']);
$CurrentYearBeginingDate = getDisplayFormatDate($CurrentYearBeginingDate);
$CurrentYearEndingDate = getDisplayFormatDate($_SESSION['default_year_end_date']);
}
//echo "CurrentYearBeginingDate:".$CurrentYearBeginingDate;
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Period</title>
</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
			changeMonth:true,
            changeYear:true,
          	maxDate:-0,
			yearRange:"-7:+7",
        })});
    </script>

</head>

<body>

<div id="middle">
<div class="panel panel-info" style="height:350px;">
    <div class="panel-heading" id="pageheader">
    <?php if(isset($_GET["income"]))
			{
				echo "Income Register Report";
            }
			elseif(isset($_GET["expense"]))
			{
           		echo "Expense Register Report";
            }
			elseif(isset($_GET["payment"]))
			{
            	echo "Payment Register Report";
            }
			elseif(isset($_GET["receipt"]))
			{
            	echo "Receipt Register Report";
			}
            elseif(isset($_GET["cashflow"]))
			{
           		echo "Cashflow Report";
            } 
			elseif(isset($_GET["bankreco"]))
			{
				echo "Bank Reconciliation Report";	
			}
			elseif(isset($_GET["duesadvance"]))
			{
				echo "Dues - Advance From Member Report";
			}
			elseif(isset($_GET["memberdues"]))
			{
				echo "Dues From Member Regular Report";
			}
			elseif(isset($_GET["sinkingfund"]))
			{
				echo "Sinking Fund Report";
			}
			elseif(isset($_GET["investmentregister"]))
			{
				echo "Investment Register Report";
			}
			
	?>
   
    
    </div>
<center>

<form name="report_main" id="report_main" target="_blank" method="post" action="cash_flow_report.php">
<table>
<tr><div id="show" style="font-weight:bold;color:#FF0000" align="center"></div></tr>

<tr><h4><font  style="width:50px" color="#003399">Please select the period</font></h4></tr>
<?php
if(isset($_GET['temp']))
     {
		 ?>
         
	<script>
    //document.getElementById('show').innerHTML = "Please Select Period..";
    //$('#show').fadeOut(6000);
    </script>	 
		
<?php	}
	
    if(isset($_GET["cashflow"]) || isset($_GET["payment"]) || isset($_GET["bankreco"]))
			{
            ?> 
 <tr>
        <td> Please Select Bank : </td>
        <td>
        <select name="ledgerid" id="ledgerid">
    <?php 
	if(isset($_GET["payment"]))
			{?>
            <option value=""> All </option>
            <?php
			}
	foreach($arBankDetails as $arData=>$arvalue)
   {
	   $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
	   $arParentDetails = $obj_Utility->getParentOfLedger($arvalue["LedgerID"]);
	   $CategoryID = $arParentDetails['category'];
	   if(isset($_GET["cashflow"]))
	   {
	   if($CategoryID == CASH_ACCOUNT || $CategoryID ==BANK_ACCOUNT)
	   {
		?>    	
    	<option value="<?php echo $arvalue["LedgerID"]; ?>"> <?php echo $BankName; ?> </option>
    <?php }
	   }
	   else
	   {?>
       <option value="<?php echo $arvalue["LedgerID"]; ?>"> <?php echo $BankName; ?> </option>
       <?php
	   }
   }?>
   </select>
   </td>
   </tr> 
   <?php } 
   
   else if(isset($_GET["fail"]))
	{
		
		?>
         
	<script>
    document.getElementById('show').innerHTML = "Records Not Found For Selected Period...";
    $('#show').fadeOut(4000);
    </script>	
   <?php } 
   if(!isset($_GET["bankreco"]) || !isset($_GET["duesadvance"]))
			{
				//echo "abc";?>
             
<tr id="from_date1">        				
            <?php if(isset($_GET["bankreco"]))
			{  ?>  
            <td style="display:none;">From :</td>
            <td style="display:none;"><input type="text" name="from_date" id="from_date" size="10" readonly   value="<?php echo $CurrentYearBeginingDate?>" style="width:80px; background-color:#CCC;"/></td>
            <?php
			}
			else
			{  ?>
            <td >From :</td>
			<td><input type="text" name="from_date" id="from_date"   class="basics" size="10" readonly   value="<?php echo $CurrentYearBeginingDate?>" style="width:80px;"/></td>
            <?php
			}  ?>
</tr>
<?php }?>

<tr>
        	
			<td id="To">To :</td> 
           <td>
             <?php if(isset($_GET["duesadvance"]) || isset($_GET['memberdues']))
			{  ?>   
            <input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly   value=<?php echo date('d-m-Y'); ?>  style="width:80px;"/>
            </td>
            </tr>
            <tr>
            <td>Bill Type : </td>
            <td>
            <?php if(isset($_GET["duesadvance"]))
			{?>
            <select id="BillType" name="BillType" >
            <option value="<?=Combine_Bill?>" selected="selected">Combined</option>
            <option value="<?=Maintenance?>">Maintenance Bills</option>
			<option value="<?=Supplementry?>">Supplemenary Bills</option>
			<option value="<?=Invoice?>">Invoice Bills</option>
            
            </select>
            <?php }
			if(isset($_GET['memberdues']))
			{?>
			<select id="BillType" name="BillType" >
            <option value="<?=Maintenance?>">Maintenance Bills</option>
			<option value="<?=Supplementry?>">Supplemenary Bills</option>
			</select>
			<?php }?>
            </td>
            
            <?php
			}else
			{ 
				if((strtotime($CurrentYearBeginingDate) < strtotime(date('d-m-Y'))) && (strtotime(date('d-m-Y')) < strtotime($CurrentYearEndingDate)))
				{
					$to_date = date('d-m-Y');
				}
				else
				{
					$to_date = $CurrentYearEndingDate;
				}
			 ?>
            	<input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly   value=<?php echo $to_date ?>  style="width:80px;"/>
            <?php }?>
            </td>
            
</tr>
<?php if(isset($_GET["duesadvance"]))
{  ?>   
<tr>
<td style="padding-top: 3px;">Single Amount Column:</td> 
<td>
    <input type="checkbox" name="chkb1" id="chkb1"   value='1'  style="width:20px;"/>
</td>            
</tr>
 <?php
}?>
<tr><td>&nbsp;&nbsp;</td></tr>
<?php
if(isset($_GET['duesadvance']) || isset($_GET['memberdues']) )
     {
		 //echo "test";
		 ?>
       <script>
	     //alert("script tag");
       //document.getElementById('from_date1').style.display= 'none';
	   $('#from_date1').hide('fast');
	   document.getElementById('To').innerHTML="Upto:";
	     </script>
	<?php }?>
<tr>
			<td colspan="2" align="center">
            <?php if(isset($_GET["income"]))
			{
				?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='income_register_report.php'"   class="btn btn-primary"   style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
            }
			elseif(isset($_GET["expense"]))
			{
            ?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='expense_register_report.php'"  class="btn btn-primary"   style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
            }
			elseif(isset($_GET["payment"]))
			{
            ?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='payment_report.php'"   class="btn btn-primary"   style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
            }
			elseif(isset($_GET["receipt"]))
			{
            ?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='receipt_register.php'"   class="btn btn-primary"    style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
			<?php
			}
            elseif(isset($_GET["cashflow"]))
			{
            ?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='cash_flow_report.php'"    class="btn btn-primary"    style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
            } 
			elseif(isset($_GET["bankreco"]))
			{?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='BankReco.php'"   class="btn btn-primary"  style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php 
			}			
			elseif(isset($_GET["duesadvance"]))
			{
			?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='dues_advance_frm_member_report.php?&sid=<?php echo $_SESSION['society_id']; ?> '"   class="btn btn-primary"    style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
			}
			elseif(isset($_GET["memberdues"]))
			{
			?>
            <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='memberdues_regularreport.php?&sid=<?php echo $_SESSION['society_id']; ?> '"  class="btn btn-primary"    style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4; ">
            <?php
			}
			elseif(isset($_GET["sinkingfund"]))
			{
			?>
			   <?php
			  if($_SESSION['default_sinking_fund']!=0)
			  {
               ?>
              <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='Sinking_fund_report.php?sinkingfundid=<?php echo $_SESSION['default_sinking_fund']; ?> '"class="btn btn-primary" style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4;">
			  <br>

			  <?php
			  }
			  ?>

              <?php
			  if($_SESSION['default_sinking_fund']==0)
			  {
               ?>
              <td> <span style="color:#0000FF;font-size:20px;position: absolute ;left:22%;">Sinking Fund Register not selected on defaults page.Please select Sinking Fund Register.For that click <a href="defaults.php" style="font-size:20px;">here</a></span>
			  <td>

			   <?php
			  }
			  ?>
            <?php
			}
			elseif(isset($_GET["investmentregister"]))
			{
			?>
			<?php
			  if($_SESSION['default_investment_register']!=0)
			  {
               ?>
              <input type="submit" name="insert" id="insert" value="Generate Report" onClick="report_main.action='Investment_register_report.php?investmentregisterid=<?php echo $_SESSION['default_investment_register']; ?> '"class="btn btn-primary" style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4;">
			  <?php
			  }
			  ?>
                <?php
			  if($_SESSION['default_investment_register']==0)
			  {
               ?>
			   
              <span style="color:#0000FF;font-size:20px;position: absolute ;left:20%;display:block;">Investment Register not selected on defaults page.Please select Investment Register value.For that click  <a href="defaults.php" style="font-size:20px; color:#00008B;font-weight: bold;">here</a></span>
		    
			   <?php
			  }
			  ?>
            <?php
			}
			?>
			
			
            </td>                            			
			
		</tr>
</table>
</form>

</center></div></div>

<?php if(isset($_REQUEST['lID'])) { 
?>
<script>
	document.getElementById('ledgerid').value = <?php echo $_REQUEST['lID']; ?>;
</script>
<?php } ?>

<?php include_once "includes/foot.php"; ?>