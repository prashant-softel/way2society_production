<?php 
error_reporting(1);
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/member_due_regular.class.php";
include_once "classes/dbconst.class.php";
include_once("classes/list_member.class.php");

$obj_list_member = new list_member($dbConn);
if(isset($_GET["unset"]))
{
	$_POST['wing'] = "";
	$_POST['to_date'] = $_GET['to_date'];
	$_POST['from_date'] = $_GET['from_date'];
	$_POST['BillType'] = $_GET['BillType'];
	
}

$obj_memberDuesRegular= new memberDuesRegular($dbConn,$dbConnRoot);
if($_SESSION['society_id'] == 202)
{
	$templateData= $obj_memberDuesRegular->getNoticeTemplates(56);
}
else
{
	$templateData= $obj_memberDuesRegular->getNoticeTemplates(27);
}
//print_r($templateData);

if($_POST['to_date'] == '' )
{	
?>
<script>
	window.location.href="common_period.php?memberdues&temp";
</script>			
<?php }
else
{	
	$show_dues_details=$obj_memberDuesRegular->getMemberDuesRegular($_POST['dues_range'], $_POST['wing'],$_POST['to_date'],$_POST['BillType']);
	if($show_dues_details == '')
	{
	?>	
	<script>
		window.location.href="common_period.php?memberdues&fail";
	</script>		
	<?php	
	}
}
if($_POST['BillType'] == 0)
{
	$bHeadTitle= 'Maintenance';
}
if($_POST['BillType'] == 1)
{
	$bHeadTitle= 'Supplementry';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<title>Dues From Members-Regular Bill</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 1px solid #cccccc;
		text-align:left;
	} 	
</style>
 <script type="text/javascript" src="js/memberdues_regularreport_17052018.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
<script>
function SelectAll(chkBox)
{
	if(chkBox.checked)
	{
		$('.unitCheckbox').prop('checked', true);
		document.getElementById("btn_notice").style.backgroundColor='#337ab7';
		document.getElementById("btn_notice").disabled=false;
		document.getElementById("btn_notice1").style.backgroundColor='#337ab7';
		document.getElementById("btn_notice1").disabled=false;
		//document.getElementById("showbutton2").style.display='table-row';
		//document.getElementById("showbutton4").style.display='table-row';
		document.getElementById("showbutton1").style.display='table-row';
		document.getElementById("showbutton3").style.display='table-row';
	}
	else
	{
		$('.unitCheckbox').prop('checked', false);
		document.getElementById("btn_notice").style.backgroundColor='#337ab77a';
		document.getElementById("btn_notice").disabled=true;
		document.getElementById("btn_notice1").style.backgroundColor='#337ab77a';
		document.getElementById("btn_notice1").disabled=true;
		document.getElementById("showbutton1").style.display='block';
		document.getElementById("showbutton3").style.display='block';
	}
}

function showDues(isChecked)
{

	if(isChecked == true) 
	{
		$(".dueShowClass").css('visibility' ,'visible');
		//$(".dueShowClass").css('width', '85%');
		//$(".dueShowClass").css('border-top', 'none');
		//$(".dueShowClass").css('float', 'left');
		//border-bottom: none;
   // border-top: none;
		
		$('#dueDaysChecked').val(1);
	}
	else
	{
		$(".dueShowClass").css('visibility' ,'hidden');
		$('#dueDaysChecked').val(0);
	}
}
function SelectUnit(UnitID, values)
{
	var checkedCount = 0;
	$('.unitCheckbox:checkbox:checked').each(function() {
        checkedCount++;
    });
	
	
	if(checkedCount > 0)
	{
		document.getElementById("btn_notice").style.backgroundColor='#337ab7';
		document.getElementById("btn_notice").disabled=false;
		document.getElementById("btn_notice1").style.backgroundColor='#337ab7';
		document.getElementById("btn_notice1").disabled=false;
		document.getElementById("showbutton1").style.display='table-row';
		document.getElementById("showbutton3").style.display='table-row';	
		
	}
	else
	{
		document.getElementById("btn_notice").style.backgroundColor='#337ab77a';
		document.getElementById("btn_notice").disabled=true;
		document.getElementById("btn_notice1").style.backgroundColor='#337ab77a';
		document.getElementById("btn_notice1").disabled=true;
		document.getElementById("showbutton1").style.display='block';
		document.getElementById("showbutton3").style.display='block';
	}
}

