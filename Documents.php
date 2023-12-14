
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>

<title>W2S - Documents Upload</title>
</head>



<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/doc.class.php");
$objdoc = new document($m_dbConn);
$UnitBlock = $_SESSION["unit_blocked"];
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsevents20190504.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	 //$( document ).ready(function() {
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
      window.location.href='suspend.php';
		}
    //});
	
	</script>
    
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
         Documents
    </div>
    <center>
    <form enctype="multipart/form-data" action="process/document.process.php" method="POST">   
    <div class="panel-body">                        
        <div class="table-responsive">
         <table cellspacing="5">           	            
            <tr>
                <td><?php echo $star;?></td>
                <td><b>Document Type</b></td>
                <td>&nbsp;<b>:<b>&nbsp;</td>
                <td><select name="Document_type" id="Document_type">
                    <?php echo $combo_doc = $objdoc->combobox("select ID, doc_type from document_type",'0');
                    ?>
                  </select>
                </td>
          </tr>
          <tr>
            	<td valign="middle"></td>
        		<th>Document Name</th>
                <td>&nbsp;:&nbsp;</td>
                <td><input type="text" name="doc_name" id="doc_name" /></td>
            </tr>
            <tr>
            	<td valign="middle"></td>
        		<th>Visibility</th>
                <td>&nbsp;:&nbsp;</td>
                <td>
                	<select id="category" name="category">
                    	<option value="0">Visible To Committee Members</option>
                     	<?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      				{?>
                        <option value="1">Visible To All</option>
                        <?php }?>
                    </select>
                </td>
            </tr>
             <tr>
            	<td valign="middle"></td>
        		<th>Note</th>
                <td>&nbsp;:&nbsp;</td>
                <td> <textarea type="text" name="note" id="note" style="width: 200px; height: 40px;"> </textarea></td>
            </tr>
            <tr><td colspan="4"><br /> </td></tr>
           <tr>
           		<td colspan="4" align="left" style="color:#003399; font-size:16px;"><b>Attach your documents</b></td>
           </tr>
           <!-- MAX_FILE_SIZE must precede the file input field -->
           <input type="hidden" name="post_date" value="<?php echo date('d-m-Y')?>" />  
           <tr> 
           		<td valign="middle"></td>
           		<td>Upload Document</td>   
                <td>&nbsp;:&nbsp;</td>       
                <!-- Name of input element determines name in $_FILES array -->
                <td><input name="userfile" type="file" /></td>
           </tr>      
           <tr>
               <td colspan="4"> <input type="submit" value="Upload" align="left" /></td>
           </tr>    
        </table>                                                   
         </form>
         </center>
           
			
		
        </div>
           
</div>

</div>

<?php include_once "includes/foot.php"; ?>