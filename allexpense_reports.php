<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - All Expense Report</title>
</head>
<?php

include_once("classes/include/dbop.class.php");
include_once("includes/head_s.php"); 
include_once("classes/utility.class.php");
include_once "classes/dbconst.class.php";
include_once "classes/include/fetch_data.php";
error_reporting(0);
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_Utility = new utility($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
//echo $objFetchData->objSocietyDetails->sSocietyName;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Period</title>
</head>
<body>
  <div id="middle">
     <div class="panel panel-info">
          <div class="panel-heading" id="pageheader">Expense Report Details</div>
            <center>
            <div>
            	<table style="width:40%;">
                   <tr> <td colspan="3"> <br /> </td> </tr>
                   <tr>
                   	<th> &nbsp;&nbsp; Report Type </th>
                    <td> : &nbsp; </td>
                    <td>
                    	<select name="report_name" id="report_name"  style=" width: 150px;">
                        	<option value="1"> Normal Report </option>
                        	<option value="2"> Expanded Report </option>
                    	</select>
                    </td>
                        </tr>
                        <tr id="from">        	
                            <th> &nbsp;&nbsp; From </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="from_date" id="from_date"  class="basics" size="10" readonly   value="<?php if(isset($_SESSION['from_date'])){ echo $_SESSION['from_date']; } else { echo getDisplayFormatDate($CurrentMonthBeginingDate); }?>" style="width:100px;"/></td>
                        </tr>
                        <tr id="to">        	
                            <th> &nbsp;&nbsp; To </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly   value=<?php if(isset($_SESSION['to_date'])){ echo $_SESSION['to_date']; } else { echo date("d-m-Y"); } ?>  style="width:100px;"/></td>
                        </tr>
                        
                        <tr> <td colspan="3" style="text-align:center"> <button id="fetch_report_btn" class="fetch_report_btn  btn-primary btn"  style="margin-top: 10px;">Fetch Report</button>
                        <input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()"  style="display:none;margin-top: 10px;"/>
                        <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;margin-top: 10px;"/>
                        </td> </tr>
                                                 
                        <tr> <td colspan="3"> <br /> </td> </tr>
                    </table>
                     <div id="error-msgs" class="Print_JOBCARD_MSG text-danger"></div>
               		<hr>
               </div>
              <div id="society_name" style="display:none"><?php echo $objFetchData->objSocietyDetails->sSocietyName ?></div> 
              <div id="showTable"></div>
             </center>
           </div>
            
         </div>
                            
        
<?php include_once "includes/foot.php"; ?>
<script>
function go_error()
{
	setTimeout('hide_error()',3000);	
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
function hide_error()
{
	$(".Print_JOBCARD_MSG").html('');
}
	
function Expoort()
{
	//document.getElementById('societyname').style.display ='block';	
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	//document.getElementById('societyname').style.display ='none';	
}

function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	
	var printContents = document.getElementById('showTable').innerHTML;
	var name = '<div id="society_name" style="text-align:center; font-size:18px" ><b><?php echo $objFetchData->objSocietyDetails->sSocietyName ?></b></div>';//document.getElementById('society_name').style.display ='block';		
	document.body.innerHTML =name+printContents;
	window.print();
	document.body.innerHTML= originalContents;
}
$(document).ready(function(){
        
            $(document).on('click','#fetch_report_btn', function(){
                
                var reportType = $('#report_name').val();
				 var fromDate = $('#from_date').val();
				  var toDate = $('#to_date').val();
               //console.log("reportType",reportType);
               /* if(reportType == "")
                {
                    $(".Print_JOBCARD_MSG").html('Please Select Report Type');
                    go_error();
                    return false;
                }*/
				
				 $.ajax({
                    url:'ajax/expense_report.ajax.php',
                    type:"POST",
                    cache:false,
                    data:{'method':'fetchExpeseReportDetails','reportType':reportType,'from':fromDate,'to':toDate},
					success:function(data)
                    {
						//console.log(data);
						document.getElementById('showTable').innerHTML = data;
						document.getElementById('btnExport').style.display='inline-block';
						document.getElementById('Print').style.display='inline-block';
					}
				 });
                
          });     
        });
        </script>
        </body>
    
</html>

