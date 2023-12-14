<?php
include_once("includes/head_s.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/smsQuota.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_smsQuota = new smsQuota($m_dbConn,$m_dbConnRoot);
$clientId = $_SESSION['client_id'];
$smsQuotaId=$_REQUEST['smsQuotaId'];
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
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
		function validateForm()
		{
			var societyId = document.forms["addSMSQuota"]["societyId"].value;
			var sellDate = document.forms["addSMSQuota"]["sellDate"].value;
			var soldBy = document.forms["addSMSQuota"]["soldBy"].value;
			var smsAllotted = document.forms["addSMSQuota"]["smsAllotted"].value;
			var amount = document.forms["addSMSQuota"]["amount"].value;
			var paymentStatus = document.getElementsByClassName("pStatus");
			if(societyId <= 0)
			{
				alert ("Society must be selected.");
				return false;
			}
			if( sellDate == "")
			{
				alert ("Selling Date must be filled out.");
				return false;
			}
			if( soldBy == "")
			{
				alert ("Sold By must be filled out.");
				return false;
			}
			if( smsAllotted == "")
			{
				alert ("SMS Allotted must be filled out.");
				return false;
			}
			if(amount == "")
			{
				alert ("Amount must be filled out.");
				return false;
			}
			if(!paymentStatus[0].checked && !paymentStatus[1].checked)
			{
				alert ("Payment Status must be check.");
			}
		}
		$( document ).ready(function()
		{
			var method = "<?php echo $_REQUEST['method'];?>";
			var smsQuotaId;
			//alert (smsQuotaId);
			if( method == "edit" )
			{
				smsQuotaId = "<?php echo $_REQUEST['smsQuotaId']; ?>";
				$.ajax
				({
					url : "ajax/addSMSQuota.ajax.php",
					type : "POST",
					datatype: "JSON",
					data : {"method":"fetchSMSQuota","smsQuotaId":smsQuotaId},
					success : function(data)
					{	
						//alert (data);
						var a		= data.trim();	
						var arr2	= new Array();
						arr2		= a.split("#");
						//alert (arr2);
						document.getElementById("clientId").value = arr2[1];
						document.getElementById("societyId").value = arr2[2];
						document.getElementById("sellDate").value = arr2[3];
						document.getElementById("soldBy").value = arr2[4];
						document.getElementById("smsAllotted").value = arr2[5];
						document.getElementById("amount").value = arr2[6];
						if(arr2[7] == "1")
						{
							document.getElementById("paymentStatus").checked = true;
						}
						document.getElementById("note").value = arr2[8];
						document.getElementById("btnSubmit").value = "Update";
					}
				});
			}
		});
	</script>
	</head>
	<body>
	<div id="middle">
		<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
      		<?php if($_REQUEST['method']=="edit")
	  		{
			?>
      			<div class="panel-heading" id="pageheader">Update SMS Quota</div>
      		<?php 
	 		}
	  		else
			{?>
        		<div class="panel-heading" id="pageheader">Add SMS Quota</div>
        	<?php 
			}?>
			<br>
            <?php
			$star = "<font color='#FF0000'>*</font>";
			?>
			<form name="addSMSQuota" id="addSMSQuota" method="post" action="process/addSMSQuota.process.php" enctype="multipart/form-data"  onSubmit="return validateForm();">
			<table align='center' id="data_table" width="100%">
           		 <tr>
                 	<input type="hidden" id="smsQuotaId" name="smsQuotaId" value="<?php echo $smsQuotaId?>">
                    <input type="hidden" id="clientId" name="clientId" value="<?php echo $clientId;?>">
                    <td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Select Society&nbsp;:&nbsp;</b></td>
					<td width="10%" style="padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<select id="societyId" name="societyId">
                        	<?php
								if($_SESSION['login_id'] == "4")
								{
									echo $obj_smsQuota->comboboxForSociety("SELECT `society_id`, `society_name` FROM `society`","0");
								}
								else
								{
									echo $obj_smsQuota->comboboxForSociety("SELECT `society_id`, `society_name` FROM `society` where `client_id` =".$clientId,"0");
								}
								?>
                        </select>
            		</td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Selling Date&nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="sellDate" name="sellDate" class="basics"/>        
            		</td>
                </tr>
        		<tr>
					<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Sold By &nbsp;:&nbsp;</b></td>
                    <td width="10%" style="padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="soldBy" name="soldBy"/>        
            		</td>
				</tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>SMS Allotted&nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="smsAllotted" name="smsAllotted"/>        
            		</td>
                </tr>
                
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Amount &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<input type="text" id="amount" name="amount" placeholder="Rs."/>        
            		</td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><?php echo $star;?><b>Payment Status&nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
                	<td width="50%" style="padding-top:1%">
                		<input type="checkbox" name="paymentStatus" id="paymentStatus" value="1" class="pStatus" /><b>Received</b>&nbsp;&nbsp;
            		</td>
                </tr>
                <tr>
                	<td style="text-align:right;width:40%;vertical-align:center;padding-top:1%"><b>Note &nbsp;:&nbsp;</b></td>
                    <td style="width:10%;padding-top:1%"></td>
					<td width="50%" style="padding-top:1%">
                		<textarea id="note" name="note" cols="70" rows="4"></textarea>   
            		</td>
                </tr>
	 	</table>
        <br>
        <!--<input type="button" id="printButton" name="printButton" style="display:none" value="Print" onClick="printFunction()" class="btn btn-primary" />-->
        <center>
        <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-primary"/>
         <br>
         <br>
        </center>
	</form>
</body>
</div>
</div>
</html>
<?php include_once "includes/foot.php"; ?>