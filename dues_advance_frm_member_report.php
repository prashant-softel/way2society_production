
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>

<title>W2S - Dues Advance From Member Report</title>
</head>


<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";


$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/report.class.php";
$obj_view_reports = new report($dbConn);
include_once "classes/dbconst.class.php";

$show_society_name=$obj_view_reports->show_society_name($_REQUEST["sid"]);
//echo "fromdate:".$_POST['from_date'];
//print_r($_SESSION);
if($_POST['from_date'] == '' && $_POST['to_date'] == '' )
{	
?>
<script>
	window.location.href="common_period.php?duesadvance&temp";	
</script>			
<?php 
}
else
{	
	$show_mem_due_details=$obj_view_reports->show_mem_due_details($_POST['from_date'],$_POST['to_date'], $_POST['wing'], $_POST['BillType']);
	if($show_mem_due_details == '')
	{
	?>	
	<script>
		window.location.href="common_period.php?duesadvance&fail";
	</script>		
	<?php	
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php 
if($_POST['BillType'] == Maintenance)
{ 
	$Heading = " - Maintenance Bills";
}
else if($_POST['BillType'] == Supplementry)
{
	$Heading = " - Supplementary Bills";
}
else if($_POST['BillType'] == Invoice)
{
	$Heading = " - Invoice Bills";
}
else
{
	$Heading = "";
}
?>
<title>Dues-Advance From Members <?php echo $Heading;?></title>
<style>
	/*table {
    	border-collapse: collapse;
		border:1px solid #cccccc; 
		
	}*/
	th, td {
		border-collapse: collapse;
		border:1px solid #cccccc; 
		text-align:left;
	}	
	tr:hover {background-color: #f5f5f5}
	/*td{border:1px dotted black !important;}*/
</style>

<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>

</head>

<body>
<center>
<div id="mainDiv" style="width:80%;">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
<center>
<div  id="originalDiv" style="border: 1px solid #cccccc; border-collapse:collapse; width:100%;" >
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
            <div style="font-weight:bold; font-size:16px;">Dues-Advance From Members<?php echo $Heading;?></div>
          <!--  <div style="font-weight; font-size:16px;">As on Date:<?php //echo date("d.m.Y");?></div> -->
            <div style="font-weight; font-size:16px;">From<?php echo '   '. getDisplayFormatDate($_POST['from_date']).' ';?> To<?php echo '   '.getDisplayFormatDate($_POST['to_date']).'      ';?></div>            
            <!--<div style="font-weight; font-size:16px;">Operator:<?php //echo $obj_view_reports->get_login_name($_SESSION['login_id']);?></div>-->
            
        </div>

   <div id="formDiv" class="no-print">
             <form method="post"  action="dues_advance_frm_member_report.php?sid=<?php echo $_SESSION['society_id'];?>"  class="no-print">
                <label id="dueslabel">Dues(>than):</label><input type="text" id="dues_range" name="dues_range"  style="width:80px" value="<?php echo $_POST['dues_range'];?>" >
                <label id="winglabel">Wing:</label><input type="text" id="wing" name="wing"  style="width:80px" value="<?php echo $_POST['wing'];?>" >
                <input type="hidden" id="from_date" name="from_date" value="<?php echo $_POST['from_date']; ?>" />
                <input type="hidden" id="to_date" name="to_date" value="<?php echo $_POST['to_date']; ?>"  /> 
                <input type="hidden" id="BillType" name="BillType" value="<?php echo $_POST['BillType']; ?>" />
                <input type="submit"  name="submit"  id="submit" value="Go" >
             </form>	
		<br/>
        </div>

        <table  style="width:100%;font-size:14px;border-collapse: collapse; " >
              <tr>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; border-left:none;">Sr. No.</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc; " >Wing</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;   width:10%;" colspan="3">Unit No.</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;   width:30%; " colspan="3">Member Name</th>
                <?php if($_POST['chkb1'] == 0)
				{?>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;   width:20%; " colspan="3">Dues (Dr.)</th>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-right:none;  width:20%;" colspan="3">Advance (Cr.)</th>                
            	<?php }
				else  if(isset($_POST['chkb1']) && $_POST['chkb1'] == 1) 
				{?>
                <th style="text-align:center;border-collapse: collapse;border:1px solid #cccccc;" >Amount (Rs.)</th>                
            	<?php } ?>
             </tr>
                <?php
				//echo 'test1';
				$count=0;
				$totalDuesAmount=0;
				$totalAdvAmount=0;
				$totalAmount =0;
				if($show_mem_due_details<>"")
				{																												
					foreach($show_mem_due_details as $k => $v)
					{	
						if($_POST['chkb1'] == 0)
						{																
								if(isset($_POST['dues_range']) && $_POST['dues_range'] <> '' && $_POST['dues_range'] > 0)
								{							
									if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Go' && $show_mem_due_details[$k]['amount'] > $_POST['dues_range'] ) //&&  strcasecmp($_POST['wing'],$show_mem_due_details[$k]['wing'])==0 )
									{								
										$totalDuesAmount+=$show_mem_due_details[$k]['amount'];
										$count++;
										echo "<tr><td style='text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>".$count."</td><td style='border-left:none;text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['wing']."</td><td colspan=3 style='text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['unit']."</td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['member']."</td><td colspan=3 style='text-align:right;border-collapse: collapse;border:1px solid #cccccc; '><a href='member_ledger_report.php?&uid=".$show_mem_due_details[$k]['unit_id']."' style='color:#0000FF;text-align:right;'>".number_format($show_mem_due_details[$k]['amount'],2)."</a></td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc;border-right:none; '></td></tr>";
									}
																	
								}
								else if($show_mem_due_details[$k]['amount']>0)
								{
									$totalDuesAmount+=$show_mem_due_details[$k]['amount'];
									$count++;
								echo "<tr><td style='text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>".$count."</td>
												<td style='border-left:none;text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['wing']."</td>
												<td colspan=3 style='text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['unit']."</td>
												<td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['member']."</td>
												<td colspan=3 style='text-align:right;border-collapse: collapse;border:1px solid #cccccc; '><a href='#' style='color:#0000FF;text-align:right;'  onClick='window.open(\"member_ledger_report.php?&uid=".$show_mem_due_details[$k]['unit_id']."\&from=".getDBFormatDate($_POST['from_date'])."\&to=".getDBFormatDate($_POST['to_date'])."\",\"unitReportPopUp\",\"type=fullWindow,fullscreen,scrollbars=yes\")'>".number_format($show_mem_due_details[$k]['amount'],2)."</a></td>
												<td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; border-right:none;'></td></tr>";
								}
								else if($show_mem_due_details[$k]['amount'] <0)
								{
									$totalAdvAmount+=$show_mem_due_details[$k]['amount'];
									$count++;
									echo "<tr><td style='text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>".$count."</td><td style='border-left:none;text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['wing']."</td><td colspan=3 style='text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['unit']."</td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['member']."</td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '></td><td colspan=3 style='text-align:right;border-collapse: collapse;border:1px solid #cccccc;border-right:none; '><a href='#' style='color:#0000FF;text-align:right;'  onClick='window.open(\"member_ledger_report.php?&uid=".$show_mem_due_details[$k]['unit_id']."\&from=".getDBFormatDate($_POST['from_date'])."\&to=".getDBFormatDate($_POST['to_date'])."\",\"unitReportPopUp\",\"type=fullWindow,fullscreen,scrollbars=yes\")'>".number_format(abs($show_mem_due_details[$k]['amount']),2)."</a></td></tr>";							
								}	
						}
						else if(isset($_POST['chkb1']) && $_POST['chkb1'] == 1)
						{
								if(isset($_POST['dues_range']) && $_POST['dues_range'] <> '' && $_POST['dues_range'] > 0)
								{							
									if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Go' && $show_mem_due_details[$k]['amount'] > $_POST['dues_range'] ) //&&  strcasecmp($_POST['wing'],$show_mem_due_details[$k]['wing'])==0 )
									{								
										$totalAmount += $show_mem_due_details[$k]['amount'];
										$count++;
										echo "<tr><td style='text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>".$count."</td><td style='border-left:none;text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['wing']."</td><td colspan=3 style='text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['unit']."</td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['member']."</td><td colspan=3 style='text-align:right;border-collapse: collapse;border:1px solid #cccccc; '><a href='#' style='color:#0000FF;text-align:right;'  onClick='window.open(\"member_ledger_report.php?&uid=".$show_mem_due_details[$k]['unit_id']."\&from=".getDBFormatDate($_POST['from_date'])."\&to=".getDBFormatDate($_POST['to_date'])."\",\"unitReportPopUp\",\"type=fullWindow,fullscreen,scrollbars=yes\")'>".number_format($show_mem_due_details[$k]['amount'],2)."</a></td></tr>";
									}
																	
								}
								else
								{
									$totalAmount += $show_mem_due_details[$k]['amount'];
									$count++;
									echo "<tr><td style='text-align:center;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>".$count."</td><td style='border-left:none;text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['wing']."</td><td colspan=3 style='text-align:center;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['unit']."</td><td colspan=3 style='text-align:left;border-collapse: collapse;border:1px solid #cccccc; '>".$show_mem_due_details[$k]['member']."</td><td colspan=3 style='text-align:right;border-collapse: collapse;border:1px solid #cccccc; '><a href='#' style='color:#0000FF;text-align:right;'  onClick='window.open(\"member_ledger_report.php?&uid=".$show_mem_due_details[$k]['unit_id']."\&from=".getDBFormatDate($_POST['from_date'])."\&to=".getDBFormatDate($_POST['to_date'])."\",\"unitReportPopUp\",\"type=fullWindow,fullscreen,scrollbars=yes\")'>".number_format($show_mem_due_details[$k]['amount'],2)."</a></td></tr>";
								}
														
						}
					}
					
					if( $_POST['chkb1'] == 0)
					{
						echo "<tr><td colspan='8' style='text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>***Total***</td><td  colspan='3' style='text-align:right;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc; '>".number_format($totalDuesAmount,2)."</td><td colspan='3' style='text-align:right;background-color: #D3D3D3;text-align:right;border-collapse: collapse;border:1px solid #cccccc;border-right:none; '>".number_format(abs($totalAdvAmount),2)."</td></tr>";
					}
					else if(isset($_POST['chkb1']) && $_POST['chkb1'] == 1)
					{
						echo "<tr><td colspan='8' style='text-align:center;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc;border-left:none; '>***Total***</td><td  colspan='3' style='text-align:right;background-color: #D3D3D3;border-collapse: collapse;border:1px solid #cccccc; '>".number_format($totalAmount,2)."</td></tr>";		
					}
			  }
              ?>			                
        </table>

</div>
</center>
</div>
</center>
</body>

</html>