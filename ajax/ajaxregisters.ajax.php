
<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/mem_registers.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$mem_registers = new mem_registers($dbConn,$dbConnRoot);

//echo $_REQUEST["method"]."@@@";

if($_REQUEST['method'] == "insert_new_iid")
{
	$unit_id = $_REQUEST['unit_id'];
	$iid = $_REQUEST['iid'];
	$name = $_REQUEST['name'];
	$date = $_REQUEST['date'];
	//$status = $_REQUEST['status'];
	
	$insert = $mem_registers->insert_iid($unit_id,$iid,$name,$date);
	
	echo json_encode($insert);
}

if($_REQUEST['method'] == "update_iid")
{
	$old_iid = $_REQUEST['old_iid'];
	$new_iid = $_REQUEST['new_iid'];
	$new_owner_name = $_REQUEST['new_owner_name'];
	$ownership_date = $_REQUEST['ownership_date'];
	$member_id = $_REQUEST['member_id'];
	
	$update = $mem_registers->update_iid($old_iid,$new_iid,$member_id,$new_owner_name,$ownership_date);
	
	echo json_encode($update);
}
if($_REQUEST['method'] == "rename_iid")
{
	$old_iid = $_REQUEST['old_iid'];
	$new_iid = $_REQUEST['new_iid'];
	$ownership_date = $_REQUEST['ownership_date'];
	$new_owner_name = $_REQUEST['new_owner_name'];
	$member_id = $_REQUEST['member_id'];
	$update = $mem_registers->rename_iid($old_iid,$new_iid,$member_id,$new_owner_name,$ownership_date);
	
	echo json_encode($update);
}
if($_REQUEST["method"] == "fetch_unit_details_for_nomination")
{
	$unit_id = $_REQUEST["unit_id"];
	$get_data = $obj_templates->fetch_data_for_nomination($unit_id);
	
	echo json_encode($get_data);
}

if($_REQUEST["method"] == "fetch_unit_details_and_cc_dets")
{
	$unit_id = $_REQUEST["unit_id"];
	$get_data = $obj_templates->fetch_unit_details_and_cc_dets($unit_id);
		
	echo json_encode($get_data);
}

if($_REQUEST["method"] == "fetch_subject")
{
	$temp_id = $_REQUEST["temp_id"];
	$subject = $obj_templates->fetch_subject($temp_id);
	
	echo json_encode($subject);
}

if($_REQUEST["method"] == "fetch_unit_id")
{
	$rc_id = $_REQUEST['rc_id'];
	$unit_id = $obj_templates->fetch_unit_id($rc_id);
	
	echo json_encode($unit_id);
}

if($_REQUEST["method"]=="fetch_template_data")
{
	$template_id = $_REQUEST["template_id"];
	
	$to_pass = array();
	$to_pass['unit_id'] = $_REQUEST['unit_id'];
	$to_pass['template_id'] = $template_id;
	$to_pass['date_of_meeting'] = $_REQUEST['date_of_meeting'];
	$to_pass['venue'] = $_REQUEST['venue'];
	$to_pass['time_of_meeting'] = $_REQUEST['time_of_meeting'];
	$to_pass['last_agm_date'] = $_REQUEST['last_agm_date'];
	$to_pass['fine_rs_id'] = $_REQUEST['fine_rs_id'];
	$to_pass['applier_name'] = $_REQUEST['applier_name'];
	$to_pass['relation'] = $_REQUEST['relation'];
	$to_pass['owner_name'] = $_REQUEST['owner_name'];
	$to_pass['address'] = $_REQUEST['address'];
	$to_pass['start_date'] = $_REQUEST['start_date'];
	
	$to_pass['manager_name'] = $_REQUEST['manager_name'];
	$to_pass['bank_name'] = $_REQUEST['bank_name'];
	$to_pass['bank_address'] = $_REQUEST['bank_address'];
	$to_pass['flat_no'] = $_REQUEST['flat_no'];
	$to_pass['flat_owner_name'] = $_REQUEST['flat_owner_name'];
	$to_pass['flat_cost'] = $_REQUEST['flat_cost'];
	$to_pass['cc_no'] = $_REQUEST['cc_no'];
	$to_pass['cc_date'] = $_REQUEST['cc_date'];
	
	$to_pass['gender'] = $_REQUEST['gender'];
	$to_pass['tenant_name'] = $_REQUEST['tenant_name'];
	
	$to_pass['flat_owner_address'] = $_REQUEST['flat_owner_address'];
	$to_pass['current_em'] = $_REQUEST['current_em'];
	
	//data: {"method": "fetch_template_data", "template_id":temp_id, "flat_owner_name":nomi_owner_name, "flat_no":nomi_flat_no, "area":area, "sc_no":sc_no, "sc_date":sc_date, "amt_per_share":amt_per_share, "no_of_shares":no_of_shares, "start_no":start_no, "end_no":end_no, "witness_name_1":witness_name_1, "witness_address_1":witness_address_1, "witness_name_2":witness_name_2, "witness_address_2":witness_address_2, "nominees_info":JSON.stringify(nominees_info)},
	
	$nominees_info = json_decode($_REQUEST['nominees_info']);
	$to_pass['area'] = $_REQUEST['area'];
	$to_pass['sc_no'] = $_REQUEST['sc_no'];
	$to_pass['sc_date'] = $_REQUEST['sc_date'];
	$to_pass['amt_per_share'] = $_REQUEST['amt_per_share'];
	$to_pass['no_of_shares'] = $_REQUEST['no_of_shares'];
	$to_pass['start_no'] = $_REQUEST['start_no'];
	$to_pass['end_no'] = $_REQUEST['end_no'];
	$to_pass['witness_name_1'] = $_REQUEST['witness_name_1'];
	$to_pass['witness_address_1'] = $_REQUEST['witness_address_1'];
	$to_pass['witness_name_2'] = $_REQUEST['witness_name_2'];
	$to_pass['witness_address_2'] = $_REQUEST['witness_address_2'];
	$to_pass['triplicate'] = $_REQUEST['is_triplicate'];
	//echo "check: <pre>".print_r($nominees_info)."</pre>";
	
	$to_pass['ledger_id'] = $_REQUEST['ledger_id'];
	$to_pass['place'] = $_REQUEST['place'];
	$to_pass['flat_id'] = $_REQUEST['flat_id'];
		
	if($template_id == 1) //member's associaion form
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 27) //overdue payment
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 25) //AGM
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 28 || $template_id == 29) //fine(debit) and reverse charge(credit) resp.
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 49) //nomination form
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass, $nominees_info);
	}
	else if($template_id == 31 || $template_id == 33) //passport and domicile resp.
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 34) //bank noc
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 35) //lnl noc
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 36) //em noc
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 37 || $template_id == 38) //web access blocked
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}
	else if($template_id == 51) //property tax certificate
	{
		$to_display_temp = $obj_templates->fetch_data($to_pass);
	}

	echo json_encode($to_display_temp);	
}

?>