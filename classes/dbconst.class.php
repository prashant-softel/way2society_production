<?php 
	if($_SERVER['HTTP_HOST']=="localhost")
	{
		if(!isset($_SESSION)){ session_start(); }
	}
	
	//Roles
	define('ROLE_SUPER_ADMIN', 'Super Admin');
	define('ROLE_ADMIN', 'Admin');
	define('ROLE_MEMBER', 'Member');
	define('ROLE_ADMIN_MEMBER', 'Admin Member');
	define('ROLE_MASTER_ADMIN', 'Master Admin');
	
	//  Add New Role
	define('ROLE_CONTRACTOR', 'Contractor');
	define('ROLE_SECURITY', 'Security');
	
	define('ROLE_ACCOUNTANT', 'Accountant');
	define('ROLE_MANAGER', 'Manager');	
		
	//TransactionType
	define ('TRANSACTION_CREDIT', 'Credit');
	define ('TRANSACTION_DEBIT', 'Debit');
	
	//For Table BankRegister
	define ('TRANSACTION_PAID_AMOUNT', 'PaidAmount');
	define ('TRANSACTION_RECEIVED_AMOUNT', 'ReceivedAmount');
		
	//ID's in group
	define ('LIABILITY', '1');
	define ('ASSET', '2');
	define ('INCOME', '3');
	define ('EXPENSE', '4');


	//Depreciation Methods
	define ('STRAIGHT_LINE', '1'); 	//Straight line
	define ('Reducing_Balance', '2'); 	//Reducing balance
	
	//For lien
	
	define ('DOC_TYPE_LIEN_ID', '8');
	
	//ID's in vouchertype Table
	define ('VOUCHER_SALES', '1');
	define ('VOUCHER_PAYMENT', '2');
	define ('VOUCHER_RECEIPT', '3');
	define ('VOUCHER_PURCHASE', '4');
	define ('VOUCHER_JOURNAL', '5');
	define ('VOUCHER_CONTRA', '6');
	
	//Constant define to identify the transaction module to keep record
	define('VOUCHER_INVOICE','7');
	define('VOUCHER_CREDIT_NOTE','8');
	define('VOUCHER_DEBIT_NOTE','9');
	
	
	//Default Year and Period
	define('DEFAULT_YEAR', $_SESSION['default_year']);
	define('DEFAULT_PERIOD', $_SESSION['default_period']);
	
	//ID's in account_category Table
	define('PRIMARY', 1); //1
	define('CURRENT_ASSET', $_SESSION['default_current_asset']);	//2
	define('DUE_FROM_MEMBERS', $_SESSION['default_due_from_member']);	//3
	define('BANK_ACCOUNT', $_SESSION['default_bank_account']);	//6
	define('CASH_ACCOUNT', $_SESSION['default_cash_account']);	//6
	define('FIXED_ASSET', $_SESSION['default_fixed_asset']);
	
	//ID's in ledger Table
	define('INTEREST_ON_PRINCIPLE_DUE', $_SESSION['default_interest_on_principle']);	//6
	define('PENALTY_TO_MEMBER', $_SESSION['default_penalty_to_member']);
	define('BANK_CHARGES', $_SESSION['default_bank_charges']);
	define('TDS_PAYABLE', $_SESSION['default_tds_payable']);
	define('TDS_RECEIVABLE', $_SESSION['default_tds_receivable']);
	define('IMPOSE_FINE', $_SESSION['default_impose_fine']);                   /// impose Fine
	define('ADJUSTMENT_CREDIT', $_SESSION['default_adjustment_credit']);
	define('IGST_SERVICE_TAX', $_SESSION['igst_service_tax']);
	define('CGST_SERVICE_TAX', $_SESSION['cgst_service_tax']);
	define('SGST_SERVICE_TAX', $_SESSION['sgst_service_tax']);
	define('CESS_SERVICE_TAX', $_SESSION['cess_service_tax']);
	define('ROUND_OFF_LEDGER', $_SESSION['default_ledger_round_off']);
	define('INPUT_CGST', $_SESSION['cgst_input']);
	define('INPUT_SGST', $_SESSION['sgst_input']);
	define('INPUT_IGST', $_SESSION['igst_input']);
	define('SINKING_FUND', $_SESSION['default_sinking_fund']);
	define('INVESTMENT_REGISTER', $_SESSION['default_investment_register']);
	
	//Transaction wise deposit ID
	define('DEPOSIT_NEFT', '-2');
	define('DEPOSIT_CASH', '-3');
	define('DEPOSIT_ONLINE', '-4');

	//Billing PreFix
	define('PREFIX_SALE_VOUCHER','SL');
	define('PREFIX_INVOICE_BILL','INV');
	define('PREFIX_JOURNAL_VOUCHER','JV');
	define('PREFIX_DEBIT_NOTE','DN');
	define('PREFIX_CREDIT_NOTE','CN');
	
	//Default society
	define('DEFAULT_SOCIETY', $_SESSION['society_id']);
	
	//Default society
	define('PG_PayuMoney', '1');
	define('PG_Paytm', '2');
	
	//For renovation service Request
	//define('RENOVATION_DOC_ID','9');
	
	define('RENOVATION_SOURCE_TABLE_ID','3');
	define('TENANT_SOURCE_TABLE_ID','1');
	define('ADDRESSPROOF_SOURCE_TABLE_ID','2');
		
	//Table name from where Voucher Tables 'RefNo' is linked to
	define('TABLE_BILLREGISTER', '1');
	define('TABLE_CHEQUE_DETAILS', '2');
	define('TABLE_PAYMENT_DETAILS', '3');
	define('TABLE_REVERSAL_CREDITS', '4');
	define('TABLE_NEFT', '5');
	define('TABLE_FD_MASTER', '6');
	define('TABLE_FIXEDASSETLIST','7');
	define('TABLE_SALESINVOICE', '8');
	define('TABLE_CREDIT_DEBIT_NOTE', '9');
	define('TABLE_JOURNAL_VOUCHER', '10');
	define('TABLE_TDSCHALLAN', '11');
	define('TABLE_FREEEZE_YEAR', '12');
	define('TABLE_VENDOR_MANAGEMENT','13');
	define('TABLE_LEDGER', '14');
	define('TABLE_BILL_MASTER', '15');
	define('TABLE_REVERSE_CHARGE_CREDIT_FINE', '16');
	// BalanceSheet Template
	
	define("ABSOLUTE_BALANCESHEET",0);
	define("CLASSIC_BALANCESHEET",1);
	
	// BIll Templates
	define("ORIGINAL_BILL_TEMPLATE",0);
	define("CLASSIC_BILL_TEMPLATE",1);
	define("MODERN_BILL_TEMPLATE",2); 
	define("CLASSIC_ADVANCE_BILL_TEMPLATE",3);
	
	if($_SERVER['HTTP_HOST']=="localhost")
	{
		define('HOST_NAME', 'http://localhost:');
	}
	else
	{
		define('HOST_NAME', 'http://way2society.com:');
		//define('HOST_NAME', 'http://localhost:');
	}
	
	//Suspense A/c
	
	define('SUSPENSE_AC','Suspense A/c');
	
	//For Digital Renting
	
	define('WAR_FILE_NAME', 'Unichem_web');

	//Rebate Method
	
	define('REBATE_METHOD_NONE', '1');
	define('REBATE_METHOD_FLAT', '2');
	//define('REBATE_DUE_WAIVER', '3');
	define('REBATE_METHOD_WAIVE','3');
	define('REBATE_METHOD_WAIVE_MENTION_AMOUNT','4');
	
	
	//For Comment Type
	define('E_TASK', '1');
	define('E_APPROVAL', '2');
	define('E_MEETING', '3');
	
	//Interest Method
	
	define('INTEREST_METHOD_DELAY_DUE', '1');
	define('INTEREST_METHOD_FULL_MONTH', '2');
	define('INTEREST_METHOD_FULL_CYCLE','3');
	define('INTEREST_METHOD_DELAY_SINCE_BILLING_DAYS','4');
	
	//Bill Format Method
	define('BILL_FORMAT_WITH_RECEIPT', '1');
	define('BILL_FORMAT_WITHOUT_RECEIPT', '2');
	
	//Notice Type
	define('NOTICE_TYPE_ADMINISTRATION', '1');
	define('NOTICE_TYPE_GENERAL', '2');
	define('NOTICE_TYPE_BUY_SELL_RENT', '3');
	
	//Default Profile ID's in DB in table 'societydb.profile'
	define('PROFILE_SUPER_ADMIN_ID', '1');
	define('PROFILE_ADMIN_ID', '2');
	define('PROFILE_MEMBER_ID', '3');
	define('PROFILE_ADMIN_MEMBER_ID', '4');
	
	//UserProfiles
	define('PROFILE_HOME', 'home_s.php');
	define('PROFILE_GENERATE_BILL', 'genbill.php');
	define('PROFILE_CREATE_INVOICE', 'sale_invoice_list.php');
	define('PROFILE_EDIT_BILL', 'Maintenance_bill.php');
	define('PROFILE_CHEQUE_ENTRY', 'ChequeDetails.php');
	define('PROFILE_PAYMENTS', 'PaymentDetails.php');
	define('PROFILE_UPDATE_INTEREST', 'updateInterest.php');
	define('PROFILE_REVERSE_CHARGE', 'reverse_charges.php');
	define('PROFILE_MANAGE_MASTER', 'settings.php');
	define('PROFILE_BANK_RECO', 'bank_reconciliation.php');
	define('PROFILE_SEND_NOTIFICATION', 'notification.php');
	define('PROFILE_NEFT_DETAILS', 'NeftDetails.php');
	define('PROFILE_CREATE_VOUCHER', 'createvoucher.php');
	define('PROFILE_VOUCHER_EDIT', 'VoucherEdit.php');
	define('PROFILE_DEPOSITGROUP', 'depositgroup.php');
	define('PROFILE_CHEQUELEAFBOOK', 'chequeleafbook.php');
	define('PROFILE_EDIT_MEMBER', 'view_member_profile.php');
	define('PROFILE_SEND_NOTICE', 'addnotice.php');
	define('PROFILE_SEND_EVENT', 'events.php');
	define('PROFILE_CREATE_ALBUM', '#gallery_upload.php');
	define('PROFILE_CREATE_POLL', 'create_poll.php');
	define('PROFILE_APPROVALS_LEASE', '#app_lease.php');
	define('PROFILE_SERVICE_PROVIDER', '#1.php');
	define('PROFILE_PHOTO', '#Gallery.php');
	define('PROFILE_MESSAGE', 'sendGeneralMsgs.php');//
	define('PROFILE_MANAGE_LIEN', '#addlien.php');
	define('PROFILE_USER_MANAGEMENT', 'add_member_id.php');
	define('PROFILE_VENDOR_MANAGEMENT', 'vendor.php');
	
	
	define('PROFILE_CLASSIFIED', '#classified.php');
	
	
	//Reconcilation Related Constant
	
	define("MATCH_ENTRY",'1');
	define("PRESENT_IN_BANK",'2');
	define("PRESENT_IN_W2S",'3');
	define("AMOUNT_MATCH",'4');
	
	define("RECO_TRANSACTION",'RecoTransaction');
	
	//w2s SMS TemplateID  
	define("BILL_NOTIFICATION_TEMP_ID",'1207162252824521240');
	define("BILL_REMINDER_TEMP_ID",'1207162252841361156');
	
	
	//Max date used in Bill Master
	define('PHP_MAX_DATE', '2037-03-31');

		define('GST_START_DATE', $_SESSION['gst_start_date']);
	
	//Month names array as per cycle
	$MONTHS_MONTHLY = array('April','May','June','July','August','September','October','November','December','January','February','March');
	$MONTHS_BIMONTHLY = array('April-May','June-July','August-September','October-November','December-January','February-March');
	$MONTHS_QUATERLY = array('April-June','July-September','October-December','January-March');
	$MONTHS_QUADRUPLE = array('April-July','August-November','December-March');
	$MONTHS_HALFYEARLY = array('April-September', 'October-March');
	$MONTHS_YEARLY = array('April-March');
		
	//action constants
	define ('NONE', '0');
	define ('ADD', '1');	
	define ('DELETE', '2');	
	define ('EDIT', '3');	
	
	
	//Charge Type in reverse/fine/creditnote
	
	define("REVERSE_CHARGE",1);
	define("FINE",2);
	
	//Bill Type
	// 0 for maintaince and 1 for supplementry and 2 invoice and 3
	define("Maintenance",0);
	define("Supplementry",1);
	define("Invoice",2);
	define("CREDIT_NOTE",3);
	define("DEBIT_NOTE",4);
	define("Combine_Bill", 5);
	
	//send reminder sms
	define("SENDBILLREMINDER", 1);
	define("SENDEVENTREMINDER", 2);
	define("SENDFDREMINDER", 3);

	//FD Actions
	define('FD_CREATED' ,'0');
	define('FD_CLOSED' ,'1');
	define('FD_RENEW' ,'2');
	define( 'CREATE_NEW_LEDGER', '-1'); 
	
	//Interest frequency
	define('MONTHLY' ,'0');
	define('QUARTERLY' ,'1');
	define('SEMIANNUAL' ,'2');
	define('ANNUAL' ,'3');
	define('ON_MATURITY' ,'4');

	//FD Interest Type
	define('FD_CUMULATIVE' ,'0');
	define('FD_NON_CUMULATIVE' ,'1');
	
	define('CLIENT_FEATURE_EXPORT_MODULE','CLIENT_FEATURE_EXPORT_MODULE');
	define('CLIENT_FEATURE_SMS_MODULE','CLIENT_FEATURE_SMS_MODULE');
	
	//bill type
	define("BILL_TYPE_REGULAR", 0);
	define("BILL_TYPE_SUPPLEMENTARY", 1);
	
	//sms sent type
	define("SMS_TYPE_GENERAL", 0);
	define("SMS_TYPE_BILL_NOTIFICATION_MANUALLY", 1);
	define("SMS_TYPE_BILL_NOTIFICATION_CRON", 2);
	//Email activation status
	define("ACCOUNT_EXIST_ACTIVE", 1);
	define("ACCOUNT_EXIST_MAPPING_NOT_FOUND", 2);
	define("NO_ACCOUNT", 3);

	define("MEMBER_TYPE_OTHER", 0);
	define("MEMBER_TYPE_OWNER", 1);
	define("MEMBER_TYPE_COOWNER", 2);
	define("MEMBER_TYPE_TENANT", 10);
	
	//Vehicle Type
	
	define("VEHICLE_BIKE",2);
	define("VEHICLE_CAR",4);
	
	
	// Committee Tresurure
	
	define('TREASURER','Treasurer');
	define('SECRETARY','Secretary');
	define('CHAIRMAN','Chairman');	
	
	
	
	define("PRIORITY_LOW", 1);
	define("PRIORITY_MEDIUM", 2);
	define("PRIORITY_HIGH", 3);
	define("PRIORITY_CRITICAL", 4);
	
	
	//Module Type Id in appdefault_new table
	
	define("TENANT_MODULE_TYPE", 1);
	define("DIGITAL_RENTING_TEMPLATE_ID", 55);
	define("ROOT_FOLDER_NAME","beta_aws_master");

	// Lien Constant
	
	define("LIEN_ISSUED","NOC");
	define("LIEN_OPEN","open");
	define("LIEN_CLOSED","closed");
	define("LIEN_DELETE","deleted");
	
	//Notification WarFile Constant	
	define("WAR_FILE","W2S1");
	
	//These settings are for AWS TLS. But not working
	define("SMTP_Username","AKIAWORPNMPGX76CCAPQ");
	define("SMTP_Password", "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe");
	define("SMTP_endpoint", "email-smtp.ap-south-1.amazonaws.com");
	define("SMTP_Port", 587);
	define("SMTP_Security", "tls");
