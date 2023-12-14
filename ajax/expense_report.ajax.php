<?php

include_once("../classes/include/dbop.class.php");
include_once("../classes/utility.class.php");
include_once("../classes/general_expense_report.class.php");

$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_Utility = new utility($dbConn, $dbConnRoot);
$obj_expense_report = new GeneralExpenseReport($dbConn, $dbConnRoot); 

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchExpeseReport')
{
	$PeriodType = $_REQUEST['period_type'];
	$result = $obj_expense_report->fetchExpenseReport($PeriodType);
	echo '@@@'.json_encode($result);
}
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchExpeseReportDetails')
{
	$reportType = $_REQUEST['reportType'];
	$FromDate   = $_REQUEST['from'];
	$ToDate 	= $_REQUEST['to']; 
	if($reportType == 1)
	{
		$result = $obj_expense_report->fetchExpenseReportDetails($reportType,$FromDate,$ToDate);
		print_r($result);
	}
	if($reportType == 2)
	{
		echo $result = $obj_expense_report->fetchExpenseReportDetails1($reportType,$FromDate,$ToDate);
	}
	//echo '@@@'.json_encode($result);
	//echo "Test";
}


?>