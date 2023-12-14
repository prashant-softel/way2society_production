<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
<title>W2S - checkpost master QR Code List</title>
</head>
<?php
include('phpqrcode/qrlib.php');  
include_once("includes/head_s.php");   
include_once("classes/include/dbop.class.php");
include_once("classes/checkpost_master.class.php");

//*****Making different object to connect different databases
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$m_dbConn= new dbop(false,false,true,false);
$smreport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot,$m_dbConn);
$checkpostdetails = $smreport->selecting($_REQUEST['id']);
//var_dump($checkpostdetails);
?>



<style type="text/css" media="print">
  /*@page { size: landscape; }*/
 
  @media print {
  body * {
	
    visibility: hidden;
  }
  
  .section-to-print, .section-to-print * {
    visibility: visible;
  }

@media print {
html, body {
  /*height:100vh; */
  margin: 0 !important; 
  padding: 0 !important;
  overflow: hidden;
   
}
}
  /* .section-to-print {
    position: absolute;
    left: 0;
    top: 0;
  } */
}
</style>

<script>

	window.onbeforeprint = function(){  		
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");		
		btnPrint.style.visibility = 'hidden';		
	}
	window.onafterprint = function(){		
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");		
  		//Set the print button to 'visible' again 
        btnPrint.style.visibility = 'visible';		
	}
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");		
		btnPrint.style.visibility = 'hidden';	
		document.getElementById("printImg").style.width = "400px";
		document.getElementById("printImg").style.height = "500px";
	
		//Print the page content
        window.print();        
		//Set the print button to 'visible' again 
        btnPrint.style.visibility = 'visible';
		document.getElementById("printImg").style.width = "50%";
		document.getElementById("printImg").style.height = "auto";
		
	}    
</script>

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%;width:80%"> 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
        QR Code List   
    </div>
    <br>
    <table >

<tr>
	<td>
<button type="button" class="btn btn-primary btn-circle"  style="float:left;" id="btnBack" onClick="window.location.href='checkpost_master.php'"><i class="fa  fa-arrow-left"></i></button>
    </td>
</tr>
</table>
<div align="center">
    <INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print" value="Print" width="300" style="width:60px;height:30px; font-size:20px" />
</div>
<br>
<?php
  //echo "test1";
  if($checkpostdetails[0]['qrcodename'] == '') 
  { 
	    $name = $checkpostdetails[0]['qrcode'];
      $path = "QRcodeImg/".$_SESSION['society_id']."/".$_REQUEST['id']."/"; 		
      if(!file_exists($path))
	    {
		      mkdir($path, 0777, true);
      }		
      $file=$path.$name.".png";
      unlink($file);
      $insertQuery1 = "update checkpost_master set `qrcodename`='".$name."', `qrcodepath`='".$file."' where id='".$_REQUEST['id']."' and qrcode='".$checkpostdetails[0]['qrcode']."'";
      $Insert = $smConn->update($insertQuery1);
      QRcode::png($name,$file);   
  } ?>

     <br>
     <center>
        <body>
     <table style="border:1px solid black; width: 50%; height: 100%;" class="section-to-print" id="printImg">    
        <tr>
             <tr>
                <td align="center"><br>
                     <span style="font-size: x-large;"> <b><?php echo $checkpostdetails[0]['checkpost_name'];?></b></span><br>
                     <span style="font-size: x-large;"> <b><?php echo $checkpostdetails[0]['desc'];?></b></span>
                 </td>
             </tr>
             <?php
            if($checkpostdetails[0]['qrcodename'] <> '') 
            { ?>
             <tr>
                 <td align="center">    
                     <img id="img_qr" src='<?php echo $checkpostdetails[0]['qrcodepath']; ?>' width="300">                   
                 </td>
             </tr>
            </tr>
             <?php }               
            else if($checkpostdetails[0]['qrcodename'] == '') 
            {  ?>
         <tr>
                 <td align="center">    
                     <img src='<?php echo $file; ?>' width="300">                   
                 </td>
             </tr>
        </tr>
        <?php } ?>
         </table>
        </body>
</center>
<?php

?>

</div>
<?php include_once "includes/foot.php"; ?>