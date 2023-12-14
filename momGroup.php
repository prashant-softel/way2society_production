<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Groups</title>
</head>

<?php include_once "ses_set_s.php"; ?>
<?php
if(isset($_SESSION['admin']))
{
  include_once("includes/header.php");
}
else
{
  include_once("includes/head_s.php");
}
?>
<?php
include_once("classes/momGroup.class.php");
$obj_group = new momGroup($m_dbConn);
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
?>
 

<html>
  <head>
    <title>momGroup</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/momGroup.js"></script>
      <style>
        select.dropdown
        {
          position: relative;
          width: 100px;
          margin: 0 auto;
          padding: 10px 10px 10px 30px;
          appearance:button;
          /* Styles */
          background: #fff;
          border: 1px solid silver;
          /* cursor: pointer;*/
          outline: none;
        }
        @media print
        {    
          .no-print, .no-print *
          {
            display: none !important;
          }
          div.tr, div.td , div.th 
          {
            page-break-inside: avoid;
          }
        }
		#hide
        {
          display: none;
		  /*text-align: center;*/
        }
    </style>
  </head>
  <body>
    <center>
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
          <div class="panel-heading" id="pageheader">Groups</div>
            <?php
              $star = "<font color='#FF0000'>*</font>";
              if(isset($_REQUEST['msg']))
              {
                $msg = "Sorry !!! You can't delete it. ( Dependency )";
              }
              else if(isset($_REQUEST['msg1']))
              {
                $msg = "Deleted Successfully.";
              }
              else{}
            ?>
            <form name="group" id="group" method="post" action="process/momGroup.process.php">
              <br>
              <div style="position:absolute, top: 10px, right:10px"><button type="button" class="btn btn-primary" onClick="window.location.href='createGrp.php?method=create'" style="float:right;margin-right:5%">Create New Group</button></div>
              <!--<center>
                <table width="50%" id="hide">
                    <tr>
                      <td width="25%" style="text-align:right">Group Name</td>
                      <td>&nbsp:&nbsp;</td>
                      <td width="75%"><input type="text" name="groupname" id="groupname" /></td>
                    </tr>
                    <tr><br/><br/></tr>
                    <tr><br/></tr>
                    <tr>
                      <td  style="text-align:right">Group Description</td>
                      <td>&nbsp:&nbsp;</td>
                      <td><textarea name="groupdes" id="groupdes" cols="50" rows="3"></textarea></td>
                    </tr>
                    <tr><br/></tr>
                    <tr><br/></tr>
                    <tr>
                      <td colspan="2" align="right"><input type="hidden" name="id" id="id">
					  <td>             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="insert" id="insert" value="Update" style="background-color:#E8E8E8;"></td>
                    </tr>
                  
              </table>
              </center>-->
              <br>
              <br>
              <br>
            </form>
            <table align="center">
              <tr>
                <td>
                  <?php
                  echo "<br>";
                  echo $str1 = $obj_group->pgnation();
                  //echo "In mom group";
                  ?>          
              </td>
            </tr>
          </table>
        </center>
      </div>
  </body>
</html>
<?php include_once "includes/foot.php"; ?>