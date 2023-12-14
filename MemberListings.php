<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Member Directory</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } 
  include_once("includes/head_s.php");
  //include_once("classes/home_s.class.php");
  include_once("classes/dbconst.class.php");
  include_once("classes/directory.class.php");

  $objDirectory = new mDirectory($m_dbConn);
?>
    
<div class="panel panel-info" id="panel" style="margin-top:3.5%;margin-left:3.5%; border:none;width:70%">
  <div class="panel-heading" id="pageheader" style="font-size:20px">Member Directory</div>
    <!--<div class="panel-body">-->
      <center>
        <table align="center" border="0" width="100%">
          <tr>
          	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
          </tr>
          <tr>
            <td>
              <?php
                echo "<br>";
                echo $str1 = $objDirectory->MemberDirectory();
              ?>
            </td>
          </tr>
        </table>
      </center>
    </div>
  <!--</div>-->
</div>

<?php include_once "includes/foot.php"; ?>