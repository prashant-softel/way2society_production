<?php error_reporting(1) ?>
<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
include_once("includes/dbop.class.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/backup.class.php");
$dbConnRoot = new dbop(true);
$m_dbConn = new dbop();
$obj_backup = new backup($m_dbConn);
$display_backup=$obj_backup->backup($_SESSION['society_id']);	

?>

<div class="panel panel-info" style="margin-top:4%;margin-left:3.5%; border:none;width:90%">
  
    <div class="panel-heading" style="font-size:20px;text-align: center;">
         Backup Information
      <button id="myBtn" class="btn btn-primary" style="    float: left;" data-toggle="modal" data-target="#myModal">Take backup</button>
    <button type="button" class="btn btn-primary" style="    float: right;" onclick="restoredb()">Restore Old Database</button>
    </div>
    <br />
   
	<div class="panel-body">                        
        <div class="table-responsive">
         
         <br />
          
         <?php //echo $_REQUEST['type']; ?>
               <table id="example" class="display" cellspacing="0" width="100%">
               <thead>
            			<tr  height="30" bgcolor="#CCCCCC">

                        <th>Id</th>
                      
                        <th style="text-align: center;">Description</th>
                        
                        <th> Path</th>
                        <th> Date</th>
                        
                       
                       
					
                    </tr>
                     </thead>
                <tbody>
               
                <?php if($display_backup <> '')
				{
					$i=1;
					foreach($display_backup as $key=>$val)
					{
						
						echo "<tr>";
						echo "<td>".$i."</td>";
						echo "<td>".$val['description']."</td>";
						echo "<td>".$val['path']."</td>";
						echo "<td>".$val['created_at']."</td>";
						echo "</tr>";
						$i++;
                
           			}
				} 
				 ?>                                        			
                </tbody>
            </table>

        </div>
            
</div>
<script type="text/javascript" src="js/bootstrap-modalmanager.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel">Take Backup</h4>

            </div>
            <div class="modal-body"><label>Back Up Description : </label></td>
                  <td></td>
                  <td>
                     <textarea rows="4" required="true" id="description" name="description" cols="50"></textarea>
                  </td>
                  <span id="error"></span>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" retu data-dismiss="modal">Close</button>
                <button type="button" id="savebutton" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$("#savebutton").click(function(){
      var description=$('#description').val();
      if (description.length==0) {
        $('#error').html('<p style="color: red">Please enter disctription</p>');
        return false;
      }
	$.ajax({
			url: 'takebkp.php',
        	type: 'POST',
        	data: {"description": description},
        	success: function(data)
        	{
        		
        			alert("BackUp Created successfully");
        			window.location.reload();
        		
            }
		})
	});

   
	});
   function restoredb() {
      $.ajax({
      url: 'dumpdatabase.php',
          type: 'POST',
          
          success: function(data)
          {
            
              alert("BackUp Restored successfully");
              window.location.reload();
            
            }
    })
  
    }
</script>
<?php include_once "includes/foot.php"; ?>
 