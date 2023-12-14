<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Accounting Notes</title>
</head>
<?php //include_once "ses_set_as.php"; ?>
<?php include_once("includes/head_s.php");
include_once "ses_set_s.php";
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/soc_note1.class.php");
$obj_society_notes = new soc_note1($m_dbConn, $m_dbConnRoot);
$res_display=$obj_society_notes->AccountsNotes();


//$notes = $obj_society_notes->view_notes($_REQUEST['nt']);
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:90%">
 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
        ACCOUNTING NOTES
    </div>
    <br />
    
	
          <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/OpenDocumentViewer.js"></script>
</head>
          <body>
          <center>
          <table align='center' style="width: 96%;border: 1px solid #80808026; padding-left: 10px; padding-right: 10px;padding-top: 10px;
    padding-bottom: 10px;">
          <?php if($res_display <> '')
		  {?>
          <tr align="left" id="">
			<td colspan="6" align="left" style="font-size: 12px;"><?php strip_tags(print_r($res_display)); ?></td>
		</tr>
          <?php  } 
		  else
		  {?>
          	<tr> <td align="center" style="font-size:13px;"><b> Data Not Available</b> </td></tr>
		   <?php }
		  ?>
          </table>
          </center>
          <br>
          <?php if($_SESSION['role'] && $_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN ||$_SESSION['role']==ROLE_MANAGER|| $_SESSION['profile']['soc_note1.php'] == 1))
	      {?>
          <center><button type="button" class="btn btn-primary" onClick="window.location.href='soc_note1.php'">Edit Notes</button></center>
          </body>
          </html>
         <!-- <a href="addnotice.php" >Add New</a>-->
    <?php }?>

</div>

<?php include_once "includes/foot.php"; ?>

