
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Documents</title>
</head>




<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/doc.class.php");
$obj_document = new document($m_dbConn);
$display_documents = $obj_document->fetchDocuments();
?>

<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
        Documents
    </div>
    <br />    
    <!--<a href="Documents.php" >Add New Document</a> -->
    <button type="button" class="btn btn-primary" onclick="window.location.href='Documents.php'">Add New Document</button>   
	<div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                	<tr height="30">
            			<td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td>
            		</tr>
                    <tr>
                        <th>Document Name</th>
                        <th>Category</th>
                        <th>Note</th>                        
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($display_documents <> '')
				{
					foreach($display_documents as $key=>$val)
					{						
				?>
                       <tr>
                            <td><?php echo $display_documents[$key]['Name'];?></td>
                            <td><?php if($display_documents[$key]['Category'] == 0)
									  {
									   	  echo "Visible To Super Admin/Admin";  
									  }
									  else
									  {
										  echo "Visible To All";
                                      } ?>
                            </td>
                            <td><?php echo $display_documents[$key]['Note'];?></td>                            
                            <td align="center"><?php echo "<a href='http://way2society.com/Uploaded_Documents/".$display_documents[$key]['Document']. "' class='links'>".$display_documents[$key]['Document']."</a>" ?> </td>
                        </tr> 
               <?php 	
				   } 
				} ?>                                        			
                </tbody>
            </table>
        </div>
              
</div>

</div>

<?php include_once "includes/foot.php"; ?>