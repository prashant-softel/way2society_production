<?php include_once("includes/head_s.php");
 include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
// include_once("classes/FixedDeposit.class.php");
 include_once("classes/view_ledger_details.class.php");
 $obj_ledger_details = new view_ledger_details($m_dbConn);
	$result = $obj_ledger_details->getChallanList();
	//print_r($result);
 ?>
 
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    
   <style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}
 .fade.in {
    display: block !important;
}
.modal-backdrop {display: none;}
 </style>   
</head>
<body>
<center>
<div class="panel panel-info" id="panel" >
    <div class="panel-heading" id="pageheader">Challan List </div>
    <br>
	<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
    </div>
		 
<br>     

<table id="example" class="display" cellspacing="0" width="100%" >
	<thead>
        <tr>
        	<th >Print</th>
            <th >Edit</th>
            <th style="text-align: center;">Challan Ref No.</th>
            <th style="text-align: center;">Challan No.</th>
            <th style="text-align: center;">BSR Code</th>
            <th style="text-align: center;">Assessment Year</th>
           	<th style="text-align: center;">Challan Date </th>
            
            <th style="text-align: center;">Paid To </th>
            <th style="text-align: center;">Nature Of TDS</th>
            <th style="text-align: center;">Total Amount</th>
             <th style="text-align: center;">Comment</th>
            <!--<th style="text-align: center;">TDS %</th>
            <th style="text-align: center;">TDS Deducted</th>
            <th style="text-align: center;">Challan Genretated</th>-->
            
        </tr>
    </thead>
    
    <?php 
	for($i=0; $i<sizeof($result);$i++)
	{?>
    <tr>
    <td style="text-align: center;">
                	<a href="print_challan.php?id=<?php echo $result[$i]['id']?>" target="_blank"><img src="images/print.png" width="35" style="display:block;"></a>
         </td>
         <td style="text-align: center;">
        <!-- <button type="button"  data-toggle="modal" data-target="#exampleModal">
 <img src="images/edit.gif"  style="display:block;">
</button>-->
             <a href="#" data-toggle="modal" data-target="#exampleModal" class="updatechallanpopup"   data-id="<?php echo $result[$i]['id'] ?>" onClick="load_data(<?php echo $result[$i]['id']?>)"><img src="images/edit.gif"  style="display:block;"></a>
         </td>
         <td style="text-align: center;"><a href="view_tds_report.php?lid=<?php echo $result[$i]['LedgerId']?>&gid=1&stDate=<?php echo $result[$i]['from_date'] ?>&endDate=<?php echo $result[$i]['to_date'] ?>" style="text-decoration: none;"><?php echo $result[$i]['id']?></a></td>
         <td style="text-align: center;"><?php if($result[$i]['ChallanNo'] <> ''){echo $result[$i]['ChallanNo']; }else { echo "0";}?></td>
          <td style="text-align: center;"><?php if($result[$i]['BSR_Code'] <> ''){echo $result[$i]['BSR_Code']; }else { echo "---";}?></td>
        <td style="text-align: center;"><?php echo $result[$i]['AssessmentYear']?></td>
		<td style="text-align: center;"><?php echo $result[$i]['Challan_date']?></td>
        
        <td style="text-align: center;"><?php echo $result[$i]['ledger_name']?></td>
        <td style="text-align: center;"><?php echo $result[$i]['NatureOfTDS']?></td>
        <td style="text-align: center;"><?php echo $result[$i]['TotalAmount']?></td>
        <td style="text-align: center;"><?php if($result[$i]['Comment'] <> ''){ echo $result[$i]['Comment']; }else { echo "---";}?></td>
        </tr>
	<?php }
	?>
    
    </table>
</div>


</center>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:400px;padding-top: 80px;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title" id="exampleModalLabel" style="text-align:center;    font-size: 12px;"><b>Update Challan Number</b></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px;margin-right: 14px;background-color: lightslategray;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div style="line-height: 22px; text-align: center;"> <b>Challan Number &nbsp;&nbsp;:</b> &nbsp;&nbsp;<input type="text" id="challan_no" name="challan_no"stylte =" width: 150px;">
      </div>
       <div style="line-height: 22px; text-align: center;margin-left:29px;"> <b>BSR Code &nbsp;&nbsp;:</b> &nbsp;&nbsp;<input type="text" id="BSR_code" name="BSR_code"stylte =" width: 150px;">
      </div>
       <div style="margin-left: 25px;margin-top: 10px; text-align: center;"> <b>Comments &nbsp;&nbsp;:</b> &nbsp;&nbsp;<textarea id="comment" name="comment" rows="4" cols="35" ></textarea>
      </div>
        <?php //echo  "Challan ID" .$challanID ;?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="hidden" id="popUpId" name="popUpId" value="">
        <button type="button " class="btn btn-primary" onClick="SubmitData()">Save changes</button>
      </div>
    </div>
  </div>
</div>
</body>
</html> 
 

 
<?php include_once "includes/foot.php"; ?>

<script>
$('.updatechallanpopup').click(function(){

var id = $(this).attr('data-id');

$("#popUpId").val(id);

});
function load_data(id)
{
	
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: {"method" : 'LoadChallan',"ChallanId": id},
			success : function(data)
			{
				
				var arr = Array();
				arr		= data.split("@@@");
				
				var arr2 =JSON.parse(arr[1]);
				
				document.getElementById('challan_no').value=arr2[0]['ChallanNo'];
				document.getElementById('BSR_code').value=arr2[0]['BSR_Code'];
				document.getElementById('comment').value=arr2[0]['Comment'];
				
			},		
			
		});
}
function SubmitData()
{
	var challanNo= document.getElementById('challan_no').value;
	var BSR_code= document.getElementById('BSR_code').value;
	var ChallanId= document.getElementById('popUpId').value;
	var Comment= document.getElementById('comment').value;
	//alert(ChallanId);
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: {"method" : 'UpdateChallan',"ChallanId": ChallanId, "ChallanNo":challanNo, "BSR_code":BSR_code, "Comment":Comment},
			success : function(data)
			{
				var arr = Array();
				arr		= data.split("@@@");
				if(arr[1]==1)
				{
					alert("Record Added Successfully...");
					
				}
				else
				{
					alert("Record not updated ...");
				}
				location.reload();
			},		
			
		});
}
</script>