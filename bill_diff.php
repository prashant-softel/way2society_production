<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Bill Difference</title>
</head>
<?php 
   include_once("includes/head_s.php");
   include_once "check_default.php";
   include_once("classes/dbconst.class.php");
   include_once("classes/bill_diff.class.php");
   $obj_bill_diff= new Bill_Diff($m_dbConn, $m_dbConnRoot);
   ?>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>      
<script type="text/javascript" src="js/ajax_new.js"></script> 
<br>
<center>
   <div class="panel panel-info" id="panel" style="display:none">
      <div class="panel-heading" id="pageheader">Bill Difference</div>
      <form method="GET" action="show_bill_diff.php"  onsubmit="return validateform()" >
         <table style="width:80%;padding-left: 100px;">
            <tbody>
               <tr height="30">
                  <td>
                     <center><font color="red" style="size:11px;"><b id="error"></b></font></center>
                  </td>
               </tr>
               <tr>
                  <td>
                     <!-- First table of left side-->
                     <table style="width:47%; float:left;">
                        <tbody>
                           <tr height="30">
                              <td>Bill Year <font color="#FF0000">*</font></td>
                              <td>
                                 <select name="year_id_1" id="year_id_1" required onChange="get_period_1(this.value );">
                                 <?php
                                    echo $combo_year = $obj_bill_diff->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year, "Please Select"); 
                                    ?>    
                                 </select>
                              </td>
                           </tr>
                           <tr  height="30">
                              <td>Bill For<font color="#FF0000">*</font></td>
                              <td>
                                 <select name="period_id_1" id="period_id_1" required >
                                 </select>
                              </td>
                           </tr>
                           <tr  height="30">
                              <td>Bill Type  <font color="#FF0000">*</font></td>
                              <td>
                                 <select name="bill_type_1" id="bill_type_1" required >
                                    <option value="">select please</option>
                                    <OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Regular Bill</OPTION>
                                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>">Supplementary Bill</OPTION>
                                 </select>
                              </td>
                           </tr>
                           <tr  height="30">
                              <td>Skip Interest On Principle Due<font color="#FF0000">*</font></td>
                              <td>
                                 <input type="checkbox" name="skip_interest">
                              </td>
                           </tr>
                            <tr  height="30">
                              <td>Show Only difference<font color="#FF0000">*</font></td>
                              <td>
                                 <input type="checkbox" checked name="only_diff">
                                 <?php $only_diff=1?>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <!-- Second  table of right side-->
                     <table style="width:48%;float:left;">
                        <tbody>
                           <tr height="30">
                              <td>Bill Year <font color="#FF0000">*</font></td>
                              <td>
                                 <select name="year_id_2" id="year_id_2" required onChange="get_period_2(this.value);">
                                 <?php
                                    echo $combo_year = $obj_bill_diff->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year, "Please Select"); 
                                    ?> 
                                 </select>
                              </td>
                           </tr>
                           <tr  height="30">
                              <td>Bill For<font color="#FF0000">*</font></td>
                              <td>
                                 <select name="period_id_2" id="period_id_2" required>
                                 </select>
                              </td>
                           </tr>
                           <tr  height="30">
                              <td>Bill Type  <font color="#FF0000">*</font></td>
                              <td>
                                 <select name="bill_type_2" id="bill_type_2" required  >
                                    <option value="">select please</option>
                                    <OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>">Regular Bill</OPTION>
                                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>">Supplementary Bill</OPTION>
                                 </select>
                              </td>
                           </tr>
                            <tr  height="30">
                              <td>Skip GST<font color="#FF0000">*</font></td>
                              <td>
                                 <input type="checkbox" name="skip_gst">
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>

            </tbody>
         </table>
         <input type="submit" class="btn btn-primary" name="submit" id="submit" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;">
      </form>
   </div>
</center>
<?php include_once "includes/foot.php"; ?>
<script type="text/javascript">
   function get_period_1(year_id)
   {
      
         
      if(year_id == null || year_id.length == 0)
      {
         populateDDListAndTrigger('select#period_id_1', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id_1').value,'period');
      }
      else
      {
         populateDDListAndTrigger('select#period_id_1', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period');
      }
   
   }
   
   function get_period_2(year_id)
   {
      
   
         
      if(year_id == null || year_id.length == 0)
      {
         populateDDListAndTrigger('select#period_id_2', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id_2').value,'period');
      }
      else
      {
         populateDDListAndTrigger('select#period_id_2', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period');
      }
   
   }
</script>
<script type="text/javascript">
    function validateform()
   {
    var bill_type_1=$('#bill_type_1').val();
    var bill_type_2=$('#bill_type_2').val();
    
    if(bill_type_1!=bill_type_2)
    {
      $('#error').text('Both Bill Type Must Be Same');
      return false;

    }

   }
</script>