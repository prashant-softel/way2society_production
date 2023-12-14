<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Create Meeting</title>
</head>


<?php if(!isset($_SESSION)) { session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php"); 
$dbConn=new dbop();
include_once("classes/utility.class.php"); 
include_once("classes/createMeeting.class.php");
include_once("classes/createMinutes.class.php"); 
$obj_utility=new utility($dbConn);
$obj_cMeeting=new createMeeting($dbConn);
$obj_cMinutes=new createMinutes($dbConn);
$res=$obj_cMeeting->SelectgrpName();
$mId=$_REQUEST['mId'];	
$socId=$_SESSION['society_id'];
$socName=$obj_cMinutes->getSocietyName($socId);
$selectRes=$obj_cMeeting->selectComboboxForTemplate();
/*echo "<pre>";
print_r($selectRes);
echo "</pre>";*/
//echo "Society Name:".$socName;

//echo "mId:".$mId;					
?>
<html>
  <head>
    <title>createMeeting</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/CreateMeeting.js"></script>
      <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
      <script type="text/javascript" src="js/jsevents20190504.js"></script>
      <script type="text/javascript">
       $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker
			({ 
            	dateFormat: "dd-mm-yy", 
            	showOn: "both", 
            	buttonImage: "images/calendar.gif", 
            	buttonImageOnly: true, 
				yearRange: '-10:+10', // Range of years to display in drop-down,
        	})
		});
     $( document ).ready(function()
		{
    	var mId=document.getElementById("mId").value;
			//alert ("Id:"+mId);
			if(mId!=0)
			{
				$.ajax
				({
					url : "ajax/createMeeting.ajax.php",
					type : "POST",
					datatype: "JSON",
					data : {"method":"Edit","mId":mId},
					success : function(data)
					{		
						//alert ("Data:"+data);	
						var meetingRes=Array();
						allData=data.split('#');
						meetingRes=JSON.parse(allData[0]);
						//alert("MeetingRes:"+meetingRes['Title']);
						var i;
						var id=0;
						document.getElementById("title").value=meetingRes['Title'];
						document.getElementById("mdate").value=meetingRes['MeetingDate'];
						var time    = new Array();	
						time = meetingRes['MeetingTime'].split(":");
						var str= time[1];
						var mn =str.substr(0,2);
						var ampm= str.substr(2,2);
						//document.getElementById("agenda").style.display="table";
						document.getElementById("hide").style.display="table-row";
						document.getElementById("hide1").style.display="table-row";
						document.getElementById("hide2").style.display="table-row";
						document.getElementById("hide3").style.display="table-row";
						document.getElementById("hide4").style.display="table-row";
						document.getElementById("next").style.display="none";
						document.getElementById("create").value="Update";
						document.getElementById("pageheader").innerHTML="Update Meeting";
						document.getElementById('hr').value=time[0];
						document.getElementById('mn').value=mn;
						document.getElementById('ampm').value=ampm;
						document.getElementById('venue').value=meetingRes['Venue'];
						document.getElementById('mlastdate').value=meetingRes['LastMeetingDate'];
						document.getElementById("mId").value=mId;
						document.getElementById("notes").value=meetingRes['Notes'];
						document.getElementById("endText").value=meetingRes['EndText'];
						var meetingAgenda=JSON.parse(allData[1]);
						var len=meetingAgenda.length;
						document.getElementById("maxrows").value = len;
						document.getElementById("maxlength").value = len;
						if(len == 5)
						{
							document.getElementById("temp_type").value = "3";
						}
						else if(len == 6)
						{
							document.getElementById("temp_type").value = "4";
						}
						else if(len == 7)
						{
							//var agenda2 = meetingAgenda[1]['Question'];
							//agenda2.substring()
							document.getElementById("temp_type").value = "1";
						}
						document.getElementById("temp_type").disabled = "true";
						document.getElementById("refreshBtn").style.display = "table-cell";
						document.getElementById("tdVenue").style.width = "50%";
						document.getElementById("tdVenue").style.paddingLeft ="4%";
						document.getElementById("aId0").value=meetingAgenda[0]['Id'];
						document.getElementById("srNo0").value=1;
						document.getElementById("agenda0").value=meetingAgenda[0]['Question'];
						//alert ("id"+meetingAgenda[0]['Id']);
						for(i=1;i<len;i++)
						{
							sContent="<tr style='padding-top:1%;padding-bottom:10%' id='agendaRow"+i+"'><td width='30%' style='text-align:left;padding-left:15%'><b><font style='color:#F00'>*</font>Agenda:</b></td>";
   							sContent+="<td width='5%'><input type='text' id='srNo"+i+"' name='srNo"+i+"' placeholder='Sr. No.' style='width:40px;vertical-align:top'/><br><br><br><br><a id='del' onClick='deleteRow("+i+")' style='width:70%;height:120%;'><img src='images/del.gif' alt='Delete' style='width:70%;height:120%;padding-left:30%'></img></a><input type='hidden' id='aId"+i+"' name='aId"+i+"' value='"+meetingAgenda[i]['Id']+"'/></td>";   
							sContent+="<td width='65%'><textarea id='agenda"+i+"' name='agenda"+i+"' cols='90' rows='6' placeholder='Enter the meeting agenda here' style='margin-left:1%'>"+meetingAgenda[i]['Question']+"</textarea></td></tr>";
							$("#agenda > tbody").append(sContent);
								scontent=" ";
						}
						for(i=0;i<len;i++)
						{
							if(i!=id)
							{
								document.getElementById("srNo"+i).value = i+1;
								document.getElementById("srNo"+i).readOnly = true;
							}
							else
							{
								continue;
							}
						}
						var meetingAtt=JSON.parse(allData[2]);
						var memAttLen = meetingAtt.length;
						var selectedMem = "";
						var m = 0;
						var gId=meetingRes['GroupId'];
						document.getElementById("grpname").value=gId;
						
						$.ajax
						({
							url : "ajax/createMeeting.ajax.php",
							type : "POST",
							datatype: "JSON",
							data : {"method":"FetchSelectedMembers","gId":gId,"mId":mId},
							success : function(data2)
							{
								//alert ("FetchData:"+data2);
								document.getElementById("selectedMember").style.display = "table";
								document.getElementById("selectedMemId").innerHTML = data2;
							}
						})
						document.getElementById("members1").style.display="table-cell";
						document.getElementById("members2").style.display="table-cell";
						document.getElementById("members3").style.display="table-cell";
						$.ajax
						({
							url : "ajax/createMeeting.ajax.php",
							type : "POST",
							datatype: "JSON",
							data : {"method":"Fetch","gId":gId},
							success : function(data1)
							{	
								//alert ("FetchData:"+data1);
								document.getElementById("mem_id").innerHTML=data1;
								var checks = document.getElementsByClassName('checkBox');
								for(i=0;i<meetingAtt.length;i++)
								{
									checks.forEach(function(val, index, ar)
									{
										if(ar[index].id == meetingAtt[i]['MemberId'])
										{
											ar[index].checked=true;
										}
									});
								}
								document.getElementsByClassName('checkBox')[0].checked = false;
							}
						});
					}
				});
			}
		});
		function validateForm()
		{
			document.getElementById("refreshBtn").style.display = "table-cell";
			document.getElementById("tdVenue").style.width = "50%";
			document.getElementById("tdVenue").style.paddingLeft ="4%";
			var meetingTitle = document.forms["createMeeting"]["title"].value;
			var meetingDate = document.forms["createMeeting"]["mdate"].value;
			var lastMeetingDate = document.forms["createMeeting"]["mlastdate"].value;
			var templateType = document.forms["createMeeting"]["temp_type"].value;
			var venue = document.forms["createMeeting"]["venue"].value;
			var result = true;
			if ( meetingTitle == "")
			{
				alert ("Meeting Title must be filled out..");
				result = false;
			}
			if( meetingDate == "")
			{
				alert ("Meeting Date must be filled out.")
				result = false;
			}
			if( lastMeetingDate == "")
			{
				alert ("Last Meeting Date must be filled out.");
				result = false;
			}
			if( templateType == "0" )
			{
				alert ("Please Select Template for meeting.");
				result = false;
			}
			if( venue == "")
			{
				alert ("Venue must be filled out.");
				result = false;
			}
			if( result )
			{
				return viewNote();
			}
		}
	</script>
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
        @media print and (width: 21cm) and (height: 29.7cm)
      	{    
        	.no-print, .no-print *
        	{
          		display: none !important;
        	}
        	div.tr, div.td , div.th 
        	{
          		page-break-inside: avoid;
			}
     @page {
        margin: 3cm;
    	 }
      	}
	  #members1
	  {
		  display: none;
	  }
	  #members2
	  {
		  display: none;
	  }
	  #members3
	  {
		  display: none;
	  }
	  #hide
	  {
		  display:none;
	  } 
	  #hide1
	  {
		  display:none;
	  } 
	  #hide2
	  {
		  display:none;
	  }
	  #hide3
	  {
		  display:none;
	  }
	  #hide4
	  {
		  display:none;
	  }
	  #cbHeader
	  {
		  display:none;
	  }
      </style>
    </head>
  <body>
    <center>
      <form id="createMeeting" name="createMeeting" action="#" method="post" enctype="multipart/form-data">
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
              <div class="panel-heading" id="pageheader">Create Meeting</div>
              <div style="text-align:right;padding-right: 50px;padding-top: 10px;"></div>
              <input type="hidden" id="maxrows" name="maxrows"/>
              <input type="hidden" id="maxlength" name="maxlength"/>
              <input type="hidden" id="mId" name="mId" value="<?php echo $mId; ?>"/>
                <table style="width:100%">
                	<!--<tr>
                    	<td>
                        	<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa fa-arrow-left"></i>
                        </td>
                        <td>
                        </td>
                    </tr>-->
                	<tr>
                    	<td style="width:75%">
                        		<table style="width:100%">
                   					<tr>
                                    	<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa fa-arrow-left"></i>
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">*</font>Meeting Title:</td>
                    					 <td width="70%" style="padding-right:10%;padding-top:1%"><input type="text" name="title" id="title"/></td>  
                  					</tr> 
                                    <tr>
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">*</font>Meeting Date:</td>
                    					 <td width="70%" style="padding-top:1%"><input type="text" name="mdate" id="mdate" class="basics" /></td>  
                  					</tr>
                                     <tr>
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">*</font>Last Meeting Date:</td>
                    					 <td width="70%" style="padding-top:1%"><input type="text" name="mlastdate" id="mlastdate" class="basics" /></td>  
                  					</tr>  
                                    <tr>
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">*</font>Meeting Time:</td>
                    					 <td width="70%" style="padding-top:1%"><select name="hr" id="hr" style="width:50px;">
												<option value="10">10</option>
													<?php 
													for($i=1;$i<=12;$i++)
													{
														if(strlen($i)==1)
														{
															echo "<option value=0".$i.">0".$i."</option>";
														}
														else
														{	
															echo "<option value=".$i.">".$i."</option>";
														}
													}	
													?>
											</select>
           									<select name="mn" id="mn" style="width:50px;">
												<option value="00">00</option>
													<?php 
													for($ii=0;$ii<=59;$ii++)
													{
														if(strlen($ii)==1)
														{
															echo "<option value=0".$ii.">0".$ii."</option>";
														}
														else
														{
															echo "<option value=".$ii.">".$ii."</option>";
														}
													}
													?>
											</select>
            								<select name="ampm" id="ampm" style="width:50px;">
												<option value="AM">AM</option>
												<option value="PM">PM</option>
											</select>
                                          </td>  
                  					</tr> 
                                    <tr style="padding-top:1%">
                                    	<td colspan="2">
                                    		<table width="50%" style="margin-left:15%">
                                        		<tr>
                                    	 			<td width="30%" style="text-align:left;padding-top:1%"><b><font style="color:#F00">*</font>Venue:</td>
                    					 			<td width="70%" style="padding-top:1%" id="tdVenue"><input type="text" name="venue" id="venue"/></td>
                                                	<td id = "refreshBtn" width="20%" style="padding-left:5%;padding-top:1%;display:none"><a onClick="refreshNotes()"><img src="images/refresh.png" border='0' alt="Refresh" style="cursor:pointer;height:auto;width:60%" id="refresh" /></a></td>
                                           		</tr>
                                        	</table>
                                    	</td>  
                  					</tr> 
                                    <tr style="padding-top:1%"> 
        								<td width="30%" style="text-align:left;padding-left:15%"><b><font style="color:#F00">*</font>Select Template:</td>
            							<td width="70%">
                                        	<select id="temp_type" name="temp_type" onChange="changeMaxRow(this)">
			    								<?php for($i=0;$i<sizeof($selectRes);$i++) 
												{
												?>
                                                 <option value="<?php echo $i ?>"><?php echo $selectRes[$i]?></option>
                                                 <?php
												}
												 ?>
                                            </select>
            							</td>
        							</tr>
                                    <tr id="next">
                                    	<td colspan="2" style="padding-left:35%;padding-top:1%"><input type="button" id="save" name="save" class="btn btn-primary" value="Next" style="margin-left:7%" onClick="validateForm();"/></td>
                                    </tr>
                                    <tr id="hide">
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">*</font>Notes:</td>
                    					 <td width="70%" style="padding-top:1%"><textarea id="notes" name="notes" cols='100' rows='6'></textarea><input type="hidden" name="sName" id="sName" value="<?php echo $socName ?>" />
										</td>  
                  					</tr> 
                                    <tr id="hide1">
                                    	<td colspan="2">
                                    		<table style="width:100%" id="agenda">
                                            	<tr style="padding-top:1%;padding-bottom:10%" id="agendaRow0">
                                    	 			<td width="30%" style="text-align:left;padding-left:15%"><b><font style="color:#F00">*</font>Agenda:</b></td>
                                                    <td width="5%"><input type="text" id="srNo0" name="srNo0" placeholder="Sr. No." style="width:40px;vertical-align:top" value="1" readonly/><br><br><br><br><a id="del0" onClick="deleteRow(0)" style="width:70%;height:120%;"><img src="images/del.gif" alt="Delete" style="width:70%;height:120%;padding-left:30%"></img></a><input type="hidden" id="aId0" name="aId0"/></td>
                    					 			<td width="65%"><textarea id="agenda0" name="agenda0" cols='90' rows='6' placeholder="Enter the meeting agenda here" style="margin-left:1%"></textarea></td>
                                       			</tr>
                                       		</table> 
                                        </td> 
                  					</tr> 
                                   <tr id="hide2">
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00"></td>
                    					 <td width="70%"  style="padding-top:1%;padding-left:7%"><input type="button" name="addAgenda" id="addAgenda" value="Add More Agenda" onClick="addNewRow()" style="background-color:#FFFFCC; color:#000000; text-shadow:#000"/> </td>  
                  					</tr>
                                    <tr id="hide3">
                                    	 <td width="30%" style="text-align:left;padding-left:15%;padding-top:1%"><b><font style="color:#F00">&nbsp;</font>End Text:</td>
                    					 <td width="70%" style="padding-top:1%"><textarea id="endText" name="endText" cols='100' rows='6'>
                                         </textarea> </td>  
                  					</tr>	
                                   	<tr id="hide4">
                                    	<td width="30%" style="padding-left:15%;padding-top:1%"><b><font style="color:#F00"></td>
                    					 <td width="70%" style="padding-top:1%;text-align:center"><input type="button" name="create" id="create" value="Create" class="btn btn-primary" onClick="FetchMeetingDetails()"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="preview" id="preview" value="Agenda Preview" class="btn btn-primary" onClick="PreviewDetails()" style="display:none" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="finalRow" name="finalRow" value="Send As Notice" class="btn btn-primary" onClick="goto_notice()" style="display:none"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="print" id="print" value="Print" class="btn btn-primary" onClick="for_print()" style="display:none" >
                                         <input type="button" name="gotoList" id="gotoList" value="Meeting List" class="btn btn-primary" onClick="history.go(-1);"  style="display:none" >
                       	</td>
                    </tr></td> 
                                    </tr>
                        	</table>
                        </td>
                        <td  style="width:25%">   	
                            <table  style="width:100%">
                                <tr>
                                 	<td  style="text-align:left;padding-top:1%"><b>Invitees:</b></td>
                                </tr>
                               	<tr>
                                  	<td id="lblGrpName" style="text-align:left;padding-top:1%"><b><font style="color:#F00">*</font>Select Group Name:</b></td>
                                </tr>
                                <tr>
                                   	<td  style="text-align:left;padding-top:1%">
                                       	<p id="msgDiv" style="display:none;"></p>
                        					<select name="grpname" id="grpname" onChange="view(this.value)">
                                            <option value="">Select the Group</option>
                                            <option value ="AO">All Owners</option>
                                            <option value ="ACO">All Co-Owners</option>
                                            <option value="AOCO">All Owners & Co-Owners</option>
                                            <option value="AR">All Residents</option>                                            
                                            <!--<option value="ART">All Residents including Tenants</option>-->
											<option value="ACM">All Committee Members</option>
                                            <option value="AVO">All Car Owners</option>
                                            <!--<option value="ALH">All Lien Holders</option>-->
                                            <option value="AT">All Tenants</option>
                        						<?php 
												for($i=0;$i<sizeof($res);$i++)
												{
												?>
                        							<option value="<?php echo $res[$i]['Id'] ?>"><?php echo $res[$i]['Name']?></option>
                            					<?php   
                            					}
												?>
                        					</select>
                                    </td>
                               	</tr>
                                <tr>
                                  	<td style="text-align:left;padding-top:1%" id="members1"><b><font style="color:#F00">*</font>Select Members:</b></td>
                                </tr>
                                <tr>
                                	<td style="text-align:left;padding-top:1%" id="members2">
                                   		<div class="input-group input-group-unstyled" style="width:250px; ">
                        					<input type="text" class="form-control" style="width:250px; height:30px;"  id="searchbox" placeholder="Search Member Name"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
                      					</div>
                                	</td>
                               	</tr>
                                <tr>
                                	<td style="text-align:left;padding-top:1%" id="members3">
                                    	<div style="overflow-y:scroll;overflow-x:hidden;width:250px; height:250px; border:solid #CCCCCC 2px;" name="mem_id[]" id="mem_id">
                        					<p id="msgDiv" style="display:none;"></p>
                                                <?php
                                                 	//echo $obj_cMeeting->comboboxForMemberSelection("SELECT G.MemberId, M.other_name FROM membergroup_members AS G Inner join mem_other_family AS M ON G.MemberId=M.mem_other_family_id WHERE G.Status='Y'",0,'All',0);
												 ?>
                      					</div>
                                    </td>
                             	</tr>
                            </table>
                            <br>
                            <table  style="width:100%;display:none" id="selectedMember">
                                <tr>
                                  	<td style="text-align:left;padding-top:1%"><b>Selected Members:</b></td>
                                </tr>
                                <tr>
                                	<td style="text-align:left;padding-top:1%" id="member">
                                   		<div class="input-group input-group-unstyled" style="width:inherit; ">
                        					<input type="text" class="form-control" style="width:250px; height:30px;"  id="searchbox2" placeholder="Search Member Name"   onChange="SearchElement();"  onKeyUp="SearchElement();" />
                      					</div>
                                	</td>
                               	</tr>
                                <tr>
                                	<td style="text-align:left;padding-top:1%" id="member">
                                    	<div style="overflow-y:scroll;overflow-x:hidden;width:250px; height:250px; border:solid #CCCCCC 2px;" name="selectedMemId[]" id="selectedMemId">
                        					<p id="msgDiv2" style="display:none;"></p>
                                                <?php
                                                 	//echo $obj_cMeeting->comboboxForMemberSelection("SELECT G.MemberId, M.other_name FROM membergroup_members AS G Inner join mem_other_family AS M ON G.MemberId=M.mem_other_family_id WHERE G.Status='Y'",0,'All',0);
												 ?>
                      					</div>
                                    </td>
                             	</tr>
                            </table>
                      	</td>  	
                     </tr>
				</table>
                <div style="text-align:center" id="cbHeader"><br><br><input type="checkbox" id="socHeader" name="socHeader" onChange="addHeader()"><b>Need Society Header?</b><br></div>
                <table width="100%" style="text-align:center">
            		<tr id="preview_table" style="display:none;">
        				<td width="100%" style="text-align:center;padding-left:20%"><br><textarea name="events_desc" id="events_desc" rows="5" cols="50" style="text-align:center; margin-left:60%;"></textarea>
                        </td>
                   </tr>
                   <script>
						CKEDITOR.config.extraPlugins = 'justify';
						CKEDITOR.replace('events_desc', {toolbar: 
						[
         					{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        					{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
							{ name: 'insert', items: [ 'Table' ] },
							{ name: 'insert_2', items: [ 'PageBreak' ] }
   						],
						height: 350,
        				width: "80%",
						uiColor: '#14B8C4'
						});
					</script>
              </table>
              <div id="for_printing" style="display:none"></div>
          </div>
        </div>
      </form>
    </center>
  </body>
</html>
<?php include_once "includes/foot.php"; ?>