//	define("sender_noreply", "no-reply@way2society.com");
//	define("senderName_W2S", "Way2Society.com");

	$SMTP_Username = "AKIAWORPNMPGX76CCAPQ";
	$SMTP_Password = "BOwueG82ahzTYrSgK5igS9qChzA6KKF35obJEEvXTrGe";
	$SMTP_endpoint = "email-smtp.ap-south-1.amazonaws.com";
	$SMTP_Port = 587;
	$SMTP_Security = "tls";

		
	// Nature of TDS 
	//$NatureOfTDS= array("193"  => "Interest on Securities", "194" => "Dividend", "195" => "Other sums payable to a non-resident", "4BA" => "Certain income from units of a business trust", "4BB" => "Winning from Horse race", "4DA" => "Payment in respect of life insurance policy", "4EE" => "Payments in respect of Deposits under National Saving Schemes", "4LA" => "Payment of Compensation on Acquisition of Certain Immovable property", "" => "");
	$NatureOfTDS = array(array("id" => "94C", "description" =>"Payment of contractors and sub-contractors"),
								array("id" => "94J", "description" =>"Fees for Professional or Technical Services"),
								array("id" => "OTH", "description" =>"Others")
							) ;
	/*$NatureOfTDS = array(array("id" => "193", "description" =>"Interest on Securities"),
						array("id" => "194", "description" =>"Dividend"),
						array("id" => "195", "description" =>"Other sums payable to a non-resident"),
						array("id" => "4BA", "description" =>"Certain income from units of a business trust"),
						array("id" => "4BB", "description" =>"Winning from Horse race"),
						array("id" => "4DA", "description" =>"Payment in respect of life insurance policy"),
						array("id" => "4EE", "description" =>"Payments in respect of Deposits under National Saving Schemes"),
						array("id" => "4LA", "description" =>"Payment of Compensation on Acquisition of Certain Immovable property"),
						array("id" => "4LC", "description" =>"Income by way of interest from specified Company payable to a non-resident"),
						array("id" => "4LD", "description" =>"Interest on Rupee denominated bond of Company or Government Securities"),
						array("id" => "6CA", "description" =>"Alcoholic liquor for human consumption"),
						array("id" => "6CB", "description" =>"Timber obtained under forest lease"),
						array("id" => "6CC", "description" =>"Timber obtained other than forest lease"),
						array("id" => "6CD", "description" =>"Any other forest produce not being timber or tendu leaves"),
						array("id" => "6CE", "description" =>"Scrap"),
						array("id" => "6CF", "description" =>"Parking Lot"),
						array("id" => "6CG", "description" =>"Toll Plaza"),
						array("id" => "6CH", "description" =>"Mining and Quarrying"),
						array("id" => "6CI", "description" =>"Tendu Leaves"),
						array("id" => "6CJ", "description" =>"Minerals"),
						array("id" => "6CK", "description" =>"Bullion and Jewellery"),
						array("id" => "92A", "description" =>"Payment to Govt. Employees other than Union Govt. employees"),
						array("id" => "92B", "description" =>"Payment of Employees other than Govt. Employees"),
						array("id" => "94A", "description" =>"Interest other than Interest on Securities"),
						array("id" => "94B", "description" =>"Winning from lotteries and crossword puzzles"),
						array("id" => "94C", "description" =>"Payment of contractors and sub-contractors"),
						array("id" => "94D", "description" =>"Insurance commission"),
						array("id" => "94E", "description" =>"Payments to non-resident Sportsmen/Sport Associations"),
						array("id" => "94F", "description" =>"Payments on account of Re-purchase of Units by Mutual Funds of UTI"),
						array("id" => "94G", "description" =>"Commission,prize etc. on sale of Lottery tickets"),
						array("id" => "94H", "description" =>"Commission or Brokerage"),
						array("id" => "94I", "description" =>"Rent"),
						array("id" => "94J", "description" =>"Fees for Professional or Technical Services"),
						array("id" => "94K", "description" =>"Income Payable to a resident assessee in respect of units of a specified Mutual Fund or of the Units of the UTI"),
						array("id" => "96A", "description" =>"Income in respect of Units of non-residents"),
						array("id" => "96B", "description" =>"Payments in respect of Units to an Offshore Fund"),
						array("id" => "96C", "description" =>"Income from foreign currency Bonds or Shares of Indian Company payable to a non-resident"),
						array("id" => "96D", "description" =>"Income of Foreign Institutional investors from securities"),
						array("id" => "2AA", "description" =>"Payment of accumulated balance due to an employee"),
						array("id" => "LBB", "description" =>"Income in respect of units of investment fund"),
						array("id" => "6CL", "description" =>"TCS on sale of Motor vehicle"),
						array("id" => "6CM", "description" =>"TCS on sale in cash of any goods (other than bullion/jewellery)"),
						array("id" => "6CN", "description" =>"TCS on providing of any services (other than Ch-XVII-B)"),
						array("id" => "LBC", "description" =>"Income in respect of investment in securitization trust"),
						array("id" => "4IC", "description" =>"Payment under specified agreement"),
						array("id" => "9IB", "description" =>"Payment of rent by certain individuals or Hindu undivided family")
	
	
	) ;*/


	$GSTTAXRATES = array(array("id" => "5", "TaxRate" =>"5"),
						array("id" => "12", "TaxRate" =>"12"),
						array("id" => "18", "TaxRate" =>"18"),
						array("id" => "28", "TaxRate" =>"28")
						);

	$DEFINETIME = array(
						array("id" => "00:00", "time" =>"12:00 AM"),
						array("id" => "01:00", "time" =>"01:00 AM"),
						array("id" => "02:00", "time" =>"02:00 AM"),
						array("id" => "03:00", "time" =>"03:00 AM"),
						array("id" => "04:00", "time" =>"04:00 AM"),
						array("id" => "05:00", "time" =>"05:00 AM"),
						array("id" => "06:00", "time" =>"06:00 AM"),
						array("id" => "08:00", "time" =>"08:00 AM"),
						array("id" => "09:00", "time" =>"09:00 AM"),
						array("id" => "10:00", "time" =>"10:00 AM"),
						array("id" => "11:00", "time" =>"11:00 AM"),
						array("id" => "12:00", "time" =>"12:00 PM"),
						array("id" => "13:00", "time" =>"01:00 PM"),
						array("id" => "14:00", "time" =>"02:00 PM"),
						array("id" => "15:00", "time" =>"03:00 PM"),
						array("id" => "16:00", "time" =>"04:00 PM"),
						array("id" => "17:00", "time" =>"05:00 PM"),
						array("id" => "18:00", "time" =>"06:00 PM"),
						array("id" => "19:00", "time" =>"07:00 PM"),
						array("id" => "20:00", "time" =>"08:00 PM"),
						array("id" => "21:00", "time" =>"09:00 PM"),
						array("id" => "22:00", "time" =>"10:00 PM"),
						array("id" => "23:00", "time" =>"11:00 PM"),
						);
	/*$DEFINETIME = array(array("id" => "22:00", "time" =>"10:00 PM"),
						array("id" => "23:00", "time" =>"11:00 PM"),
						array("id" => "23:30", "time" =>"11:30 PM"),
						array("id" => "00:00", "time" =>"12:00 AM"),
						array("id" => "00:30", "time" =>"12:30 AM"),
						array("id" => "01:00", "time" =>"01:00 AM"),
						array("id" => "01:30", "time" =>"01:30 AM"),
						array("id" => "02:00", "time" =>"02:00 AM"),
						array("id" => "02:30", "time" =>"02:30 AM"),
						array("id" => "03:00", "time" =>"03:00 AM"),
						array("id" => "03:30", "time" =>"03:30 AM"),
						array("id" => "04:00", "time" =>"04:00 AM"),
						
						);	*/				

	$statusArr = array(ADD=>"Added", EDIT=>"Edited", DELETE=>"Deleted");

	$logModulesArr = array(TABLE_BILLREGISTER=>'Bills', TABLE_CHEQUE_DETAILS=>'Receipts', TABLE_PAYMENT_DETAILS=>'Payments', TABLE_JOURNAL_VOUCHER=>'Journal Voucher', TABLE_SALESINVOICE=>'Sale Invoice', TABLE_CREDIT_DEBIT_NOTE=>'Credit & Debit Note',TABLE_TDSCHALLAN=>'TDS Challan',TABLE_FREEEZE_YEAR=>'Freeze Year',TABLE_VENDOR_MANAGEMENT=>'Vendor Management',TABLE_LEDGER=>'Ledger',TABLE_BILL_MASTER=>'Bill Master',TABLE_REVERSE_CHARGE_CREDIT_FINE=>'Reverse Charge And Fine');

		
	function getDBFormatDate($ddmmyyyy)
	{
		if($ddmmyyyy <> '' && $ddmmyyyy <> '00-00-0000')
		{
			return date('Y-m-d', strtotime($ddmmyyyy));
		}
		else
		{
			return '00-00-0000';
		}
	}
	
	function getDisplayFormatDate($yyyymmdd, $seperator = '-')
	{
		$ddmmyyyy = '';
		if(strtotime($yyyymmdd) <> '' &&  $yyyymmdd <> '0000-00-00' && $yyyymmdd <> '00-00-0000')
		{
			$ddmmyyyy = date("d" . $seperator . "m" . $seperator . "Y", strtotime($yyyymmdd));
			
		}
		/*else
		{
			return '00-00-0000';
		}*/
		return $ddmmyyyy;
		
	}
	
	function getMonths($cycleID)
	{
		$Months = array();

		switch($cycleID)
		{
			case 1:
				$Months = $GLOBALS['MONTHS_YEARLY'];
				break;
			case 2:
				$Months = $GLOBALS['MONTHS_HALFYEARLY'];
				break;
			case 3:
				$Months = $GLOBALS['MONTHS_QUADRUPLE'];
				break;
			case 4:
				$Months = $GLOBALS['MONTHS_QUATERLY'];
				break;
			case 5:
				$Months = $GLOBALS['MONTHS_BIMONTHLY'];
				break;
			case 6:
				$Months = $GLOBALS['MONTHS_MONTHLY'];
				break;
		}
	
		return $Months;
	}

	function getBillingCycleWeightage($cycleID)
	{
		$iWeight = 1;

		switch($cycleID)
		{
			case 2:
				$iWeight = 2;	//MONTHS_HALFYEARLY
				break;
			case 3:				//'MONTHS_QUADRUPLE'
				$iWeight = 3;
				break;
			case 4:
				$iWeight = 4;	//MONTHS_QUATERLY
				break;
			case 5:				//'MONTHS_BIMONTHLY'
				$iWeight = 6;
				break;
			case 6:
				$iWeight = 12;	//MONTHS_MONTHLY
				break;
			case 1:				//'MONTHS_YEARLY'
			default :
				$iWeight = 1;
				break;
		}
	
		return $iWeight;
	}
	
	function getRandomUniqueCode()
	{
		$timestamp = DateTime::createFromFormat('U.u', microtime(true));
		$uniqueTime = $timestamp->format("m-d-Y H:i:s.u");
		
		$un=  uniqid();
		$dmtun = $uniqueTime.$un;
		$mdun = md5($dmtran.$un);
		
		//$sort=substr($mdun, 16); // if you want short length code.

		return $mdun;	
	}
	
	 function getCurrentTimeStamp()
	{
		$aryDateTime = array();
		date_default_timezone_set('Asia/Kolkata');	
		$current_DateTime = date('Y-m-d H:i:s');
		$current_Time = date('H:i:s');
		$current_Date = date('Y-m-d');
		
		$aryDateTime['DateTime'] = $current_DateTime;
		$aryDateTime['Date'] = $current_Date;
		$aryDateTime['Time'] = $current_Time;
		
		return $aryDateTime;
	}
	
	function isValidEmailID($email)
	{
		$bResult = true;
		if(filter_var($email, FILTER_VALIDATE_EMAIL) == false)
		{
			$bResult = false;
		}
		//echo '<br/>******Email : ['. $email .'] is [' . $bResult . ']******<br/>';
		return $bResult;
	}
		
		
?>