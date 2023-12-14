<?php 
include_once("includes/head_s.php");
include_once("classes/home_s.class.php"); 
include_once("classes/utility.class.php");
include_once "classes/dbconst.class.php";
include_once("classes/Changelog_cp.class.php");

$obj_AdminPanel = new CAdminPanel($m_dbConn);
$obj_Utility = new utility($m_dbConn);
$obj_ChangeLog=new ChangeLogAdmin($m_dbConn,$m_dbConnRoot);

$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance();
$ChangedLogDetails=$obj_ChangeLog->ChangeLog();
$ChangedTableDetails=$obj_ChangeLog->ChangeTable();
$ChangedAllDetails=$obj_ChangeLog->ChangeDis();

if($_SESSION['default_year'] == 0)
{?>
	<script> alert("Please Set Default Value For Current Year.");window.location.href="defaults.php";</script>
<?php	
}
else{
$CurrentYearBeginingDate = $obj_Utility->getCurrentYearBeginingDate($_SESSION['default_year']);
$CurrentYearBeginingDate = getDisplayFormatDate($CurrentYearBeginingDate);
$CurrentYearEndingDate = getDisplayFormatDate($_SESSION['default_year_end_date']);
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Period</title>

</script>
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    
    
    <script type="text/javascript">
	window.onload=function()
	{
		document.getElementById('from_date').value=document.getElementById('hid_from').value;
		document.getElementById('to_date').value=document.getElementById('hid_to').value;
	};
	</script>
        <script type="text/javascript">
		function myFunction()
		{
			var X=document.getElementById('to_date').value;
			console.log(X);
			var newdatestring=X;
			var newdate=newdatestring.split("-").reverse().join("-");
			console.log(newdate);
			var newdate1=new Date('"'+newdate+'"');
			var newdaysPrior=1;
			newdate1.setDate(newdate1.getDate()-newdaysPrior);
			console.log(newdate1);
			var m=newdate1.toISOString().slice(0,10);
			var k=m.split("-").reverse().join("-");
			document.getElementById('from_date').value=k;
		}
		</script>
        <script type="text/javascript">
		function myalertfunction()
		{
			var X=document.getElementById('to_date').value;
			var K=document.getElementById('from_date').value;
			//console.log(X);
			//console.log(K);
			var newX=X.split("-").reverse().join("-");
			var newdate1=new Date(newX);
			var newK=K.split("-").reverse().join("-");
			var newdate2=new Date(newK);
			if(newdate1.getTime()<newdate2.getTime())
			{
				alert("From date cannot be less than to date");
				var newdaysPrior=2;
				newdate1.setDate(newdate1.getDate()-newdaysPrior);
				//console.log(newdate1);
				var m=newdate1.toISOString().slice(0,10);
				var k=m.split("-").reverse().join("-");
				document.getElementById('from_date').value=k;
			}
			else{
				//alert("OK");
				}

			//console.log(newdate1);
			//console.log(newdate2);
			
		}
		</script>
       
    <script type="text/javascript">
	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true ,
			minDate: minStartDate,
          	maxDate: maxEndDate
        })});
    </script>
    
    
   <!--<script type="text/javascript">
    function showTable(){
	
	document.getElementById('table_res').style.visibility = "visible";
}</script>!-->
</head>

<body>

<div id="middle">
<div class="panel panel-info" style="height:100%;">
    <div class="panel-heading" id="pageheader">
		<?php echo "Change Log"; ?>    
	</div>
<center>
<form name="changelog_main" id="changelog_main" method="post">
<div id="show" style="font-weight:bold;color:#FF0000" align="center"></div>
<h4><font  style="width:50px;margin-left:5%" color="#003399">Please select the period</font></h4>
<table style="border:1px solid;margin-left:10%;width:750px;height:50px" border="1">
<tr>
<td>
<table style="margin-left:1%;">

 <tr><td><br /></td></tr>
