
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Import Society</title>

</head>



<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
 	//Turn off all error reporting
    //error_reporting(0);
	include_once("classes/dbmanager.class.php");
	
	if(!isset($_REQUEST['Import'])) 
	{
		if(!isset($_REQUEST['set']))
		{
			$obj_dbManager = new dbManager();
			$dbName = $obj_dbManager->getEmptyDBName();
			
			if($dbName == '')
			{
				echo $dbName;
				?>
					<script>
					
						alert('No Database Available To Import New Society.\n\nPlease Contact System Administrator.');
						window.location.href = "initialize.php";
					</script>
				<?php	
				exit();
			}
			else
			{
				$_SESSION['dbname'] = trim($dbName);
				
				?>
					<script>
						localStorage.setItem('dbname', "<?php echo $_SESSION['dbname']; ?>");
						window.location.href = "import_society.php?set";
						
					</script>
				<?php	
			}
		}
	}
	
	include_once("includes/head_s.php");
	include_once("classes/bill_period.class.php");
	
	$obj_bill_period=new bill_period($m_dbConn);
//print_r($obj_unit_import);
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">
       $(function()
        {
            //$.datepicker.setDefaults($.datepicker.regional['']);
//            $(".basics").datepicker({ 
//            dateFormat: "yy-mm-dd", 
//            showOn: "both", 
//            buttonImage: "images/calendar.gif", 
//            buttonImageOnly: true 
        //})
		
		//var dropdwn1=document.getElementById('Period').value;
		//var dropdwn2=document.getElementById('eselObj').value;
		
		
		
		});
		
		
		
		
		$(document).ready(
        function(){
            $('input:submit').attr('disabled',true);
			$('form').submit(function () {

   			var society_name = $.trim($('#society_name').val());

				// Check if empty of not
				if (society_name  === '') {
					alert('Society Name is empty.');
					return false;
				}
			});
			$('form').submit(function () {

				// Get the Login Name value and trim it
				var BillingCycle = $.trim($('#Cycle').val());
			
				// Check if empty of not
				if (BillingCycle  === '') {
					alert('Billing Cycle Not Provided');
					return false;
				}
			});
			$('form').submit(function () {

				// Get the Login Name value and trim it
				var Period = $.trim($('#eselObj').val());
			
				// Check if empty of not
				if (Period  === '') {
					alert('Period To Import Not Provided');
					return false;
				}
			
			});
			$('form').submit(function () {

				// Get the Login Name value and trim it
				var RebateAmount = $.trim($('#rebate').val());
			
				// Check if empty of not
				if (isNaN(RebateAmount)) {
					alert('Must Input Number For Rebate Amount');
					return false;
				}
			
			});
			
			$('form').submit(function () {

				// Get the Login Name value and trim it
				var ChequeBounceCharge = $.trim($('#chq_bounce_charge').val());
			
				// Check if empty of not
				if (isNaN(ChequeBounceCharge)) {
					alert('Must Input Number For Cheque Bounce Charge');
					return false;
				}
			
			});
			$('form').submit(function () {

				// Get the Login Name value and trim it
				var Rate = $.trim($('#int_rate').val());
			
				// Check if empty of not
				if (Rate  === '') {
					alert('Bill Interest Rate Not Provided');
					return false;
				}
				if(isNaN(Rate))
				{
					alert('Must Input Number For Bill Interest Rate');
					return false;
				}
			});
				$('form').submit(function () {

				// Get the Login Name value and trim it
				var Year = $.trim($('#Year').val());
			
				// Check if empty of not
				if (Year == '') {
					alert('Data Import In Year Not Provided');
					return false;
				}
			})
		
			
            $('input:file').change(
			  
                function(){
                    if ($(this).val())
					{
						
                        $('input:submit').removeAttr('disabled');
						var files = $(this)[0].files;
						var f = document.getElementById("import_society");
     					f.setAttribute('method',"post");
						if(files.length > 1)
						{
							//alert("You have selected more than 1 file");
							 f.setAttribute('action',"process/import_report.process.php");
						}
						else
						{
							//alert("You have selected 1 file");
							 f.setAttribute('action',"display_data.php");
						} 
                    }
                    else 
					{
                        $('input:submit').attr('disabled',true);
                    }
                });
        });
    
	
	
	
</script>

