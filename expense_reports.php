<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Expense Report</title>
</head>

<?php

include_once("classes/include/dbop.class.php");
include_once("includes/head_s.php"); 
include_once("classes/utility.class.php");
include_once "classes/dbconst.class.php";
include_once("classes/society.class.php");
error_reporting(0);
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_Utility = new utility($m_dbConn);
$obj_society = new society($m_dbConn, $m_dbConnRoot);



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
                <div class="panel-heading" id="pageheader">
                	Expense Report
                </div>
            	<center>
                        <table>
                        	</br></br>
                            <tr>
                            	<td valign="middle">Select Period Type </td>
                                <td valign="middle">&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                                <td valign="middle">
                                	<select id="period_type" class="period_type">
                                    	<?php echo $combo_bill_cycle = $obj_society->combobox("select `ID`,`Description` from `billing_cycle_master`",0,true); ?>
                                    </select>
                                </td>
                                <td>
                                </td>
                                <td><button id="fetch_report_btn" class="fetch_report_btn  btn-primary btn" >Fetch Report</button></td>
                                <td>
                					<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
                                    	<input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()"  style="display:none;"/>
                                    <?php }?>	
                				</td>
                                <td>
                                  <?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
                                 		<input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;"/>
                                  <?php }?>	
                                </td>
                            </tr>
                            <tr><td colspan="2"></td><td colspan="4"> <input type="checkbox" name="shownonzero" id="shownonzero"  value="1"/>&nbsp;&nbsp;<span style="line-height: 23px;">Show Non Zero<span></td></tr>

                        </table>
                 
                <div style="height:50px;"></div>
                <div id="error-msgs" class="Print_JOBCARD_MSG text-danger"></div>
                <div id="loader_img"></div>
                
                <div id='showTable' style="font-weight:lighter;">
                
                </div>
                </center> 
            </div>
            
        </div>
		
        
        <?php include_once "includes/foot.php"; ?>
        
        
		<script>
        
        function go_error()
        {
            setTimeout('hide_error()',3000);	
        }
        
        function hide_error()
        {
            $(".Print_JOBCARD_MSG").html('');
        }
		
		function Expoort()
		{
			document.getElementById('societyname').style.display ='block';	
			$("a").removeAttr("href");	
			window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
			document.getElementById('societyname').style.display ='none';	
		}
		
		function PrintPage() 
		{
			var originalContents = document.body.innerHTML;
			document.getElementById('societyname').style.display ='block';	
           	$("a").removeAttr("href");	
			var printContents = document.getElementById('showTable').innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML= originalContents;
		}
        
		$(document).ready(function(){
        
            $(document).on('click','#fetch_report_btn', function(){
                
                var period_type = $('#period_type').val();
                
                if(period_type == 0)
                {
                    $(".Print_JOBCARD_MSG").html('Please Select Period');
                    go_error();
                    return false;
                }
                
                $.ajax({
                    url:'ajax/expense_report.ajax.php',
                    type:"POST",
                    cache:false,
                    data:{'method':'fetchExpeseReport','period_type':period_type},
                    beforeSend: function(){
                        $("#loader_img").html("<img src='/images/loader/loader.gif'/>");
                        $(".Print_JOBCARD_MSG").html('Please Wait....');
						$("#Print").hide();
						$("#btnExport").hide();
                        
                    },
                    complete: function(){
                        $("#loader_img").html("");
                        $(".Print_JOBCARD_MSG").html('');
						$("#Print").show();
						$("#btnExport").show();
                    },
                    success:function(data)
                    {
                        var result = data.split('@@@');
                        var expenseDetails = JSON.parse("["+result[1]+"]");
                        var expenseLedgerList = expenseDetails[0]['ExpenseLedgerList'];
                        var expenseHeadList = expenseDetails[0]['PeriodHead'];
                        var expenseData = expenseDetails[0]['data'];
                        var finalTotal = 0
						var footerTotal = [];
						
						var mainBody = "<div style='display:none;' id='societyname'><center><h1><font>"+expenseDetails[0]['society_name']+"</font></h1></center></div>";
                        mainBody += "<div><center><font style='font-size: 20px'>Expense Report of "+expenseDetails[0]['Year']+"</font></center></div>";
                        
                        var table = "<br><br><table style='text-align:left; width:100%;' class='table table-bordered table-hover table-striped' cellpadding='50'>";
                        table +="<tr style='border:1px solid #ddd;'><th style='border:1px solid #ddd; text-align:center;'>Expense Ledgers ( Category )</th>";
                        
                        for(var j = 0 ; j < expenseHeadList.length; j++)
                        {
                            table +="<th style='border:1px solid #ddd; text-align:center;'>"+expenseHeadList[j]+"</th>";
							footerTotal[j] = 0;
                        }
                        table +="<th style='border:1px solid #ddd; text-align:center;'>Total</th>";
                        table +="</tr>";
                        
                        for(var i = 0; i < expenseLedgerList.length; i++)
                        {
                            var ledgerName = expenseLedgerList[i].ledger_name;
                            var category = expenseLedgerList[i].category_name;
                            var id = expenseLedgerList[i].id;
                            var groupId = expenseLedgerList[i].group_id;
                            var temptable = "";
                            temptable += "<tr style='border:1px solid #ddd;'>";
                            temptable += "<td style='border:1px solid #ddd; text-align:center;'><a href='view_ledger_details.php?lid="+id+"&gid="+groupId+"' target='_blank'>"+ledgerName+" ( "+category+" )</a></td>";
                            
							var ledgerTotal = 0;
							
							for(var j = 0 ; j < expenseHeadList.length; j++)
                            {
                                if(expenseData[expenseHeadList[j]] != undefined)
                                {
                                    if(ledgerName in expenseData[expenseHeadList[j]])
                                    {
										ledgerTotal += parseFloat(expenseData[expenseHeadList[j]][ledgerName]);
                                        footerTotal[j] += parseFloat(expenseData[expenseHeadList[j]][ledgerName]);
										temptable +="<td style='border:1px solid #ddd; text-align:center;'>"+expenseData[expenseHeadList[j]][ledgerName]+"</td>";
                                    }
                                    else
                                    {
                                        temptable +="<td style='border:1px solid #ddd; text-align:center;'>0.00</td>";
                                    }
                                }
                                else
                                {
                                  temptable +="<td style='border:1px solid #ddd; text-align:center;'>0.00</td>";
                                }
                            }
                            if(ledgerTotal == 0 && document.getElementById('shownonzero').checked == true)            
                            {
                                continue;
                            } 
                            table += temptable; 
							finalTotal += ledgerTotal;
							table += "<td style='border:1px solid #ddd; text-align:center;'>"+ledgerTotal.toFixed(2)+"</td>";
                            table += "</tr>";
                        }
						table += "<tfoot><th style='border:1px solid #ddd; text-align:center;'>Final Total</th>";
						for(var k =0; k < footerTotal.length; k++)
						{
							table += "<th style='border:1px solid #ddd; text-align:center;'>"+footerTotal[k].toFixed(2)+"</th>";	
						}
						table += "<th style='border:1px solid #ddd; text-align:center;'>"+finalTotal.toFixed(2)+"</th></tfoot>";
                        table += "</table>";
                        mainBody += table+"</div>";
                        $('#showTable').html(mainBody);
                    }
                });	
            });
        });
        </script>
		
    </body>
    
</html>





















