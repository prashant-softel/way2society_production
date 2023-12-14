<?php 
include_once("classes/dbconst.class.php");
include_once("classes/service_prd_reg.class.php");
include_once "classes/include/dbop.class.php";

$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_service_prd_reg = new service_prd_reg($m_dbConn,$m_dbConnRoot);

//ftechinf list of service provider
$res = $obj_service_prd_reg->fetchServiceProvider();
?>
 <style>
 .servProviderTable  
 {
    border: 1px solid #cccccc;
  border-collapse:collapse;
}

 .servProviderTable td 
 {
    border: 1px solid #cccccc;
	border-collapse:collapse;
  
}

.servProviderTable th 
 {
    border: 1px solid #cccccc;
	border-collapse:collapse;
  
}
 .servProviderInnerTable   
 {
    border: 1px solid #cccccc; border-collapse:collapse;
}

 .servProviderInnerTable  td 
 {
    border: 1px solid #cccccc;
	border-collapse:collapse;
   
}

/*table {
	border-collapse: collapse;
	border:1px solid #cccccc; 
	
}*/
th, td {
	border-collapse: collapse;
	border:1px solid #cccccc; 
	text-align:left;
}	
tr:hover {background-color: #f5f5f5}

</style>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>

  <?php 
        //include export buttons from template
        include_once( "report_template.php" ); // get the contents, and echo it out.    ?>                 
        <div  id="mainDiv">
        <center>
        <div style="font-size:20px"><b>Service  Provider Details</b></div>
        </center>
  <?php 
  if(sizeof($res)> 0)
		{?>
            <br/> <br/>
            <table  class="servProviderTable  table table-bordered table-hover table-striped" cellspacing="0" width="100%" id="showTable">
            <thead>
            <tr height="30" style="border-collapse: collapse;border:1px solid #cccccc;">
            	 <th >Full Name</th>
                <th style="width:6%;">Working Since</th>
                <th >Age(Yrs)</th>
                <th >Category</th>
                <th width="20%"> Units </th>
                 <th> Family Details </th>
                 <th>Identy Mark</th>
                 <th> Education </th>
                 <th> Marital Status </th>
                 <th> Address</th>
                 <th> Contact No</th>
                  <th>Native Address</th>
                 <th>Native Contact No</th>
            </tr>
              
            </thead>
            <tbody>
            <?php
			foreach($res as $k => $v)
			{
			?>
            <tr height="25"  align="center" style="border-collapse: collapse;border:1px solid #cccccc;">
            	 <td align="left"><?php echo $res[$k]['full_name'];?> </td>
                <td align="right" style="width:6%;"><?php echo getDisplayFormatDate($res[$k]['since']);?></td>
                <td align="right"><?php echo $res[$k]['age'];?></td>
                 <td align="left"> 
                 
                 <?php 
                 //fetching service provider category 
                 $get_reg_cat = $obj_service_prd_reg->get_reg_cat($res[$k]['service_prd_reg_id']);?>
                     
                 </td>
                 <td align="right">
                 
				 	<?php 
                    //fetching list of units and society names in which service provider working
                    $get_reg_units = $obj_service_prd_reg->get_reg_units_societywise($res[$k]['service_prd_reg_id']);
                 	   if(sizeof($get_reg_units) > 0)
                       {?>
                       		<table class="servProviderInnerTable  table table-bordered table-hover table-striped" width="100%" >
								<?php
                                foreach($get_reg_units as $k2 => $v2)
                                {?>
                                    <tr style="border-collapse: collapse;border:1px solid #cccccc;"
><td style="width:60%;min-width:60%;"><?php echo $k2;?></td><td style="width:40%;"><?php echo $v2;?></td></tr>
                     <?php }?>
						   </table>
					  <?php }	
					  else
					  {echo 'Not Mentioned ';}?>
                 </td>
                 <td align="center">
                 <?php if($res[$k]['father_name'] <> ""  ||  $res[$k]['mother_name']<>"" || $res[$k]['hus_wife_name']<>"" ||  $res[$k]['son_dou_name']<>"" || $res[$k]['other_name']<>"")
				 {?>
                	    <center>
                        <table align="center" border="0" style="width:100%;"  class="servProviderInnerTable  table table-bordered table-hover table-striped">
                           <?php if($res[$k]['father_name']<>"")
                            {?>
                            <tr height="25"  style="border-collapse: collapse;border:1px solid #cccccc;">
                                <td align="center">Father</td>
                                <td align="center"><?php if($res[$k]['father_name']<>""){echo stripslashes($res[$k]['father_name']);}else{ echo 'Not Mentioned';}?></td>
                                <td align="center"><?php if($res[$k]['father_occ']<>""){echo stripslashes($res[$k]['father_occ']);}else{ echo 'Not Mentioned';}?></td>
                            </tr>
                            <?php } ?>
                            
                             <?php if($res[$k]['mother_name']<>"")
                            {?>
                            <tr height="25"  style="border-collapse: collapse;border:1px solid #cccccc;">
                                <td align="center">Mother</td>
                                <td align="center"><?php if($res[$k]['mother_name']<>""){echo stripslashes($res[$k]['mother_name']);}else{ echo 'Not Mentioned';}?></td>
                                <td align="center"><?php if($res[$k]['mother_occ']<>""){echo stripslashes($res[$k]['mother_occ']);}else{ echo 'Not Mentioned';}?></td>
                            </tr>
                            <?php }
                             if($res[$k]['hus_wife_name']<>"")
                            {?>
                            <tr height="25"  style="border-collapse: collapse;border:1px solid #cccccc;">
                                <td align="center">Husband / Wife</td>
                                <td align="center"><?php if($res[$k]['hus_wife_name']<>""){echo stripslashes($res[$k]['hus_wife_name']);}else{ echo 'Not Mentioned';}?></td>
                                <td align="center"><?php if($res[$k]['hus_wife_occ']<>""){echo stripslashes($res[$k]['hus_wife_occ']);}else{ echo 'Not Mentioned';}?></td>
                            </tr>
                            <?php  }
                            if($res[$k]['son_dou_name']<>"")
                            {?>
                            <tr height="25"  style="border-collapse: collapse;border:1px solid #cccccc;">
                                <td align="center">Son / Daughter</td>
                                <td align="center"><?php if($res[$k]['son_dou_name']<>""){echo stripslashes($res[$k]['son_dou_name']);}else{ echo 'Not Mentioned';}?></td>
                                <td align="center"><?php if($res[$k]['son_dou_occ']<>""){echo stripslashes($res[$k]['son_dou_occ']);}else{ echo 'Not Mentioned';}?></td>
                            </tr>
                            <?php  }
                            if($res[$k]['other_name']<>"")
                            {?>
                            <tr height="25" style="border-collapse: collapse;border:1px solid #cccccc;">
                                <td align="center">Other</td>
                                <td align="center"><?php if($res[$k]['other_name']<>""){echo stripslashes($res[$k]['other_name']);}else{ echo 'Not Mentioned';}?></td>
                                <td align="center"><?php if($res[$k]['other_occ']<>""){echo stripslashes($res[$k]['other_occ']);}else{ echo 'Not Mentioned';}?></td>
                            </tr>
                            <?php }?>
                            </table>
                            </center>
<?php }
			else
			{  echo 'Not Mentioned';}
?>
                </td>
                 <td><?php echo stripslashes($res[$k]['identy_mark']);?></td> 
                 <td><?php echo stripslashes($res[$k]['education']);?></td>
                 <td><?php echo stripslashes($res[$k]['married']);?></td> 
                 <td><?php echo stripslashes($res[$k]['cur_resd_add']);?></td>
                 <td><?php echo stripslashes($res[$k]['cur_con_1']);?> <?php if($res[$k]['cur_con_2']<>""){echo ' & '.stripslashes($res[$k]['cur_con_2']);}?></td>
                 <td><?php echo stripslashes($res[$k]['native_add']);?></td>
                 <td><?php echo stripslashes($res[$k]['native_con_1']);?> <?php if($res[$k]['native_con_2']<>""){echo ' & '.stripslashes($res[$k]['native_con_2']);}?></td>
                
            </tr>
            <?php
			}
			?>
            
            </tbody>
            </table>
			<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
		}?>
	</div>
   <center>


<script>document.getElementById('btnExportPdf').style.display = "none";</script>

<?php //include_once "includes/foot.php"; ?>