<script language="javascript" type="text/javascript">

			function jsGetCycles(cycleid)
			{
				//alert("1");
				$('#eselObj').empty();
				
				var Months = [];
				Months = getMonths(parseInt(cycleid));
				
				for (var iCnt = 0; iCnt < Months.length; iCnt++)
				{
					$('#eselObj').append(
					$('<option></option>').val(Months[iCnt]).html(Months[iCnt]));
				}
				var year_id=document.getElementById('Year').value;
				//alert(year_id);
				if(year_id.length >0)
				{
					
				jsGetperiods(year_id);
				}
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


<form name="import_society"  id= "import_society" method="post" enctype="multipart/form-data" >

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Society Data</div>

<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 

      <tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">Particulars For Society Data </th></tr></table></th></tr>
         
 
   
   <tr align="left">
        	<td valign="middle"></td>
			<td>Society Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="society_name" id="society_name" value='<?php echo $_POST['society_name']; ?>'/></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Browse Society File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse"><input type="file" name="file[]" id="file" accept=".csv" multiple/></td>
            <td> &nbsp; Society Data Template File : <a href="samplefile/Society_Data.csv" download>Click here to Download</a> </td>
            
            
</tr>   


</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">Particulars From Bill Main File</th></tr></table></th></tr>
<tr align="left">
        		<td valign="middle"></td>
				<td>Billing Cycle<?php //echo $star; ?></td>
                <td>&nbsp; : &nbsp;</td>        
				<td>
                <select name="Cycle" id="Cycle" value='<?php echo $_POST['Cycle']; ?>' onChange="jsGetCycles(this.value);" > <!--disabled="disabled"-->
             	 <?php echo $combo_state=$obj_bill_period->combobox("select ID,Description from billing_cycle_master",'0');?>   
                  </select>
                  
                  </td><td><font color="#FF0000">(eg.if QtrMonth="April" then select Monthly)</font></td>	        
        
        </tr>
        
        
        <tr align="left">
                <td valign="middle"></td>
				<td>Period To Import<?php //echo $star; ?></td> 
                <td>&nbsp; : &nbsp;</td>       
				<td>
                <select name="eperiod" id="eselObj" value='<?php echo $_POST['eselObj']; ?>'> 
                </select>
                  
                 </td></td><td><font color="#FF0000">(QtrMonth of 'Bill Main' file you want to import)</font></td>	        
        
        </tr>
       
        
        
        
        
        <tr align="left">
        		<td valign="middle"></td>
				<td>Data Import Into Year 1<?php //echo $star; ?></td>
                <td>&nbsp; : &nbsp;</td>        
				<td>
                
                                <select name="Year" id="Year" value='<?php echo $_POST['Year']; ?>' onChange="jsGetperiods(this.value);"> <!--disabled="disabled"-->
             	 <?php echo $combo_state = $obj_bill_period->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", DEFAULT_YEAR ); ?>   
                  </select>
                  
                  </td><td><font color="#FF0000">(Financial Year of 'Bill Main')</font></td>	        
        
        </tr>
        
        <script>
			function jsGetperiods(year_id)
			{
				var cycleID=document.getElementById('Cycle').value;
				//alert(cycleID);
				populateDDListAndTrigger('select#Period', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id + '&cycleID='+cycleID, 'period', 'periodFetched', false);
			}
			function periodFetched()
			{
				
			}
		</script>
        
        
         
  		<!--<tr align="left">
        		<td valign="middle"></td>
				<td>Data Import Into Period<?php //echo $star; ?></td>        
				<td>&nbsp; : &nbsp;</td>
                <td>-->
                <!--<select name="Period" id="Period" value='<?php echo $_POST['Period']; ?>'> --><!--disabled="disabled"-->
             	 <?php //echo $combo_state = $obj_bill_period->combobox("select ID,Type from period where status='Y'",'0'); ?>   
                 <!-- </select>-->
                  <input type="hidden"  name="Period" id="Period"  value='<?php echo $_POST['Period']; ?> ' />
                  
               <!--   </td>		        
        </tr>       --> 
        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <BR/>
	<BR/>
     

      <tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">Particulars For Bill </th></tr></table></th></tr>
         
   <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Interest Rate</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="int_rate" id="int_rate" value='21'/></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Interest Method</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="int_method" id="int_method" value='<?php echo $_POST['int_method']; ?>'>
            		
                    <OPTION VALUE="<?php echo INTEREST_METHOD_FULL_MONTH; ?>" >Full Month</OPTION>
                     <OPTION VALUE="<?php echo INTEREST_METHOD_FULL_MONTH; ?>" >Full Cycle</OPTION>
                	<OPTION VALUE="<?php echo INTEREST_METHOD_DELAY_DUE; ?>">Delay After Due Days</OPTION>
                </select>
            </td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td><!--Interest Trigger Amount--></td>
            <td><!--&nbsp; : &nbsp;--></td>
			<td><!--<input type="hidden" name="int_tri_amt" id="int_tri_amt" value="0" >--></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Rebate Method</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="rebate_method" id="rebate_method" value='<?php echo $_POST['rebate_method']; ?>'>
            		<OPTION VALUE="<?php echo REBATE_METHOD_NONE; ?>">None</OPTION>
                    <OPTION VALUE="<?php echo REBATE_METHOD_FLAT; ?>">Flat Amount</OPTION>
                    <OPTION VALUE="<?php echo REBATE_METHOD_WAIVE; ?>">Rebate Method Waive</OPTION>
                    <!--<OPTION VALUE="<?php //echo REBATE_DUE_WAIVER; ?>">Due Amount Waiver</OPTION>-->
                </select>
            </td>
		</tr>

        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill  Rebate Amount</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="rebate" id="rebate" value='0'/></td>
		</tr>
     
        


<tr align="left">
        	<td valign="middle"></td>
			<td>Cheque Bounce Charges</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="chq_bounce_charge" id="chq_bounce_charge" value='100'/></td>
		</tr>
     
        
<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
<input type="hidden" name="flag" value="6">
 <td colspan="4" align="center"><input type="submit" name="Upload" id="Upload" value="Import" disabled on class="btn btn-primary"  accept="application/msexcel" /></td>
</tr>
<input type="hidden" name="bid" value="<?php echo $_SESSION['db_name'];?>">
</table>
<!--<details><p><font color="#FF0000">Please select Society File</font></p></details>-->
</center>

 
<!--<div id="show" style="color:#FF0000; font-weight:bold;"></div>
       <iframe id="uploadframe" name="uploadframe" src="<?php //echo $show_op;?>" width="800" height="500" scrolling="yes" frameborder="0"><?php //echo $show_op; ?></iframe>
    </div>-->

</form>
</div>

</div>
<?php include_once "includes/foot.php"; ?>