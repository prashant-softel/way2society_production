<?php include_once "ses_set_s.php"; ?>
<?php 
$title="W2S - Visitor Approval";
include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/SM_report.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$smConn = new dbop(false,false,true,false);
$smConnRoot = new dbop(false,false,false,true);
$ObjSMReport = new SM_Report($dbConn,$dbConnRoot,$smConn,$smConnRoot);
$VisitorDetails = $ObjSMReport->GetIncommingVisitorForApproval($_REQUEST['id']);
$VisitormMsg = $ObjSMReport->GetApprovalMsg();
//echo "<pre>";
//var_dump($VisitormMsg);
//echo "</pre>";
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsVisitor.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	 //$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			window.location.href='suspend.php';
		}
    //});
	
	</script>

</head>
<br>

<div class="panel panel-info" style="margin-top:4%;margin-left:1%; width:76%">
  <div class="panel-heading" style="font-size:20px;text-align:center;">
    Visitor Approval 
    </div>
    <br/>
    <br />
    <div class="panel-body">                        
    <div class="table-responsive">
    <center>

    <form name="expectedvisitor" id="expectedvisitor" method="post" action="process/visitor.process.php" enctype="multipart/form-data" onSubmit="return val();">
<table width="80%"> 
            	<tr align="center">
            		<td style="width:25%">
                    <?php if(is_file("SecuirityApp/".$ReportName."/".$VisitorDetails[0]['entry_image']))
					{?>
						<img src="<?php echo "SecuirityApp/".$ReportName."/".$VisitorDetails[0]['entry_image'];?>" class="img-rectangle" id="img" alt="img" style="height:60%;">
					<?php }else{ ?>
                    <img src="images/noimage.png" class="img-rectangle" id="img" alt="img" style="height:15%;">
                    <?php }?>
                    </td>
            		<td style="text-align:left">
                    	<table style="width:80%">
            			<tr>
            				<td style="font-size:15px;">Name</td>
                            <td style="font-size:15px;">:</td>
                            <td style="font-size:15px;"><?php echo $VisitorDetails[0]['VName'];?></td>
           		       </tr>
                       <tr>
                      		<td style="font-size:15px;">Contact No</td>
                            <td style="font-size:15px;">:</td>
                            <td style="font-size:15px;"><?php echo $VisitorDetails[0]['Contact'];?></td>
                       </tr>
                       <tr>
                      		<td style="font-size:15px;">Purpose</td>
                            <td style="font-size:15px;">:</td>
                            <td style="font-size:15px;"><?php echo $VisitorDetails[0]['purpose'];?></td>
                       </tr>
                       <?php if($VisitorDetails[0]['company'] <> ''){?>
                       <tr>
                      		<td style="font-size:15px;">Company Name</td>
                            <td style="font-size:15px;">:</td>
                            <td style="font-size:15px;"><?php echo $VisitorDetails[0]['company'];?></td>
                       </tr>
                       <?php
					   }
					    if($VisitorDetails[0]['vehicle'] <> ''){?>
                        <tr>
                      		<td style="font-size:15px;">Vehicle No</td>
                            <td style="font-size:15px;">:</td>
                            <td style="font-size:15px;"><?php echo $VisitorDetails[0]['vehicle'];?></td>
                       </tr>
                       <?php }?>
                        <tr>
                       		<td style="font-size:15px;">Approve With Note</td>
                       		<td style="font-size:15px;">:</td>
                            <td>
                            
                        	<textarea id="note" name="note" rows="5" cols="40">
                            </textarea>
                            </td>
                        </tr>
                       </table>
                     </td>  
                  </tr>  
                 <tr><td><input type="hidden" id="visitorId" name="visitorId" value="<?php echo $_REQUEST['id'] ?>"><br></td></tr>
            </table>
           <table width="100%">
            <tr> 
              <td colspan="10" align="center"><input type="submit" name="insert" id="insert" class="btn btn-primary" value="Approve" style="width: 90px; height: 30px; background-color: #337ab7; color:#FFF"; >
    		
              &nbsp;&nbsp;&nbsp;<input type="submit" name="insert" id="insert" class="btn btn-primary" value="Denite" style="width: 90px; height: 30px; background-color:#e63a14cc; color:#FFF"; ></td>
    		</tr>
           </table> 
            </center>
                            
</form>
</center>
 </div>
 </div>
 </div>
 </body>
 
    <?php include_once "includes/foot.php"; ?>