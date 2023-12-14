<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title> W2S - Create Group </title>
</head>



<?php if(!isset($_SESSION)) { session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php"); 
$dbConn=new dbop();
include_once("classes/utility.class.php"); 
include_once("classes/createGrp.class.php"); 
$obj_cGrp=new createGrp($dbConn);
$gID= $_REQUEST['groupId'];
?>
	<html>
  		<head>
    	<title>createGrp</title>
    		<link rel="stylesheet" type="text/css" href="css/pagination.css" >
      		<script type="text/javascript" src="js/validate.js"></script>
      		<script type="text/javascript" src="js/populateData.js"></script>
      		<script type="text/javascript" src="js/ajax.js"></script>
      		<script type="text/javascript" src="js/jsCreateGrp.js"></script>
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
      		</style>
            <script type="text/javascript">
            $( document ).ready(function()
			{
    			var gId=document.getElementById("gId").value;
				//alert ("Id:"+gId);
				if(gId!=0)
				{
					$.ajax({
						url : "ajax/createGrp.ajax.php",
						type : "POST",
						datatype: "JSON",
						data : {"method":"Edit","groupId":gId},
						success : function(data)
						{		
							//alert ("Data:"+data);
							var memRes=Array();
							memRes=data.split("#");
							//alert("MemRes:"+memRes[0]);
							var i;
							var id=0;
							var res=JSON.parse(memRes[0]);
							var checks = document.getElementsByClassName('checkBox');
							for(i=0;i<res.length;i++)
							{
								checks.forEach(function(val, index, ar)
								{
									if(ar[index].id==res[i]['MemberId'])
									{
										ar[index].checked=true;
									}
								});
							}
							//alert("G:"+memRes[1]);
							document.getElementsByClassName('checkBox')[0].checked = false;
							var grpRes=JSON.parse(memRes[1]);
							//alert("GrpRes:"+grpRes['Name']);
							document.getElementById("grpname").value=grpRes['Name'];
							document.getElementById("grpdes").value=grpRes['Description'];
							document.getElementById("create").value="Update";
							document.getElementById("pageheader").innerHTML="Edit Group";
						}
					});
				}
			});
            </script>
    	</head>
  		<body>
    	<center>
      		<form id="creategrpform" name="creategrpform"  action="process/createGrp.process.php" method="post" enctype="multipart/form-data">
        	<br>
        	  <div class="panel panel-info" id="panel" style="display:none">
              <div class="panel-heading" id="pageheader">Create Group</div>
              <div style="text-align:right;padding-right: 50px;padding-top: 10px;"></div>
              <div id="right_menu">
              	<center>
                <table style="margin-top:-200px">
				  <strong><div id="show" style="text-align:center" width:"100%" color:"#FF0000"></div></strong>
                  <tr>
                  	<input type="hidden" id="gId" name="gId" value="<?php echo $gID; ?>"/>
                    <td valign="middle" style="color:#FF0000"><?php echo "*";?></td>
                    <td>Group Name</td>
                    <td>&nbsp; : &nbsp;</td>
                    <td><input type="text" name="grpname" id="grpname" ></td>
                  </tr>
                   <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr>
                    <td valign="middle" style="color:#FF0000"></td>
                  <td>Group Description</td>
                    <td>&nbsp; : &nbsp;</td>
                    <td><textarea name="grpdes" id="grpdes" cols="90" rows="5"></textarea></td>
                  </tr>
                    <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr>
                    <td valign="top" style="color:#FF0000" ><?php echo "*";?></td>
                    <td> Select Members</td>
                    <td>&nbsp; : &nbsp;</td>
                    <td>
                      <div class="input-group input-group-unstyled" style="width:355px; ">
                        <input type="text" class="form-control" style="width:355px; height:30px;"  id="searchbox" placeholder="Search Member Name"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
                      </div>
                      <div style="overflow-y:scroll;overflow-x:hidden;width:355px; height:150px; border:solid #CCCCCC 2px;" name="mem_id[]" id="mem_id" >
                        <p id="msgDiv" style="display:none;"></p>
                        <?php 
                            echo $comboUnit=$obj_cGrp->comboboxForMemberSelection("Select CONCAT('M-',mof.`mem_other_family_id`) as mem_other_family_id, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '1' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as mem_other_family_id, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Co-Owner)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner`= '2' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('M-',mof.`mem_other_family_id`) as mem_other_family_id, CONCAT(u.`unit_no`,'-',mof.`other_name`,' (Resident)') as other_name from mem_other_family as mof,member_main as mm, unit as u where mof.`coowner` = '0' and mof.Status = 'Y' and mof.`member_id` = mm.`member_id` and mm.`unit` = u.`unit_id`
union Select CONCAT('T-',t.`tmember_id`) as mem_other_family_id,CONCAT(u.`unit_no`,'-',t.`mem_name`,' (Tenant)') as other_name from tenant_module as tm,tenant_member as t,unit as u where tm.Status = 'Y' and tm.`unit_id` = u.`unit_id` and t.`tenant_id` = tm.`tenant_id`",0,'All','0');
                        ?>
                      </div>
                    </td>
                  </tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr>
                    <td valign="middle" style="color:#FF0000"></td>
                    <td>&nbsp; &nbsp;</td>
                    <td>&nbsp; &nbsp;</td>
                    <td>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                      <input type="button" name="createGrp" id="create" value="Create"  class="btn btn-primary" onClick="FetchMemValue();"/> 
                    </td>
                  </tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
                  <tr><br></tr>
            </table>
            </center>
          </div>
        </div>
      </form>
    </center>
  </body>
</html>

<?php include_once "includes/foot.php"; ?>