<tr id="from_date1" >    
<tr>
<input type="hidden" name="hid_from" id="hid_from" readonly style="width:100px;" value="<?php echo $_POST['from_date'];?>" />           
<td style="text-align:right"><b>From </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;</td>  
			<td><input type="text" name="from_date" id="from_date"   class="basics" size="10" readonly onchange="myalertfunction()" style="width:100px;"/></td>
            </td>
            <td></td>
            	<td><b>Changed BY</b>&nbsp;  &nbsp;&nbsp;&nbsp;: &nbsp;</td>
    <td>
   	<select name="changelogby" id="changelogby" >
	   	<option value=""> ALL </option>
        <?php foreach($ChangedLogDetails as $i=>$v)
		{
			$changelby=$ChangedLogDetails[$i]['cby']; ?>
			<option value="<?php echo $changelby; ?>"> <?php echo $changelby; ?> </option>
			
		<?php } ?>
        
    
     </select>
   
            
 </tr></tr>
 <tr><td><br /></td></tr>
 <tr>

          <input type="hidden" name="hid_to" id="hid_to" readonly style="width:100px;" value="<?php echo $_POST['to_date'];?>" />	
			<td id="To" style="text-align:right"><b>To</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : &nbsp;   </td> 
                      

           <td>
			<input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly  style="width:100px;" onchange="myFunction()"/>
				
            </td>
            <td><input type="label" style="visibility:hidden;width:70px"  value="" ></td>
            <td><b>Table Name</b>&nbsp;  &nbsp; &nbsp;&nbsp;&nbsp;: &nbsp;</td>
    <td>
   	<select name="changelogTN" id="changelogTN">
	   	<option value=""> ALL </option>
        <?php  for($i=0;$i<sizeof($ChangedTableDetails);$i++)
		{
			$changelTN=$ChangedTableDetails[$i]['ctb']; ?>
			<option value="<?php echo $changelTN; ?>"> <?php echo $changelTN; ?> </option>
			
		<?php } ?>
     </select>
     </td>

            </tr> 
       		
        <tr><td><br /></td></tr>
        <tr>
        
        <td>
          <input type="submit" name="insert" id="insert" value="Submit"  class="btn btn-primary"  onclick="" style="color:#FFF;  box-shadow:none;border-radius: 5px; width:150px; height:30px;background-color: #337ab7;border-color: #2e6da4;margin-left:170%">
          </td>
        </tr>
        <tr><td><br /></td></tr>
       
       </table>
       </td>
       </tr>
       
       </table>
  <tr><td><br /></td></tr>

  <tr><td><br /></td></tr>
 <?php if(isset($_POST['insert'])){ ?>
 
 <div class="panel-body">                        
        <div class="table-responsive" id="table_res">
<table id="example" class="display" cellspacing="0" width="100%"  border="0">
     <thead>
    <tr>
          <th style="width:10%;"><center>Date</center></th>
          <th style="width:15%;"><center>ChangedBy</center></th>
          <th style="width:15%;"><center>Table Name</center></th>
        <th style="width:35%;"><center>Description</center></th>
        <!--<th style="width:20%;">Photo</th>-->
    </tr>
    </thead>
    <tbody>
     <?php 
	
		foreach($ChangedAllDetails as $k => $v)
		{ ?>
        
        
          <?php
            $Date=$ChangedAllDetails[$k]['cts'];
			$ChangeBy=$ChangedAllDetails[$k]['cnme'];
			$Table=$ChangedAllDetails[$k]['ctb'];
			$Description=$ChangedAllDetails[$k]['cdesc'];?>
			
          <td style="width:10%;"><center><?php echo $Date;  ?></center></td>
          <td style="width:15%;"><center><?php echo $ChangeBy; ?></center></td>
          <td style="width:15%;"><center><?php echo $Table; ?></center></td>
        <td style="width:35%;"><center><?php echo $Description; ?></center></td>
         
        <!--<th style="width:20%;">Photo</th>-->
    </tr>
    <?php } ?> 
 </tbody>
               
            </table>
        </div>
        
   
      </div>
      
      <?php } ?>


    </form>
    
   </center>
</div>
</div>
</body>
</html>



   
   
    
        
           
       
		
			  		
           
      





<?php include_once "includes/foot.php"; ?>