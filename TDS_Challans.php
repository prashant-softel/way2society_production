
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head><title>W2S - TDS Challan Reports</title>
</head>




<?php 
include_once("includes/head_s.php");
include_once("classes/utility.class.php");
include_once("classes/dbconst.class.php");

$obj_Utility = new utility($m_dbConn);
$societyDetails = $obj_Utility->GetSocietyInformation($_SESSION['society_id']);

if($_SESSION['default_year'] == 0)
{?>
	<script> alert("Please Set Default Value For Current Year.");window.location.href="defaults.php";</script>
<?php	
}
else{
$CurrentMonthBeginingDate = $obj_Utility->getCurrentYearBeginingDate($_SESSION['default_year']);
}

?>

<script language="javascript">
	<?php
		if(isset($_REQUEST['report_name']))
		{
			$status1 = $obj_Utility->getIsDateInRange(getDBFormatDate($_REQUEST['from_date']), getDBFormatDate($_SESSION['default_year_start_date']), getDBFormatDate($_SESSION['default_year_end_date'])); 
			if($status1)
			{
				$_SESSION['from_date'] = $_REQUEST['from_date'];	
			}
			else
			{
				$_SESSION['from_date'] = getDisplayFormatDate($_SESSION['default_year_start_date']);		
			}
			
			$status2 = $obj_Utility->getIsDateInRange( getDBFormatDate($_REQUEST['from_date']) , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 
			if($status2)
			{
				$_SESSION['to_date'] = $_REQUEST['to_date'];
			}
			else
			{
				$_SESSION['to_date'] = 	getDisplayFormatDate($_SESSION['default_year_end_date']);		
			}
			
			if($_REQUEST['report_name'] == 'TDS_Deductee')
			{ ?>
				//window.location.href = "TrialBalance.php" target="_blank";
				 window.open('tds_challandeduction_report.php','_blank');
			<?php	  
			}
			else if($_REQUEST['report_name'] == 'TDS_Challan')
			{ ?>
				//window.location.href = "IncomeStmt.php";
				window.open('tds_challan_report.php','_blank');
			<?php					
			}
		}			
	?>
</script>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Financial Reports</title>                                              
                <script type="text/javascript">
				$( document ).ready(function() {
    				$('#ShowPreviousYearBalance').hide();
				});
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
					
                </script>        
        </head>   
        <body>        
            <div id="middle">
            <div class="panel panel-info" style="height:400px;">
                <div class="panel-heading" id="pageheader"> TDS Challan Reports </div>
    		<center>
            	<form name="accounting_report" id="accounting_report" method="post">
                	<table style="border:1px solid black; width:40%;">
                    	<tr> <td colspan="3"> <br /> </td> </tr>
                    	<tr><h4><font  style="width:50px" color="#003399">Please select the period</font></h4></tr>
                        <tr>
                        	<th> &nbsp;&nbsp; Report Type </th>
                            <td> : &nbsp; </td>
                            <td>
                        	<select name="report_name" id="report_name">
                            	<option value="TDS_Deductee"> TDS Deduction </option>
                                <option value="TDS_Challan"> TDS Challan </option>
                            </select>
                            </td>
                        </tr>
                        <tr id="from">        	
                            <th> &nbsp;&nbsp; From </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="from_date" id="from_date"  class="basics" size="10" readonly   value="<?php if(isset($_SESSION['from_date'])){ echo $_SESSION['from_date']; } else { echo getDisplayFormatDate($CurrentMonthBeginingDate); }?>" style="width:80px;"/></td>
                        </tr>
                        <tr id="to">        	
                            <th> &nbsp;&nbsp; To </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly   value=<?php if(isset($_SESSION['to_date'])){ echo $_SESSION['to_date']; } else { echo date("d-m-Y"); } ?>  style="width:80px;"/></td>
                        </tr>
                        
                        <tr> <td colspan="3"> <br /> </td> </tr>
                        <tr>
                            <td colspan="3" align="center">                               	                         
                            	<input type="submit" name="insert" id="insert" value="Generate Report" style="color: #fff;background-color: #337ab7;border-color: #2e6da4;width:30%;height:20%;margin-top:5%">  
                        	</td>
                      	</tr>                          
                        <tr> <td colspan="3"> <br /> </td> </tr>
                    </table>
                </form>            
            </center>
            </div>
            </div>                       			
               
	<?php include_once "includes/foot.php"; ?>