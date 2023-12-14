<?php include_once "ses_set.php"; ?>
<?php
include_once("includes/header.php");

include_once("classes/mem_bike_parking.class.php");
$obj_mem_bike_parking=new mem_bike_parking($m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script language="javascript" type="application/javascript">
	function go_print()
	{
		var id = document.getElementById("service_prd_reg_id").value;
		if(id!="")
		{
			window.open("reg_form_print.php?id="+id,"SPRF","height=400,width=300,top=30,left=490,scrollbars=yes");	
		}
		else
		{
			alert("Please Select Any one");	
		}
		
	}
	</script>
</head>

<body>

<div id="middle">
<center><font color="#43729F" size="+1"><b>Service Provider Form Print Manually</b></font></center>

<br><br>
<table align="center">
    <tr>
        <td valign="top"><?php echo $star;?></td>
        <td>Select Service Provider</td>
        <td>&nbsp; : &nbsp;</td>
        <td>
            <select name="service_prd_reg_id" id="service_prd_reg_id" style="width:140px;">
                <?php echo $combo_member_id=$obj_mem_bike_parking->combobox("select service_prd_reg_id,full_name from service_prd_reg where status='Y'"); ?>
            </select>
        </td>
    </tr>
    
    <tr><td colspan="4">&nbsp;</td></tr>
    
    <tr>
        <td colspan="4" align="center">
        <!--<input type="button" name="print" id="print" value="Print" onClick="go_print();">-->
        <a href="javascript:void(0)"  onClick="go_print();">
        <img src="images/print.png" height="50" width="50" >
        </a>
        </td>
    </tr>
</table>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</body>
</html>