</script>
<style>
.table-striped>tbody>tr:nth-of-type(odd)
    {
        background: #FFF !important;
    }

    .table-striped>tbody>tr:nth-of-type(even)
    {
        background: #F5F3F3 !important;
    }
</style>
</head>

<body>
<div id="mainDiv">
<?php include_once("report_template.php"); // get the contents, and echo it out.?>
<div style="border: 1px solid #cccccc;">
 <input type="hidden" id= 'temp_sub' value="<?php echo $templateData[0]['template_subject']; ?>">
 <input type="hidden" id= 'temp_data' value="<?php echo $templateData[0]['template_data']; ?>">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Dues From Members-Regular Bill <?php echo  "[".$bHeadTitle."]"?></div>
           <!-- <div style="font-weight; font-size:16px;">As on Date:<?php //echo date("d.m.Y");?></div> -->
           	<div style="font-weight; font-size:16px;">UpTo <?php echo '   '.getDisplayFormatDate($_POST['to_date']).'      ';?></div>            
        </div>
         <br>
         <div class="no-print" style="float:left;width:49%;">
              <form method="post"  action="memberdues_regularreport.php?&sid=<?php echo $_SESSION['society_id'];?>" class="no-print" id="selectform">
                <label id="Principalamt">Principal Amount(>than):</label><input type="text" id="dues_range" name="dues_range"  style="width:80px" value="<?php echo $_POST['dues_range'];?>" >
                
                <label id="winglabel">Wing:</label><input type="text" id="wing" name="wing"  style="width:80px" value="<?php echo $_POST['wing'];?>" >    
                <label id="overduelabel">Overdue days:</label><input type="text" id="Overdue" name="Overdue"  style="width:80px" value="<?php echo $_POST['Overdue']?>" >            
                <input type="hidden" id="to_date" name="to_date" value="<?php echo $_POST['to_date']; ?>"  />  
                <input type="hidden" id="dueDaysChecked" name = "dueDaysChecked" value="<?php echo $_POST['dueDaysChecked']; ?>" />
                <input type="submit"  name="submit" id="submit" value="Go">&nbsp;&nbsp;&nbsp;  
                <!-- <input type="button" name="clearform" onClick="clearForm()" value="Reset"> -->
                
			</form>	
        </div>
        <div style="float:left;">
				<form action="memberdues_regularreport.php?&sid=<?php echo $_SESSION['society_id']; ?>" method="post">
	                <input type="hidden" id="to_date" name="to_date" value="<?php echo $_POST['to_date']; ?>" />
	            	<input type="submit" name="clearform" value="Reset">
	            </form>
            </div>
       
         <div style="float:right;padding-right: 170px;" class="no-print">
             <label>Show number of days since last payment :</label>&nbsp;&nbsp;<span style="margin-top: 1px;
    float: right;"><input type="checkbox" id="checkdues"  checked="checked" onClick="showDues(this.checked);" <?php if(isset($_POST['dueDaysChecked']) && $_POST['dueDaysChecked'] == 1) { echo 'checked'; } ?> /></span>
        </div>						<!--Over Dues  days-->
             
	<div class="no-print" style=" padding-bottom: 1%;padding-top: 1%;">	
    <br>
  <table width="100%" cellpadding="0" border="0" style="border:0px;" class="no-print">
  
  <tr align="center">
  <td colspan="8" id="showbutton1" style="text-align: center;border:0px; display:block;"><input type="button" id="btn_notice" onClick="sendNotice();" value="Send overdue notice to selected units" style="color: #fff;background-color: #337ab77a;border-color: #2e6da4;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;cursor: pointer;border: 1px solid transparent;border-radius: 4px;" disabled/></td>
  
 <!-- <td colspan="8" id="showbutton2" style="text-align: center;border:0px; display:block;"><input type="button" id="btn_all_notice" onClick="sendNotice();" value="Send overdue notice to all selected units" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;cursor: pointer;border: 1px solid transparent;border-radius: 4px;"/></td>-->
  </tr>
  </table>
  </div>


        <table  style="width:100%;font-size:14px; " class="table table-striped">
        	<tr>
            
            	<th class="no-print" style="text-align:center;  width:2%;border: 1px solid #cccccc;"><input type="checkbox" id="chk_all" onClick="SelectAll(this);" style="visibility:block"/></th>
                <th style="text-align:center;  width:5%;border: 1px solid #cccccc;">Sr. No.</th>
                <th style="text-align:center;  width:5%;border: 1px solid #cccccc;" >Wing</th>
                <th style="text-align:center;  width:10%;border: 1px solid #cccccc;">Unit No.</th>
                <th style="text-align:center;  border: 1px solid #cccccc;" colspan="3">Member Name</th>
                <th style="text-align:center;  width:10%;border: 1px solid #cccccc;"  colspan="3">Principal  (Rs.)</th>
                <th style="text-align:center;  width:10%;border: 1px solid #cccccc;"  colspan="3">Interest  (Rs.)</th>
                <th style="text-align:center;  width:10%;border: 1px solid #cccccc;"  colspan="3">Total Amount  (Rs.)</th>
                <th class="dueShowClass" colspan="3"  style=" width:10%;text-align:center;border: 1px solid #cccccc;visibility: visible;">Days Since Last Payment</th>				                
          	</tr>
            <?php				
				$count=0;
				$principaltotal=0;
				$interesttotal=0;
				$TotalAmount=0;
				$str_unit_ary = '';	
				
				$UnitArray =$obj_list_member->getAllUnits();
				$EncodeUnitArray;
				$EncodeUrl;
				if(sizeof($UnitArray) > 0)
				{
					$EncodeUnitArray = json_encode($UnitArray);
					$EncodeUrl = urlencode($EncodeUnitArray);
				}		
				if($show_dues_details <>"")
				{
							
					$overdueDays = (isset($_POST['Overdue']) && !empty($_POST['Overdue'])) ? (int)$_POST['Overdue'] : '';
					foreach($show_dues_details as $k => $v)
					{	
					 if(sizeof($UnitArray) > 0)
						{
							$Url = "member_ledger_report.php?&uid=".$show_dues_details[$k]['UnitID']."&Cluster=".$EncodeUrl;
						}
						else
						{
							$Url = "member_ledger_report.php?&uid=".$show_dues_details[$k]['UnitID'];
						}
					
						$payment_details=$obj_memberDuesRegular->getAllPaymentDetails($show_dues_details[$k]['UnitID'],$show_dues_details[$k]['BillDate'],$show_dues_details[$k]['Principal']+$show_dues_details[$k]['GSTTax'],$show_dues_details[$k]['Interest'],$_POST['to_date'],$_POST['BillType']);		
						
						
						$Days = $payment_details[0]['DiffDate'];
						if(is_int($overdueDays))
						{
							if((int)$Days < $overdueDays)
							{
								continue;
							}
						}
															
						$wing = $show_dues_details[$k]['Wing'];															
										 
						if(isset($_POST['dues_range']) && $_POST['dues_range'] <> '' && $_POST['dues_range'] > 0)
						{									
							if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Go' && $payment_details[0]['principal'] > $_POST['dues_range'])												
							{
								$principaltotal=$principaltotal + $payment_details[0]['principal'] ;
								$interesttotal=$interesttotal + $payment_details[0]['interest'] ;
								$total=$payment_details[0]['principal'] + $payment_details[0]['interest'];		
								if($total > 0) //not show in amount 0 is details 
								{
									if($total > 0)
									{
										$Days=$payment_details[0]['DiffDate'];
									}
									else
									{
										$Days=0;
									}
						
								if(is_int($overdueDays))
								{
									if((int)$Days < $overdueDays)
									{
										continue;
									}
								}		
								$TotalAmount=$TotalAmount + $total;	 
								
								$count++;
								$str_unit_ary .= $show_dues_details[$k]['UnitID'] . '#';
						
								echo "<tr><td class='no-print'><input type='checkbox' class='unitCheckbox' value='1' id='chk_".$show_dues_details[$k]['UnitID']."' onClick='SelectUnit(".$show_dues_details[$k]['UnitID'].",this);'></td><td style='border: 1px solid #cccccc;text-align:center;'>".$count."</td><td style='border: 1px solid #cccccc;border-left:none;text-align:center;'>".$wing."</td><td  style='border: 1px solid #cccccc;text-align:center;'>".$show_dues_details[$k]['UnitNo']."</td><td colspan=3 style='border: 1px solid #cccccc;text-align-left;'><a href='view_member_profile.php?scm&id=".$show_dues_details[$k]['member_id']."&tik_id=".time()."&m&view' target='_blank' style='text-decoration: none;'>".$show_dues_details[$k]['owner_name']."</a></td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['principal'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['interest'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;' ><a href=".$Url." target='_blank' style='text-decoration: none;' id='totalAmounts_".$show_dues_details[$k]['UnitID']."'>".number_format($total,2)."</a></td><td id='duesDays_".$show_dues_details[$k]['UnitID']."' colspan=3 class='dueShowClass' style='border: 1px solid #cccccc;text-align:right;visibility: visible '>".$Days."</td></tr>";
								}
							}	
						}
						
						else if(isset($_POST['dues_range']) && $_POST['dues_range'] <> '' && $_POST['dues_range'] <= 0)																						
							{		
								if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Go' && $payment_details[0]['principal'] <= $_POST['dues_range'])												
								{						
							 
									$principaltotal=$principaltotal + $payment_details[0]['principal'] ;
									$interesttotal=$interesttotal + $payment_details[0]['interest'] ;
									$total=$payment_details[0]['principal'] + $payment_details[0]['interest'];
								if($total > 0)  // show in amount 0 is details 
								{
									if($total > 0)
									{
										$Days=$payment_details[0]['DiffDate'];
									}
									else
									{
										$Days=0;
									}
								
									if(is_int($overdueDays))
									{
									//echo 'Overdue Set';
										if((int)$Days < $overdueDays)
										{
											continue;
										}
									}
									$TotalAmount=$TotalAmount + $total;
									$count++;
									$str_unit_ary .= $show_dues_details[$k]['UnitID'] . '#';														 
								
								//$str_unit_ary .= $res[$k]['unit_id'] . '#';
								echo "<tr><td class='no-print'><input type='checkbox' class='unitCheckbox' value='1' id='chk_".$show_dues_details[$k]['UnitID']."' onClick='SelectUnit(".$show_dues_details[$k]['UnitID'].",this);' ></td><td style='border: 1px solid #cccccc;text-align:center;'>".$count."</td><td style='border: 1px solid #cccccc;border-left:none;text-align:center;'>".$wing."</td><td  style='border: 1px solid #cccccc;text-align:center;'>".$show_dues_details[$k]['UnitNo']."</td><td colspan=3 style='border: 1px solid #cccccc;text-align-left;'><a href='view_member_profile.php?scm&id=".$show_dues_details[$k]['member_id']."&tik_id=".time()."&m&view' target='_blank' style='text-decoration: none;' >".$show_dues_details[$k]['owner_name']."</a></td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['principal'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['interest'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;' ><a href=".$Url." target='_blank' style='text-decoration: none;' id='totalAmounts_".$show_dues_details[$k]['UnitID']."' >".number_format($total,2)."</a></td><td id='duesDays_".$show_dues_details[$k]['UnitID']."'  class='dueShowClass' colspan=3 style='border: 1px solid #cccccc;text-align:right;visibility: visible'>".$Days."</td></tr>";
							 }	
							}
						}
						else
						{
							$principaltotal=$principaltotal + $payment_details[0]['principal'] ;
							$interesttotal=$interesttotal + $payment_details[0]['interest'] ;
							$total=$payment_details[0]['principal'] + $payment_details[0]['interest'];
							if($total > 0)  // not show in amount 0 is details 
							{	
								if($total > 0)
								{
									$Days=$payment_details[0]['DiffDate'];
								}
								else
								{
									$Days=0;
								}
								if(is_int($overdueDays))
								{
								//echo 'Overdue Set';
								if((int)$Days < $overdueDays)
								{
									continue;
								}
							}
							$TotalAmount=$TotalAmount + $total;
							$count++;		
								
							$str_unit_ary .= $show_dues_details[$k]['UnitID'] . '#';
							echo "<tr><td class='no-print'><input type='checkbox' class='unitCheckbox' value='1' id='chk_".$show_dues_details[$k]['UnitID']."' onClick='SelectUnit(".$show_dues_details[$k]['UnitID'].",this);' ></td><td style='border: 1px solid #cccccc;text-align:center;'>".$count."</td><td style='border: 1px solid #cccccc;border-left:none;text-align:center;'>".$wing."</td><td  style='border: 1px solid #cccccc;text-align:center;'>".$show_dues_details[$k]['UnitNo']."</td><td colspan=3 style='border: 1px solid #cccccc;text-align-left;'><a href='view_member_profile.php?scm&id=".$show_dues_details[$k]['member_id']."&tik_id=".time()."&m&view' target='_blank' style='text-decoration: none;' >".$show_dues_details[$k]['owner_name']."</a></td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['principal'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;'>".number_format($payment_details[0]['interest'],2)."</td><td colspan=3 style='border: 1px solid #cccccc;text-align:right;' ><a href=".$Url." target='_blank' style='text-decoration: none;' id='totalAmounts_".$show_dues_details[$k]['UnitID']."' >".number_format($total,2)."</a></td><td id='duesDays_".$show_dues_details[$k]['UnitID']."'  class='dueShowClass' colspan=3 style='border: 1px solid #cccccc;text-align:right;visibility: visible'>".$Days."</td></tr>";
							} 	
						}
					}
					
							
					echo "<tr><td class='no-print'></td><td colspan='6' style='border: 1px solid #cccccc;text-align:center;background-color:#F5F3F3;'>Total (Rs.)</td><td  colspan='3' style='border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3;'>".number_format($principaltotal,2)."</td><td  colspan='3' style='border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3;'>".number_format($interesttotal,2)."</td><td colspan='3' style='border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3;'>".number_format($TotalAmount,2)."</td><td  colspan='3' style='border: 1px solid #cccccc;text-align:right;background-color:#F5F3F3; width:10%;'></td></tr>";																							
		      	}
              	?>			                
        </table>
       	
        <input type="hidden" id="unit_ary" value="<?php echo $str_unit_ary?>" />
		<input type="hidden" id='sid' value="<?php echo $_REQUEST['sid'];?>">
       
  </div>
  </div>
 <div class="no-print" style=" padding-bottom: 1%;padding-top: 1%;">	
    <br>
  <table width="100%" cellpadding="0" border="0" style="border:0px;">
  
  <tr align="center">
  <td colspan="8" id="showbutton3" style="text-align: center;border:0px; display:block;"><input type="button" id="btn_notice1" onClick="sendNotice();" value="Send overdue notice to selected units" style="color: #fff;background-color: #337ab77a;border-color: #2e6da4;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;cursor: pointer;border: 1px solid transparent;border-radius: 4px;" disabled/></td>
  
  <!--<td colspan="8" id="showbutton4" style="text-align: center;border:0px; display:none;"><input type="button" id="btn_all_notice" onClick="sendNotice();" value="Send overdue notice to all selected units" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;cursor: pointer;border: 1px solid transparent;border-radius: 4px;"/></td>-->
  </tr>
  </table>
  </div>
  
 
  <div id="openDialogYesNo" class="modalDialog" style="margin-top: -50px;" >
	<div style="width: 70%; text-align:left; ">
		<div id="message_yesno"  >
		</div>
	</div>
</div>


<div id="openDialogOk" class="modalDialog" >
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>
  </body>

</html>

<script>
<?php 
if(isset($_POST['dueDaysChecked']) && $_POST['dueDaysChecked'] == 1)
{
	?>
		showDues(true);
	<?php
}
?>
</script>