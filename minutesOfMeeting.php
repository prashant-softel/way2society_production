<?php if(!isset($_SESSION)) { session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");  
$dbConn=new dbop();
include_once("classes/utility.class.php"); 
include_once("classes/createMeeting.class.php"); 
$obj_utility=new utility($dbConn);
$obj_cMeeting=new createMeeting($dbConn);
$res=$obj_cMeeting->SelectgrpName();
$mId=$_REQUEST['mId'];	
$GroupId = $obj_cMeeting->getMeetingGroupId($mId);
$dataForProposedByAndSecondedBy = ""; 
$memberDetails = $obj_cMeeting->getMeetingAttendees($GroupId,$mId);
if($memberDetails > 0)
{
	$dataForProposedByAndSecondedBy = "<option value = '0'>&nbsp;Please Select</option>";
	for($i = 0;$i < sizeof( $memberDetails );$i++)
	{
		$dataForProposedByAndSecondedBy .= "<option value = ".$memberDetails[$i]['MemberId'].">&nbsp ".$memberDetails[$i]['other_name']."</option>";
	}
}
/*if($GroupId == "AO")
{
	$sql = "Select ma.MemberId, mm.primary_owner_name as other_name from meetingattendance ma,member_main mm where ma.MeetingId = '".$mId."' and mm.member_id = ma.MemberId";
}
else if($GroupId == "ACO")
{
	$sql = "Select ma.MemberId, mof.other_name from meetingattendance ma, mem_other_family mof where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId";
}
else if($GroupId == "AOCO")
{
	$sql = "Select ma.MemberId, mm.primary_owner_name as other_name from meetingattendance ma,member_main mm where ma.MeetingId = '".$mId."' and mm.member_id = ma.MemberId union Select ma.MemberId, mof.other_name from meetingattendance ma, mem_other_family mof where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId";
}
else if($GroupId == "AR")
{
	$sql = "Select ma.MemberId, mof.other_name from meetingattendance ma, mem_other_family mof where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId";
}
else if($GroupId == "ART")
{
	$sql = "Select ma.MemberId, mof.other_name as other_name from meetingattendance ma, mem_other_family mof where ma.MeetingId = '".$mId."' and mof.mem_other_family_id = ma.MemberId union Select ma.MemberId, mt.mem_name as other_name from meetingattendance ma, tenant_member mt where ma.MeetingId = '".$mId."' and mt.tmember_id = ma.MemberId";
}
else if($GroupId == "ACM")
{
	$sql = "Select ma.MemberId, mof.other_name from meetingattendance ma, mem_other_family mof, commitee C where ma.MeetingId = '5' and mof.mem_other_family_id = C.member_id and mof.mem_other_family_id = ma.MemberId";
}
else if($GroupId == "AT")
{
	$sql = "Select ma.MemberId, tm.tenant_name as other_name from meetingattendance ma, tenant_module tm where ma.MeetingId = '".$mId."' and ma.MemberId = tm.tenant_id and tm.status = 'Y'";
}
else if($GroupId == "AOV")
{
	$sql = "Select ma.MemberId, mof.other_name as other_name from meetingattendance ma, mem_car_parking mcp, mem_other_family mof where ma.MeetingId = '".$mId."' and m.mem_other_family_id = mcp.member_id and ma.MemberId = mof.mem_other_family_id";
}
else if($GroupId == "ALH")
{
	$sql = "Select ma.MemberId, mm.owner_name as other_name from meetingattendance ma, mortgage_details as L, member_main as mm where ma.MeetingId = '".$mId."' and and mm.member_id = L.member_id and mm.member_id = ma.MemberId";
}
else
{
	$sql = "SELECT ma.`MemberId`, mf.`other_name` FROM `meetingattendance` ma, `mem_other_family` mf where ma.`MeetingId` = '".$mId."' AND mf.`mem_other_family_id` = ma.`MemberId`";
}*/
//echo "sql:".$sql;	
		
?>
<html>
  <head>
    <title>MinutesOfMeeting</title>
    <link rel="stylesheet" type="text/css" href="css/pagination.css" >
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/populateData.js"></script>
      <script type="text/javascript" src="js/ajax.js"></script>
      <script type="text/javascript" src="js/MinutesOfMeeting.js"></script>
	  <script type="text/javascript" src="js/jsevents20190504.js"></script>
      <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
      <script type="text/javascript">
      	function collapsable(id) 
		{
			//alert ("Id:"+id);
			var len= id.length;
			//alert ("len:"+len);
			var i=id.substr(6,len-6);
    		if (document.getElementById("hide1A"+i).style.display === "none") 
			{
        			document.getElementById("hide1A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide1A"+i).style.display = "none";
    		}
			if (document.getElementById("hide2A"+i).style.display === "none") 
			{
        			document.getElementById("hide2A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide2A"+i).style.display = "none";
    		}
			if (document.getElementById("hide3A"+i).style.display === "none") 
			{
        			document.getElementById("hide3A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide3A"+i).style.display = "none";
    		}
			if (document.getElementById("hide4A"+i).style.display === "none") 
			{
        			document.getElementById("hide4A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide4A"+i).style.display = "none";
    		}
			if (document.getElementById("hide5A"+i).style.display === "none") 
			{
        			document.getElementById("hide5A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide5A"+i).style.display = "none";
    		}
			if (document.getElementById("hide6A"+i).style.display === "none") 
			{
        			document.getElementById("hide6A"+i).style.display = "table-cell";
    		} 
			else 
			{
        		document.getElementById("hide6A"+i).style.display = "none";
    		}
		}
		
		//For minutes
		$( document ).ready(function()
		{
    		var mId=document.getElementById("mId").value;
			//alert ("Id:"+mId);
			if(mId!=0)
			{
				$.ajax({
					url : "ajax/minutesOfMeeting.ajax.php",
					type : "POST",
					datatype: "JSON",
					data : {"method":"getAgenda","mId":mId},
					success : function(data)
					{		
						data = data.trim();
						var allData=[];
						allData=data.split('#');
						//alert (allData);
						var meetingRes=[];
						meetingRes=JSON.parse(allData[0]);
						var meetingAgenda=[];
						var meetingTemplate = [];
						meetingAgenda=JSON.parse(allData[1]);
						meetingTemplate=JSON.parse(allData[2]);
						var len=meetingAgenda.length;
						var i;
						var id=0;
						var meetingAtt=[];
						var att;
						var atId;
						meetingAtt=JSON.parse(allData[3]);
						document.getElementById("pageheader").innerHTML="Meeting - #"+meetingRes['Id']+" Meeting for "+meetingRes['Title'];
						//alert (allData[2].length);
						if( allData[2].length < 7)
						{
							//alert("in if");
							document.getElementById("note").value="Since there was no quorum at the said time, i.e "+meetingRes['MeetingTime']+", the "+meetingRes['Title']+" was adjourned and subsequently commenced at hh:mm am/pm Out of a total of "+meetingAtt.length+" members, members attended the "+meetingRes['Title']+". Members requested Chairman "+allData[4]+" to take seat and preside over the meeting after which meeting commenced.";
						}
						else
						{
							//alert("in else");
							document.getElementById("note").value = meetingTemplate['Note'];	
						}
						document.getElementById("id0").value=meetingAgenda[0]['Id'];
						document.getElementById("srNo0").value=meetingAgenda[0]['AgendaSrNo'];
						document.getElementById("agenda0").value="Agenda #"+meetingAgenda[0]['AgendaSrNo']+" "+meetingAgenda[0]['Question'];
						var mint=meetingAgenda[0]['Minutes'];
						var sContent;
						var maxR=1;
						if(mint === undefined)
						{
							for(i=1;i<len;i++)
							{
								sContent="<tr><input type='hidden' name='id"+i+"' id='id"+i+"' value="+meetingAgenda[i]['Id']+"><input type='hidden' name='srNo"+i+"' id='srNo"+i+"' value="+meetingAgenda[i]['AgendaSrNo']+"><td width='100%'style='text-align:left;padding-top:1%'><b><input type='button' name='agenda"+i+"' id='agenda"+i+"' style='width:90%;text-align:left' value='Agenda #"+meetingAgenda[i]['AgendaSrNo']+" "+meetingAgenda[i]['Question']+"' onClick='collapsable(this.id);'></b></td></tr>";
								sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide1A"+i+"'><b><label>Minutes:</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left;display:none; padding-top:1%;display:none' id='hide2A"+i+"'><textarea name='minutes"+i+"' id='minutes"+i+"' placeholder='Enter minutes here' rows='7' cols='120'></textarea></b></td></tr>";
                            	sContent+="<tr><td width='100%' style='text-align:left;padding-top:1%;display:none' id='hide3A"+i+"'><b><label>Resolution:</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide4A"+i+"'><textarea name='resolution"+i+"' id='resolution"+i+"' placeholder='Enter resolution here' rows='7' cols='120'>RESOLVED BY</textarea></b></td></tr>";
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide5A"+i+"'><b><label>Proposed By:</label><label style='padding-left:11%;width:25%'>Seconded By:</label><label style='padding-left:6%;width:25%'>Passed :</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide6A"+i+"'><select id='pName"+i+"' name='pName"+i+"' style='width:20%'><?php echo $dataForProposedByAndSecondedBy; ?></select><select id='sName"+i+"' name='sName"+i+"' style='width:20%'><?php echo $dataForProposedByAndSecondedBy;?></select><input type='text' id='passBy"+i+"' name='passBy"+i+"' value='Unanimously'/></td></tr>";
								$("#agenda > tbody").append(sContent);
								maxR=maxR+1;
								document.getElementById("maxrows").value=maxR;
								scontent=" ";
							}
						}
						else
						{
							document.getElementById("presentMemberList").style.display = "table";
							document.getElementById("minutes0").value=meetingAgenda[0]['Minutes'];
							document.getElementById("resolution0").value=meetingAgenda[0]['Resolution'];
							document.getElementById("pName0").value=meetingAgenda[0]['ProposedBy'];
							document.getElementById("sName0").value=meetingAgenda[0]['SecondedBy'];
							document.getElementById("passBy0").value=meetingAgenda[0]['PassedBy'];
							for(i=1;i<len;i++)
							{
								sContent="<tr><input type='hidden' name='id"+i+"' id='id"+i+"' value="+meetingAgenda[i]['Id']+"><input type='hidden' name='srNo"+i+"' id='srNo"+i+"' value="+meetingAgenda[i]['AgendaSrNo']+"><td width='100%'style='text-align:left;padding-top:1%'><b><input type='button' name='agenda"+i+"' id='agenda"+i+"' style='width:90%;text-align:left' value='Agenda #"+meetingAgenda[i]['AgendaSrNo']+" "+meetingAgenda[i]['Question']+"' onClick='collapsable(this.id);'></b></td></tr>";
								sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide1A"+i+"'><b><label>Minutes:</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left;display:none; padding-top:1%;display:none' id='hide2A"+i+"'><textarea name='minutes"+i+"' id='minutes"+i+"' placeholder='Enter minutes here' rows='7' cols='120'>"+meetingAgenda[i]['Minutes']+"</textarea></b></td></tr>";
                            	sContent+="<tr><td width='100%' style='text-align:left;padding-top:1%;display:none' id='hide3A"+i+"'><b><label>Resolution:</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide4A"+i+"'><textarea name='resolution"+i+"' id='resolution"+i+"' placeholder='Enter resolution here' rows='7' cols='120' >"+meetingAgenda[i]['Resolution']+"</textarea></b></td></tr>";
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide5A"+i+"'><b><label>Proposed By:</label><label style='padding-left:11%;width:25%'>Seconded By:</label><label style='padding-left:6%;width:25%'>Passed :</label></td></tr>"; 
                            	sContent+="<tr><td width='100%' style='text-align:left; padding-top:1%;display:none' id='hide6A"+i+"'><select id='pName"+i+"' name='pName"+i+"' style='width:20%'><?php echo $dataForProposedByAndSecondedBy; ?></select><select id='sName"+i+"' name='sName"+i+"' style='width:20%'><?php echo $dataForProposedByAndSecondedBy;?></select><input type='text' id='passBy"+i+"' name='passBy"+i+"' value='"+meetingAgenda[i]['PassedBy']+"' /></td></tr>";
								$("#agenda > tbody").append(sContent);
								maxR=maxR+1;
								document.getElementById("maxrows").value=maxR;
								scontent=" ";
							}
							for(i = 1;i < len;i++)
							{
								document.getElementById("pName"+i).value=meetingAgenda[i]['ProposedBy'];
								document.getElementById("sName"+i).value=meetingAgenda[i]['SecondedBy'];
							}
							document.getElementById("create").value="Update";
						}
						var meetingAtt=[];
						meetingAtt=JSON.parse(allData[3]);
						var att;
						var atId;
						var checks = document.getElementsByClassName('checkBox');
						for(i=0;i<meetingAtt.length;i++)
						{
							checks.forEach(function(val, index, ar)
							{
								if(ar[index].id==meetingAtt[i]['MemberId'])
								{
									if(meetingAtt[i]['Attendance'] == "P")
									{
										ar[index].checked=true;
									}
								}
							});
						}
							//alert("G:"+memRes[1]);
						document.getElementsByClassName('checkBox')[0].checked = false;						
						/*var checks = document.getElementsByClassName('checkBox');
						for(i=0;i<meetingAtt.length;i++)
						{
							checks.forEach(function(val, index, ar)
							{
								if(meetingAtt[i]['Attendance']=="P" && ar[index].id==meetingAtt[i]['MemberId'])
								{
									ar[index].checked=true;
								}
								else
								{
									ar[index].checked=false;
								}
							});
						}*/
					}
				});
			}
		});
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
		@media all 
		{
			.page-break	{ display: none; }
		}

		@media print
		{
			.page-break	{ display: block; page-break-before: always; }
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
	  
      </style>
    </head>
  <body>
    <center>
      <form id="minutesofmeeting" name="minutesofmeeting" action="#" method="post" enctype="multipart/form-data">
        <br>
        <div class="panel panel-info" id="panel" style="display:none">
              <div class="panel-heading" id="pageheader">Meeting #40 - Meeting for Cultural Committee</div>
              <div style="text-align:right;padding-right: 50px;padding-top: 10px;"></div>
                <table style="width:100%">
                	<tr>
                    <button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa fa-arrow-left"></i>
                    	<td style="width:70%">
                        	<table style="width:100%">
                            	 <tr>
                                 	<input type="hidden" name="mId" id="mId" value="<?php echo $mId; ?>"/>
                                 	<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%"><b><label>Note:</label></td>
                  				</tr> 
                            	<tr>
                                	<input type="hidden" name="maxrows" id="maxrows" value="1"/>
                                   	<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%"><textarea name="note" id="note" placeholder="Enter minutes here" rows="7" cols="120"></textarea></b></td>
                  				</tr>
                            	<tr>
                                	<td width="100%" style="text-align:left;padding-left:4%;padding-top:1%"><label>Fill the Minutes and Resolution for the following agenda in the blank provided:</label><input type="button" id="btnExCol" class="btn btn-primary" value="Expand" onClick="ExpandCollpase()" style="padding: 2px 5px;font-size:10pt;float:right;margin-right:10%"></td>
                                </tr>
                   				<tr>
                                   <input type="hidden" id="mId" name="mId" value="<?php echo $mId; ?>"/>
                                   <td width="100%" style="text-align:left;padding-left:4%;padding-top:1%">
                                   	   <div>
                                   		<table id="agenda" style="width:100%">
                                        	<tr>
                                            	<input type="hidden" name="id0" id="id0"/>
                                            	<input type="hidden" name="srNo0" id="srNo0"/>
                                            	<td width="100%" style="text-align:left;padding-top:1%"><b><input type="button" name="agenda0" id="agenda0" style="width:90%;text-align:left" onClick="collapsable(this.id);"></b></td>
                  							</tr> 
                                			<tr>
                                 				<td width="100%" style="text-align:left; padding-top:1%;display:none" id="hide1A0"><b><label>Minutes:</label></td>
                  							</tr> 
                                			<tr>
                                   				<td width="100%" style="text-align:left; padding-top:1%;display:none" id="hide2A0"><textarea name="minutes0" id="minutes0" placeholder="Enter minutes here" rows="7" cols="120"></textarea></b></td>
                  							</tr>
                                			<tr>
                                 				<td width="100%" style="text-align:left;padding-top:1%;display:none" id="hide3A0"><b><label>Resolution:</label></td>
                  							</tr> 
                                			<tr>
                                   				<td width="100%" style="text-align:left; padding-top:1%;display:none" id="hide4A0"><textarea name="resolution0" id="resolution0" placeholder="Enter resolution here" rows="7" cols="120">RESOLVED BY </textarea></b></td>
                  							</tr>
                                			<tr>
                                				<td width="100%" style="text-align:left; padding-top:1%;display:none" id="hide5A0"><b><label>Proposed By:</label><label style="padding-left:11%;width:25%">Seconded By:</label><label style="padding-left:6%;width:25%">Passed :</label></td>
                                			</tr>  
                                			<tr>
                                				<td idth="100%" style="text-align:left; padding-top:1%;display:none" id="hide6A0">
                                                <select id="pName0" name="pName0" style="width:20%">
                                                <?php
													echo $dataForProposedByAndSecondedBy;
													//echo $obj_cMeeting->comboboxForSelect($sql,0);
												?>
                                                </select><select id="sName0" name="sName0" style="width:20%">
                                                <?php
													echo $dataForProposedByAndSecondedBy;
													//echo $obj_cMeeting->comboboxForSelect($sql,0);
												?>
                                                </select><input type="text" id="passBy0" name="passBy0" value="Unanimously"/>
                                    			</td>
                                			</tr>
                                        </table>
                                      </div>
                                   </td>
                                </tr>
                                <tr>
                                 	<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%"><b><label>End Note:</label></td>
                  				</tr> 
                            	<tr>
                                   	<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%"><textarea name="endNote" id="endNote" placeholder="Enter minutes here" rows="4" cols="120">After all the matters were discussed, meeting ended with a vote of thanks to the Chairman.  </textarea></b></td>
                  				</tr>
                                <tr>
                                	<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="create" id="create" value="Save" class="btn btn-primary" onClick="FetchAgendaDetails()" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="preview" id="preview" value="Preview" class="btn btn-primary" onClick="PreviewDetails()" style="display:none" />
                                    </td>
                                </tr>
                                 <tr>
                                 	<td width="100%" style="text-align:center;display:none" id="cbHeader"><div style="text-align:center" id="cbHeader"><br><br><input type="checkbox" id="socHeader" name="socHeader" onChange="addHeader()"><b>Need Society Header?</b><br></div></td>
                                 </tr>
                                 <tr id="preview_table" style="display:none;">
        							<td width="100%" style="text-align:left;padding-left:4%; padding-top:1%"><textarea name="events_desc" id="events_desc" rows="5" cols="50" style="text-align:center; margin-left:40%;"></textarea></td>
        						</tr>
                                <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('events_desc', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] },
								{ name: 'insert', items: [ 'Table' ] },
								{ name: 'insert_2', items: [ 'PageBreak' ] }
   								 ],
								 height: 350,
        						 width: "100%",
								 uiColor: '#14B8C4'
								 });
							</script>
                            <tr style="display:none" id="finalRow">
                            	<td width="100%" style="text-align:center;padding-left:4%; padding-top:1%;"><input type="button" name="print" id="print" value="Print" class="btn btn-primary" onClick="for_print()">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="final" name="final" value="Finalise" class="btn btn-primary" onClick="finaliseMethod()"/> </td>
                            </tr>
                           </table>
                        </td>
                        <td  style="width:30%">   	
                            <table  style="width:100%">
                                <tr>
                                    <td style="text-align:left; padding-top:1%"><font color="#FF3300">*</font><label>Attendees:</label></td>
                                </tr>
                                  <tr>
                                   	<td style="text-align:left">
                                        <div style="overflow-y:scroll;overflow-x:hidden;width:250px; height:250px; border:solid #CCCCCC 2px;" name="mem_id[]" id="mem_id">
                        					<p id="msgDiv" style="display:none;"></p>
                                            <?php
												$memRes = $obj_cMeeting->getMeetingAttendees($GroupId,$mId);
												if($memRes > 0)
												{
											?>
                                                    <ul><li>&nbsp;<input type='checkbox' id ='0' class='checkBox' name='mem_id[]' checked = 'true'/>&nbsp;All		</li>
													<?php 
													for($i=0;$i<sizeof($memRes);$i++)
													{
													?>
														<li>&nbsp;<input type='checkbox' id="<?php echo $memRes[$i]['MemberId'];?>" class='checkBox' onChange='uncheckDefaultCheckBox(this.id);' name="<?php echo $memRes[$i]['MemberId'];?>"/> &nbsp <?php echo $memRes[$i]['other_name'];?></li>
													<?php 
                									}
												}
												else
												{
													
											?>
                                            		<ul><li>Members Not Found</li></ul>
                                                   <?php 
												}
												?>
                      					</div>
                                     </td>
                                   </tr>
                               </table>
                               <br>
                               <table  style="width:100%;display:none" id = "presentMemberList">
                                <tr>
                                    <td style="text-align:left; padding-top:1%"><label>Members mark present:</label></td>
                                </tr>
                                  <tr>
                                   	<td style="text-align:left">
                                        <div style="overflow-y:scroll;overflow-x:hidden;width:250px; height:250px; border:solid #CCCCCC 2px;" name="presentMemId[]" id="presentMemId">
                        					<p id="msgDiv" style="display:none;"></p>
                                            <?php
												$memRes = $obj_cMeeting->getMembersMarkPresent($GroupId,$mId);
												if($memRes > 0)
												{
											?>
                                                    <ul>
													<?php 
													for($i=0;$i<sizeof($memRes);$i++)
													{
													?>
														<li>&nbsp;&nbsp <?php echo $memRes[$i]['other_name'];?></li>
													<?php 
                									}
												}
												else
												{
													
											?>
                                            		<ul><li>Members Not Found</li></ul>
                                                   <?php 
												}
												?>
                      					</div>
                                     </td>
                                   </tr>
                               </table>
                    		</td>  	
                     	</tr>
				</table>
                <div id="for_printing" style="display:none"></div>
          </div>
        </div>
      </form>
    </center>
  </body>
</html>
<?php include_once "includes/foot.php"; ?>