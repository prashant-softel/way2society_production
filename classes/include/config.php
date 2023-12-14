<?php if(!isset($_SESSION)){ session_start(); }

//echo $_SERVER['HTTP_HOST'];
if($_SERVER['HTTP_HOST']=="localhost")
{
	define("DB_HOST","localhost");
	define("DB_USER","root");
	define("DB_PASSWORD","");
	//define("DB_DATABASE","societies");
	define("DB_DATABASE",$_SESSION['dbname']);
	
	define("DB_HOST_ROOT","localhost");
	define("DB_USER_ROOT","root");
	define("DB_PASSWORD_ROOT","");
	define("DB_DATABASE_ROOT","hostmjbt_societydb");
	
	define("DB_HOST_SM","localhost");
	define("DB_USER_SM","root");
	define("DB_PASSWORD_SM","");
	define("DB_DATABASE_SM",$_SESSION['security_dbname']);
		
	define("DB_HOST_SMROOT","localhost");
	define("DB_USER_SMROOT","root");
	define("DB_PASSWORD_SMROOT","");
	define("DB_DATABASE_SMROOT","security_rootdb");
}
else
{
	$set = 2;
	
	if($set == 1)
	{
		//echo "set is 1";
		define("DB_HOST","localhost");
		define("DB_USER","attuit_societies");	
		define("DB_PASSWORD","societies@123#");	
		define("DB_DATABASE","attuit_societies");	
		
		define("DB_HOST_ROOT","localhost");
		define("DB_USER_ROOT","attuit_societies");	
		define("DB_PASSWORD_ROOT","societies@123#");	
		define("DB_DATABASE_ROOT","attuit_societies");	
	}
	else
	{
		//echo "set is else";
		define("DB_HOST","localhost");
		define("DB_USER","root");
		define("DB_PASSWORD","aws123");
		define("DB_DATABASE",$_SESSION['dbname']);
		
		define("DB_HOST_ROOT","localhost");
		define("DB_USER_ROOT","root");
		define("DB_PASSWORD_ROOT","aws123");
		define("DB_DATABASE_ROOT","hostmjbt_societydb");
		
		define("DB_HOST_SM","localhost");
		define("DB_USER_SM","root");
		define("DB_PASSWORD_SM","aws123");
		define("DB_DATABASE_SM",$_SESSION['security_dbname']);
		
		define("DB_HOST_SMROOT","localhost");
		define("DB_USER_SMROOT","root");
		define("DB_PASSWORD_SMROOT","aws123");
		define("DB_DATABASE_SMROOT","security_rootdb");
	}
}
?>