<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Add Parking Type</title>
</head>

<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/parkingType.class.php");
$obj_parkingType=new parkingType($m_dbConn,$m_dbConnRoot);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/tenant_20190424.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="js/lien.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
	<script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
		$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics_Dob").datepicker(datePickerOptions)
		});

	</script>
  
	<script type="text/javascript">
		var datePickerOptions={ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            buttonImageOnly: true ,
            defaultDate: '01-01-1980'
        };
	</script>
	<script type="text/javascript">
	//parkingType Related Js
	$( document ).ready(function()
	{
		var method="<?php echo $_REQUEST['method'];?>";
		var Id;
		if( method == "edit" )
		{
			Id = "<?php echo $_REQUEST['Id']; ?>";
			$.ajax
			({
				url : "ajax/parkingType.ajax.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"getParkingType","Id":Id},
				success : function(data)
				{	
					//alert (data);
					var a		= data.trim();
					var arr2	= new Array();
					arr2		= a.split("#");
					//alert ("arr1:"+arr1);
					//alert ("arr2:"+arr2);
					document.getElementById('parkingTypeId').value=arr2[0];
					document.getElementById('parkingType').value=arr2[1];
					document.getElementById('description').value=arr2[2];
					document.getElementById("rate").value = arr2[3];
					if(arr2[4]=="1")
					{
						document.getElementById("visible").checked = true;
					}
					document.getElementById("ledgerId").value = arr2[5];
					document.getElementById("btnSubmit").value = "Update";
				}
			});
		}
	});
	//Validation
	function validateForm()
	{
		var parkingType = document.forms["addParkingType"]["parkingType"].value;
		var rate = document.forms["addParkingType"]["rate"].value;
		var ledgerId = document.forms["addParkingType"]["ledgerId"].value;
		if(parkingType == "")
		{
			alert ("Parking Type must be filled out..");
			return false;
		}
		if( rate == "")
		{
			alert ("Parking Charges must be filled out.")
			return false;
		}
		if( ledgerId == "")
		{
			alert ("Ledger Id must be filled out.");
			return false;
		}
	}
	function clear()
	{
		document.getElementById("parkingType").value = "";
		document.getElementById("rate").value = "";
		document.getElementById("ledgerId").value = "0";
		document.getElementById("description").value = "";
		document.getElementById("visible").checked = false;
	}
	/*function checkLoanStatus() 
	{
    	var gender = document.getElementsByName("loanStatus");
    	var genValue = false;
		var i = 0;
    	for(i=0; i<gender.length;i++)
		{
            if(gender[i].checked == true)
			{
                genValue = true;    
            }
        }
        if(!genValue)
		{
            alert("Please Choose the Loan Status.");
            return false;
        }
		return true;
	}​*/
	</script>
    	
	</head>
	<body>
	<div id="middle">
		<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:96%;">
      		<?php if($_REQUEST['method']=="edit")
	  		{
			?>
      			<div class="panel-heading" id="pageheader">Update Parking Type</div>
      		<?php 
	 		}
	  		else
			{?>
        		<div class="panel-heading" id="pageheader">Add Parking Type</div>
        	<?php 
			}
			$star = "<font color='#FF0000'>*</font>";
			?>
			<br>
		<form name="addParkingType" id="addParkingType" method="post" action="process/addParkingType.process.php" enctype="multipart/form-data"  onSubmit="return validateForm();">
			<table align='center' id="data_table" width="100%">
           		 <tr>
                 	<input type="hidden" id="parkingTypeId" name="parkingTypeId">
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Parking Type &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                    	<input type="text" id="parkingType" name="parkingType" />
            		</td>
                </tr>
                 <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Description &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="description" name="description"/>        
            		</td>
                </tr>
        		<tr>
					<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Parking Charges &nbsp;:&nbsp;</b></td>
                    <td width="10%" style="padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="rate" name="rate" placeholder="Rs."/>        
            		</td>
				</tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Visible to User &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="checkbox" name="visible" id="visible" value="1" /><b>Yes</b>
            		</td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Linked to Ledger Id &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<select name="ledgerId" id="ledgerId">
                         	<?php
								echo $obj_parkingType->ComboboxForLedger("SELECT `id`,`ledger_name` FROM `ledger` where `status` = 'Y' AND show_in_bill = '1' ORDER BY `ledger_name` ASC", "0");
							?>  
                        </select>
            		</td>
                </tr>
           </table>
         <center>
        	<br>
        		<input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-primary"/> &nbsp; &nbsp;
                <input type="reset" id="btnCancel" name="btnCancel" value="Clear" class="btn btn-primary" onClick="clear()"/>
         	<br>
            <br>
        </center>
	</form>
</body>
</div>
</div>
</html>
<?php include_once "includes/foot.php"; ?>