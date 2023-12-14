<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Service Request Report</title>
</head>

<?php
include_once("classes/srreport.class.php");
include_once("includes/head_s.php");    
include_once("classes/include/dbop.class.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);

$obj_srreport = new srreport($dbConn);

$cnt=0;
$var=0;
$societyName = $obj_srreport->GetSocietyName($_SESSION['society_id']);
$sname=$societyName[0]['society_name'];
 if(isset($_REQUEST['rq']))
	{
		$str="";
	if(!empty($_POST['check_list']))
		{
		foreach($_POST['check_list'] as $selected)
		{
			if($selected=="Raised")
			{
				$str.=",'Raised'";
			}
			if($selected=="Re-Open")
			{
				$str.=",'Reopen'";
			}
			if($selected=="Assigned")
			{
				$str.=",'Assigned'";
			}
			if($selected=="In process")
			{
				$str.=",'In process'";
			}
			if($selected=="Resolved")
			{
				$str.=",'Resolved'";
			}
			if($selected=="Waiting for details")
			{
				$str.=",'Waiting for details'";
			}
			if($selected=="Close")
			{
				$str.=",'Closed'";
			}
			
		}
		}
		$str = substr($str, 1);
		$cat=$_POST['cat'];
		$Details=$obj_srreport->getDetails($cat,$str);
		
		if($Details['catid']=="All")
		{
			$var=1;
		} 
}
	
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="lib/js/ajax.js"></script>
	<script type="text/javascript" src="js/srreport.js"></script>
    <script>
	function redirect(element)
	{
		var a=element.value;
		
	window.open("viewrequest.php?rq="+ a ,'_blank');	
	}
	function PrintServiceReport()
	{
			
var originalContents = document.body.innerHTML;
document.getElementById('soc_name').style.display ='block';

var printContents = document.getElementById('Serviceinfo').innerHTML;
document.body.innerHTML = printContents;
window.print();
document.body.innerHTML= originalContents;
	}
	function exportservice()
	{
		
		var myBlob =  new Blob( [$("#Serviceinfo").html()] , {type:'application/vnd.ms-excel'});
		 
		 var url = window.URL.createObjectURL(myBlob);
		
		 var a = document.createElement("a");
		 document.body.appendChild(a);
		 a.href = url;
		 a.download = "ServiceInfo.xls";
		 a.click();
		//adding some delay in removing the dynamically created link solved the problem in FireFox
		 setTimeout(function() {window.URL.revokeObjectURL(url);},0);	
	}
	</script>
   
    <style>
	#report
	{
		background-url:none;
	}
	.report1:hover
	{
		text-decoration:underline
	}
	th {
  cursor: pointer;
}
#servicereporttable tr:nth-child(even) {background-color: #f2f2f2;}
	</style>
</head>
<body>
<div class="panel panel-info" style="margin-top:3%;margin-left:3.5%; border:none;width:95%">
 
    <div class="panel-heading text-center" style="font-size:20px;">
    Service Request Report
    </div>
   <div class="panel-body">   
                   <center>      
        <div class="table-responsive" style="border:1px solid black;width:95%;">
        	 <form name="filter" id="filter" method="post" action="srreport.php?&rq">
			 	<center><table style="width:85%;background-color:transparent; " >
    <tr> <td colspan="8"><br/> </td></tr>
    	<tr>
			<table>
            <tr>
			 <td style="width:31%;font-size: 15px;padding-left:18px;"><label> Category :</label></td>
           <td style="padding-left: 14px;width:30px">
           	<select name="cat" id="cat" style="width:210px;font-size: 15px;" >
             <?php $qry1 = "SELECT `ID`, `category`, `status` FROM `servicerequest_category` WHERE `status`='Y' ORDER BY `servicerequest_category`.`category` ASC";
		echo $obj_srreport->combobox($qry1,0); 
			?>
            </select>
            </td>
            </tr>
            
			</table>
     	</tr>
        <tr><br/></tr>
        <tr>
        <td colspan="7">
        <table>
        <tr>
        	
		<td style="font-size: 15px; font-weight:bold; padding-left:20px;">
         Status:
         </td>
          <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Raised" name="check_list[]" value="Raised" checked="checked" style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Raised</label><br/>
         </td>
        <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Assigned" name="check_list[]" value="Assigned" checked="checked" style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Assigned</label><br/>
         </td>
        <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="In_process" name="check_list[]" value="In process"   checked="checked" style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">In process</label><br/>
         </td>
        <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Resolved" name="check_list[]" value="Resolved"  style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Resolved</label><br/>
         </td>
       <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Waiting_fo_details" name="check_list[]" value="Waiting for details" checked="checked" style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Waiting for details</label><br/>

        <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Re_Open" name="check_list[]" value="Re-Open"  style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Re-Open</label><br/>
         </td>
        <td style="font-size: 12px; font-weight:bold;">
         <input type="checkbox" id="Close" name="check_list[]" value="Close"  style="margin-left: 30px;"><label style="margin-left:10px;margin-top: 2px;">Close</label><br/>
         </td>
       
        </tr>
        
        </table>
        </tr>
        
        <tr><br/></tr>
        <tr>
        
            <td style="padding-right: 15px;font-size: 15px;"><input type="submit" name="submit" value="Submit" class="btn btn-primary" /> </td>
			
        </tr>
    </table></center>
	</form>
<br/><br/>
    </div>
</center>
    <BR><BR>
    
     <?php if(isset($_REQUEST['rq'])){ ?>
    
     <center>
     
      <div id="BtnExport" style="width:50%" >
        <input type="button" class="btn btn-primary" style="float:center" value="Print/Export to PDF" onClick="PrintServiceReport()">&nbsp;&nbsp;&nbsp;
        <input type="button" class="btn btn-primary" style="padding-left:14px" value="Export to Excel" onClick="exportservice()">
        <br><br>
        </div>
        </center>
        
	  <div  id="Serviceinfo">  
       <div id="soc_name" style="display: none">
					<br/>
					<p style="text-align: center;font-size: 30px"><?php echo $sname ?></p>
                           
					<p style="text-align: center;font-size: 20px">Service Request Report</p>
                  
					<p style="text-align: left;font-size: 20px">Category: <?php echo $caname ?></p>
                    		
				
			 </div>  
              <div  id="servicereport">
		<br/>          
                <br>
                <br>
            <table class="table table-responsive condensed"  id="servicereporttable"cellspacing="0" width="100%">
                <thead>              
                   <tr>
                  	 <th style="text-align:center" onclick="sortTable(0)">Request No. </th>
                     <th style="text-align:center" onclick="sortTable(1)">Reported By</th>
                     <th style="text-align:center" onclick="sortTable(2)">E-mail id</th>
                     <th style="text-align:center" onclick="sortTable(3)">Contact No.</th>
                     
                     <th style="text-align:center" onclick="sortTable(4)">Raised Date</th>
                     <th style="text-align:center" onclick="sortTable(5)">Priority</th>
                     <th style="text-align:center" onclick="sortTable(6)">Status</th>
                     <th style="text-align:center" onclick="sortTable(7)">Re-Open Count</th>
                     <?php if($var==1){?>	<th style="text-align:center" onclick="sortTable(8)">Category</th>
                     <?php } ?>
                     </tr>
                     </thead>
                     <?php for($i=0;$i<sizeof($Details);$i++){?>
                     
                     <tr>
                     <td style="text-align:center"><?php echo $Details[$i]['request_no']?></td>
                     <td style="text-align:center"><button style="background-color:none;border:none;color:#337ab7"  onClick="redirect(this)" class="report1" id="report" value="<?php echo $Details[$i]['request_no']?>" ><?php echo $Details[$i]['reportedby']?></button></td>
                     <td style="text-align:center"><?php echo $Details[$i]['email']?></td>
                     <?php if($Details[$i]['phone']=="0"){?>
                     <td style="text-align:center"></td>
                     <?php } else { ?>
                     <td style="text-align:center"><?php echo $Details[$i]['phone']?></td>
                     <?php }?>
                     <td style="text-align:center"><?php echo $Details[$i]['dateofrequest']?></td>
                     <td style="text-align:center"><?php echo $Details[$i]['priority']?></td>
                     <td style="text-align:center"><?php echo $Details[$i]['status']?></td>
                     
                       <?php 
						   $details = $obj_srreport->getViewDetails($Details[$i]['request_no'],true);
								
							for($j =0; $j<sizeof($details);$j++ )
							{
								if($details[$j]['status']=="Reopen")
								{
									$cnt=$cnt+1;
								}
							}
						 ?>
                         
                          <td style="text-align:center"><?php echo $cnt ?></td>
                         
                     
                      <?php if($var==1){
						  $catName1 = $obj_srreport->GetcategoryName($Details[$i]['category']);
							$caname1=$catName1[0]['category'];

						  ?><td style="text-align:center"><?php echo $caname1?></td>
                     <?php } ?>
                     </tr>
                     <?php $cnt=0;
					 }?>
                     </table>
                      <script>
			function sortTable(n)
			 {
				  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				  table = document.getElementById("servicereporttable");
				  switching = true;
				   dir = "asc"; 
				  while (switching) 
				  {
					switching = false;
					rows = table.rows;
					for (i = 1; i < (rows.length - 1); i++) 
					{
					  shouldSwitch = false;
					  x = rows[i].getElementsByTagName("TD")[n];
					  y = rows[i + 1].getElementsByTagName("TD")[n];
					  if (dir == "asc")
					   {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
						  shouldSwitch= true;
						  break;
						}
					  } else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
						  shouldSwitch = true;
						  break;
						}
					  }
					}
					if (shouldSwitch) {
					  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					  switching = true;
					  switchcount ++;      
					} else {
					  if (switchcount == 0 && dir == "asc") {
						dir = "desc";
						switching = true;
					  }
					}
				  }
				}
</script>

                     </div>
	 </div>
     <?php }?>
        </div>
     </div>
 </div>
</body>
<?php include_once "includes/foot.php"; ?>		
   
</html>
