<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Vehicle Parking Report</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } 
  include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/parking.class.php");
include_once("classes/genbill.class.php");
$obj_genbill = new genbill($m_dbConn, $m_dbConnRoot);
$objParking = new  Parking($m_dbConn);
//echo "dbName:".$_SESSION['dbname'];
$yearDetails = $objParking->getYears(); 
?>
<html>
<head>
<script>
	function changePeriod(str)
	{
		//alert (str);
		$.ajax
		({
			url : "ajax/ajaxgenbill.php",
			type : "POST",
			datatype: "JSON",
			data : {"method":"getPeriodByYear","YearId":str},
			success : function(data)
			{	
				//alert(data);
				document.getElementById("selectPeriod").innerHTML = data;
			}
		});
	}
	function getSelectedCheckBox()
	{
		 //alert(getSelectedCheckBox);
		 var unitId;
		 var unitIdArr = [];
		 var ledgerNameArr = [];
		 var amountArr = [];
		 var details ="";
         var detailsArr = [];
		 var yearId = "";
		 var periodId = "";
		 $.each($("input[name='checkbox']:checked"), function()
		 {            
         	unitId = $(this).val();
			details = document.getElementById(unitId).value;
			detailsArr = details.split(",");
			//alert(detailsArr[0]);
			//alert(detailsArr[1]);
			//alert(detailsArr[2]);
			unitIdArr.push(unitId);
			amountArr.push(detailsArr[1]);
			ledgerNameArr.push(detailsArr[2]);
         });
		 yearId = document.getElementById("year").value;
		 periodId = document.getElementById("period").value;
		 $.ajax
			({
				url : "ajax/ajaxgenbill.php",
				type : "POST",
				datatype: "JSON",
				data : {"method":"UpdateGenBillFromParking","unitId":JSON.stringify(unitIdArr),"amount":JSON.stringify(amountArr),"ledgerName":JSON.stringify(ledgerNameArr),"yearId":yearId,"periodId":periodId},
				success : function(data)
				{	
					///alert(data)
					if(data == "Success")
					{
						window.location.reload();
						alert("Records Updated Successfully");
					}
				}
			});
	}
</script>
	<script type="text/javascript" src="js/jsDatatableAdvanceFunction.js"></script>
    <script src="datatools/js/dataTables.checkboxes.min.js"></script>
    <link href="datatools/css/dataTables.checkboxes.css" rel="stylesheet"></link>
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
    text-align: left;
    border-radius: 6px;
    padding: 5px 0;
	margin-left: 5%;

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
</head>

<body>
<br/>
     <div id="middle">
<br>
<?php
    if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
    {
        ?>
        <div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
   <?php
    }
    else
    {
        ?> <div class="panel panel-info" id="panel" style="display:none;"> 
        <!-- <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:75%">-->
        <?php
    }
?>
        <div class="panel-heading" id="pageheader">Vehicle Parking Report 
        <input type="button" id="" value="Update All"  data-toggle='modal' data-target='#myModal' class="btn btn-primary"  style="width:100px;padding: 2px 7px;float:right;"/></div>
   <center>
<table align="center" border="0" width="100%">
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
</tr>
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $objParking->MemberParkingListings(true);
?>
</td>
</tr>
</table>
</center>
        
      </div>
</div>

</div>
</body>
</html>
<!--<script>
$(document).ready(function() {
	
$('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();
datatableRowGrouping(9, 9);
} );
</script>-->
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
  <!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog">
      <!-- Modal content-->
      	<div class="modal-content">
      		<div class="modal-header" style="background-color: #d9edf7;min-height: 0px;padding: 0px">
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
          			<h4 class="modal-title">Select Period</h4>
        	</div>
        	<div class="modal-body">
          		<table>
          			<tr style="border-bottom: 1px solid!important">
                    	<td style="width: 40%">
		          			<b>Select Year : </b>
			      		</td>
			      		<td>
			       			<select name="year" id="year" onChange="changePeriod(this.value)">
			              		<option value = "0">Select Year</option>
							<?php 
						  	for($i=0;$i<sizeof($yearDetails);$i++)
						  	{
								  ?>
                             	  <option value="<?php echo $yearDetails[$i]['YearID']?>"><?php echo $yearDetails[$i]['YearDescription'];?></option>
                                  <?php 
						    }
						   ?> 
			                </select>
				   	    </td>
               		</tr>
                    <tr>
          				<td style="width: 40%">
		          			<b>Select Period : </b>
			      		</td>
			      		<td id = "selectPeriod">
			       			<select name="period" id="period">
			              		<option value="0">Select Period</option>
			                </select>
				   	    </td>
					</tr>
          		</table>
        	</div>
        	<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal"  onClick='getSelectedCheckBox()'>Update</button>
          		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        	</div>
     	</div>
	</div>
</div>
<?php include_once "includes/foot.php"; ?>