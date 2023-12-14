
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>W2S - Display data</title>
</head>


<?php if(!isset($_SESSION)){ session_start(); 

}
include("read_daybook.php");
$read_daybook=new read_daybook();

include("read_master_daybook.php");
include("classes/dbconst.class.php");
$read_master_daybook=new read_master_daybook();
		include_once ("classes/import_society.class.php") ;
		include_once("classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_import=new import($dbConnRoot,$dbConn);

$result_table='';


 ?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
</html>
<?php
    include_once("includes/head_s.php");
    require_once("classes/CsvOperations.class.php");
	
$flag=$_REQUEST['flag'];
$upload=$_POST['Upload'];
$select=$_POST['data'];

 $validator = new CsvOperations();
 if (isset($_POST["Upload"]))
    {
		
$xml=array('application/vnd.xml','text/xml');		
$mimes = array('application/vnd.ms-excel','text/csv','text/tsv');
	
		$name=$_FILES['file']['name'][0];
		$tmpName = $_FILES['file']['tmp_name'][0];
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$error = $_FILES['file']['error'][0];
		$type=$_FILES['file']['type'][0];
		$new_ledger=$_POST['import_ledger'];
		$BankID = $_POST['bank_id'];
		$opening_year1 = $_POST['opening_year'];
		$NoteType = $_POST['NoteType'];
		
		if(empty($BankID))
		{
			$BankID = -1;
		}
		
		if(!empty($BankID))
		{
			include_once("classes/utility.class.php");
			$obj_utility = new utility($dbConn);
			$BankName = $obj_utility->getLedgerName($BankID);
		}
		
		
		if ($error != 0)
		{
			switch ($error)
			{
				case 1:
					echo '<p> The file is bigger than this PHP installation allows</p>';
					// $result = '<p> The file is bigger than this PHP installation allows</p>';
					break;
				case 2:
				   echo '<p> The file is bigger than this form allows</p>';
				   // $result = '<p> The file is bigger than this form allows</p>';
				   break;
				case 3:
                       echo '<p> Only part of the file was uploaded</p>';
                       // $result = '<p> Only part of the file was uploaded</p>';
                       break;
                case 4:
                       echo '<p> No file was uploaded</p>';
                       // $result = '<p> No file was uploaded</p>';
                   break;
            }
        }
		if(in_array($type,$xml))
		{
			
			$xmlfileData=simplexml_load_file($tmpName);
		}
		
		elseif(in_array($type,$mimes))
		{
			
			$fileData = $validator->readDataOfCsv($tmpName);
			
			if($_POST['flag']==11)
			{
			
			  $Column_array = array('Date', 'Cheque No', 'Description', 'Debit', 'Credit', 'Balance');	
			  $FirstRow = $fileData[0];
			  
			  
			  for($i = 0; $i < 6 ; $i++)
			  {
				  if(!in_array($Column_array[$i],$FirstRow))
				  { ?>
                        <script>alert("<?php echo $Column_array[$i]; ?> column is missing in excel file.");</script>
                  <?php
				  		include('import_bank_statement.php');
					}
			   }
			}
			$_SESSION['file_data'] = $fileData;
		}
		else
		{
  		echo '<script language="javascript">';
		echo 'alert("Only CSV and Excel Files Can Be Uploaded!")';

		echo '</script>';

		if($_POST['flag']==6)
		{
			//header("Location: " . $actionPage);
			include('import_society.php');
		}
		elseif($_POST['flag']==1)
		{
			include('import_ledger.php');
		}
		elseif($_POST['flag']==2)
		{
			include('import_member.php');
		}
		elseif($_POST['flag']==5)
		{
			include('import_invoice.php');
		}
		elseif($_POST['flag']==4)
		{
			include('import_sc.php');
		}
		elseif($_POST['flag']==7)
		{
			include('import_nomination.php');
		}
		elseif($_POST['flag']==3)
		{
			include('import_ser_prd.php');
		}
		elseif($_POST['flag']==8)
		{
			include('import_daybook.php');
		}
		elseif($_POST['flag']==9)
		{
			include('import_master_daybook.php');
		}
		elseif($_POST['flag']==11)
		{
			include('import_bank_statement.php');
		}
		elseif($_POST['flag']==12)
		{
			include('import_lien.php');
		}
		elseif($_POST['flag']==13)
		{
			include('import_fixed_deposit.php');
		}
		elseif($_POST['flag']==14)
		{
			include('import_share_certificate.php');
		}
		elseif($_POST['flag']==15)
		{
			include('import_vehicleParking.php');
		}
        elseif($_POST['flag']==17)
		{
			include('import_tenant_data.php');
		}
	}
 }
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery_min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script language="javascript" type="text/javascript">
function go_error()
{
    setTimeout('hide_error()',10000);   
}
function hide_error()
{
    document.getElementById('error').style.display = 'none';
}
    
$(document).ready(function () {
    
    var flag = <?php echo $_POST['flag']; ?>;
	
    if (flag == 1)
    {
        for (let j = 0; j < 6; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_ledger.php";
        });   
    }
    else if (flag==6)
    {
        for (let j = 0; j < 26; j++)
        {
			if(j===0 || j===1 || j===2 || j===7 )
			{
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
			}
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_society.php";
        });   
   }
    else if (flag==8)
    {
       
        $("#Cancel").click(function() {
            window.location.href = "import_daybook.php";
       	 });   
   }
    else if (flag==9)
    {
		
       
        $("#Cancel").click(function() {
            window.location.href = "import_master_daybook.php";
       	 });   
   }
    else if(flag==4)
    {
        for (let j = 0; j <= 5; j++)
        {
			
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
			
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_sc.php";
        });
    }
	else if(flag==5)
	{
	
        for (let j = 0; j <=30 ; j++)
        {
			if(j==0 || j==1  || j==2 )
			{
				document.getElementsByClassName('columns')[j].checked = true;
            	document.getElementsByClassName('columns')[j].disabled = true;
			}
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_invoice.php";
        });   	
	}
	else if(flag==2)
	{
	
        for (let j = 0; j <=30 ; j++)
        {
			if(j==0 || j==1  || j==2 )
			{
				document.getElementsByClassName('columns')[j].checked = true;
            			document.getElementsByClassName('columns')[j].disabled = true;
			}
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_member.php";
        });   	
	}
	
	else if(flag==7)
	{
	
        for (let j = 0; j <=15 ; j++)
        {
			
		    document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
			
		}
        
        $("#Cancel").click(function() {
            window.location.href = "import_nomination.php";
        });   	
	}
    else if(flag==3)
    {
        for (let j = 0; j < 3; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_ser_prd.php";
        });
    }
	else if(flag == 10)
	 {
        for (let j = 0; j < 5; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_invoice.php?Note";
        });
    }
	else if(flag == 11)
	 {
        for (let j = 0; j < 7; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
         var ledgerID = <?php echo $BankID; ?>;
		
        $("#Cancel").click(function() {
            window.location.href = "import_bank_statement.php?LedgerID="+ledgerID;
        });
    }
    else if(flag == 12)
	 {
        for (let j = 0; j < 9; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_lien.php?Note";
        });
    }
    else if(flag == 13)
	 {
        for (let j = 0; j < 12; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_fixed_deposit.php?Note";
        });
    }
    else if(flag == 14)
	{
        for (let j = 0; j < 6; j++)
        {
            document.getElementsByClassName('columns')[j].checked = true;
            document.getElementsByClassName('columns')[j].disabled = true;
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_share_certificate.php?Note";
        });
    }
	else if(flag==15)
	{
	
        for (let j = 0; j <=10 ; j++)
        {
			if(j==0 || j==1  || j==2 )
			{
				document.getElementsByClassName('columns')[j].checked = true;
            			document.getElementsByClassName('columns')[j].disabled = true;
			}
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_vehicleParking.php";
        });   	
	}
    else if(flag==17)
	{
	
        for (let j = 0; j <16 ; j++)
        {
			if(j==0 || j==1 || j==2 || j==3 || j==4 || j==5 || j==6 || j==7 || j==8 || j==9 || j==10 || j==11 || j==12 || j==13 || j==14 || j==15)
			{
				document.getElementsByClassName('columns')[j].checked = true;
            			document.getElementsByClassName('columns')[j].disabled = true;
			}
        }
        
        $("#Cancel").click(function() {
            window.location.href = "import_tenant_data.php";
        });   	
	}
});
    
function selectColumns(source) 
{
    checkboxes = document.getElementsByClassName('columns');
    countOfCheckBoxes = [];

    for (let i = 0; i < checkboxes.length; i++) 
    {
        if (checkboxes[i].checked === true)
        {
            countOfCheckBoxes[i] = i;
        }
    }

    // alert(countOfCheckBoxes);
    
    document.getElementById("index").value = countOfCheckBoxes;
}
    
    
function sendData(isValidate = 0) 
{
	checkboxes = document.getElementsByClassName('columns');
    countOfCheckBoxes = [];

    for (let i = 0; i < checkboxes.length; i++) 
    {
        if (checkboxes[i].checked === true)
        {
            countOfCheckBoxes[i] = i;
        }
    }
	document.getElementById("index").value = countOfCheckBoxes;
	var data = document.getElementById("index").value;
    var dataString = data.toString();
    $("input[type=hidden][name=data]").val(dataString);
    if(isValidate == 1)
    {
        $("#validateButton").click();
    }
    else
    {
        $("#submitButton").click();    
    }
    
}
function validateData()
{
    // $("#validateButton").click();
    var postData = $('#import_fixed_deposit_data').serialize();
    var formURL = $('#import_fixed_deposit_data').attr("action");
    // alert(formURL);
    $.ajax({
        url:formURL,
        method:"POST",
        data:postData,
        success:function(data)
        {
            console.log(data);
            var result = data.split('@@@');
            // console.log(result);
            window.open(result[1], "_blank");
            // console.log(data);
        }
    });
}
</script>
</head>

<body onLoad="go_error();">
<?php
    if ($_POST['Upload'])
    {
        if (isset($_POST['flag']))
        {
            if ($_POST['flag'] == 1)
            {
?>
        <form name="import_ledger_data" id="import_ledger_data" action="process/import_ledger.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
           	else if($_POST['flag'] == 2) 
            {
?>
        <form name="import_member_data" id="import_member_data" action="process/import_member.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
			else if($_POST['flag'] == 3)
			{
?>
         <form name="service_prov_import_data" id="service_prov_import_data" action="process/import_ser_prd.process.php" method="post" enctype="multipart/form-data" >
<?php
			}
			else if($_POST['flag'] == 8)
			{
?>
         <form name="import_daybook_data" id="import_daybook_data" action="process/import_daybook.process.php" method="post" enctype="multipart/form-data" >
<?php
			}
			else if($_POST['flag'] == 9)
			{
				
?>
         <form name="import_master_daybook_data" id="import_master_daybook_data" action="process/import_master_daybook.process.php" 
         method="post" enctype="multipart/form-data" >
<?php
			}else if ($_POST['flag'] == 6 )
            {	
				$_SESSION['Cycle']=$_POST['Cycle'];
				$_SESSION['eperiod']=$_POST['eperiod'];
				$_SESSION['Year']=$_POST['Year'];
				$_SESSION['society_name']=$_POST['society_name'];
				$_SESSION['Period']=$_POST['Period'];
				$_SESSION['society_code']=$_POST['society_code'];
				$_SESSION['int_rate']=$_POST['int_rate'];
				$_SESSION['int_method']=$_POST['int_method'];
				$_SESSION['rebate_method']=$_POST['rebate_method'];
				$_SESSION['rebate']=$_POST['rebate'];
				$_SESSION['chq_bounce_charge']=$_POST['chq_bounce_charge'];
				
				
?>
        <form name="import_society_data" id="import_society_data" action="process/import_society.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
            elseif ($_POST['flag'] == 4)
            {
?>
        <form name="import_sc_data" id="import_sc_data" action="process/import_sc.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
			 elseif ($_POST['flag'] == 5 )
			{
?>
        <form name="import_invoice_data" id="import_invoice_data" action="process/import_invoice.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
			  elseif ($_POST['flag'] == 2)
            {
?>
        <form name="import_member_data" id="import_member_data" action="process/import_member.process.php" method="post" enctype="multipart/form-data" >
<?php
            }

			
			  elseif ($_POST['flag'] == 7)
            {
?>
        <form name="import_nomination_data" id="import_nomination_data" action="process/import_nomination.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
			elseif ($_POST['flag'] == 10)
			{
?>
       	 <form name="import_credit_data" id="import_credit_data" action="process/import_creditnote.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
			elseif ($_POST['flag'] == 11)
			{
?>
       	 <form name="import_bank_statement" id="import_bank_statement" action="process/import_bank_reconciliation.process.php" method="post" enctype="multipart/form-data" >
<?php
            }elseif($_POST['flag'] == 12)
            {
?>
            <form name="import_lein_data" id="import_lein_data" action="process/import_lien.process.php" method="post" enctype="multipart/form-data" >

<?php 		}elseif($_POST['flag'] == 13)
            {
?>
            <form name="import_fixed_deposit_data" id="import_fixed_deposit_data" action="process/import_fixed_deposit.process.php" method="post" enctype="multipart/form-data" class="fd" >

<?php 		}elseif($_POST['flag'] == 14)
            {
?>
            <form name="import_share_certificate_data" id="import_share_certificate_data" action="process/import_share_certificate.process.php" method="post" enctype="multipart/form-data" >

<?php 		}
		 elseif ($_POST['flag'] == 15)
            {
?>
        <form name="import_vehicleParking_data" id="import_vehicleParking_data" action="process/import_vehicleParking.process.php" method="post" enctype="multipart/form-data" >
<?php
            }
        elseif ($_POST['flag'] == 17)
        {
        ?>
        <form name="import_tenant_data" id="import_tenant_data" action="process/import_tenant_data.process.php" method="post" enctype="multipart/form-data" >
        <?php
        }
        }  
    }
?>    
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
    <?php
        if ($_POST['Upload'])
        {
            if (isset($_POST['flag']))
            {
                if ($_POST['flag'] == 1)
                {

    ?>
        <div class="panel-heading" id="pageheader">Update Ledgers Data</div>

    <?php
                }
                else if($_POST['flag'] == 2)
                {
    ?>
        <div class="panel-heading" id="pageheader">Update Members Data</div>
	 <?php
                }
				else if($_POST['flag'] == 15)
                {
    ?>
        <div class="panel-heading" id="pageheader">Update Vehicle Data</div>
    <?php
                }
				else if($_POST['flag'] == 3)
				{
		?>			
					<div class="panel-heading" id="pageheader">Import Service Provider</div>
		<?php		}      else   if ($_POST['flag'] == 6 )
                {

    ?>
        <div class="panel-heading" id="pageheader">Import Society Data</div>
    <?php
                }
				else   if ($_POST['flag'] == 8 )
                {

    ?>
        <div class="panel-heading" id="pageheader">Import DayBook Data</div>

    <?php
                }
				else   if ($_POST['flag'] == 9)
                {

    ?>
    
        <div class="panel-heading" id="pageheader">Import Master DayBook Data</div>



    <?php
                }
                elseif($_POST['flag'] == 4)
                { 
    ?>
        <div class="panel-heading" id="pageheader">Import Share Certficate</div>
    <?php
                }
				elseif($_POST['flag'] == 5)
				{?>
					 <div class="panel-heading" id="pageheader">Import Invoice</div>
				<?php
				
				 }
				elseif($_POST['flag'] == 2)
				{?>
					 <div class="panel-heading" id="pageheader">Update Member Data</div>
				<?php 
				
				}
					elseif($_POST['flag'] == 15)
				{?>
					 <div class="panel-heading" id="pageheader">Import Vehicle Data</div>
				<?php 
				
				}
				elseif($_POST['flag'] == 7)
				{?>
					 <div class="panel-heading" id="pageheader">Import Nomination Data</div>
				<?php 
				
				}
				elseif($_POST['flag'] == 10)
				{
					if($_POST['NoteType'] == CREDIT_NOTE)
					{ ?>
						 <div class="panel-heading" id="pageheader">Import Credit Note Data</div>                    
    				<?php }
					else if($_POST['NoteType'] == DEBIT_NOTE)
					{?>
						 <div class="panel-heading" id="pageheader">Import Debit Note Data</div>				
					<?php }?>

				<?php 
				
				}
				elseif($_POST['flag'] == 11)
				{?>
					 <div class="panel-heading" id="pageheader">Import Bank Statement (<?php echo $BankName;?>)</div>
				<?php 
				
				}elseif($_POST['flag'] == 12)
				{?>
					 <div class="panel-heading" id="pageheader">Import Lien</div>
				<?php 
				
				}elseif($_POST['flag'] == 13)
				{?>
					 <div class="panel-heading" id="pageheader">Import Fixed Deposit</div>
				<?php 
				
				}elseif($_POST['flag'] == 14)
				{?>
					 <div class="panel-heading" id="pageheader">Import Share Certificate</div>
				<?php 
				}
                else if($_POST['flag'] == 17)
                {?>
                    <div class="panel-heading" id="pageheader">Import Tenant Data</div>
                <?php 
                }
            }  
        }
    ?>
        <div id="right_menu">
<br>
<div id="table-wrapper" 
    style="border: 1px solid black;
        width: 1030px;
        height: 380px;
        overflow: auto;">
 <?php
		$configData='';
		if(in_array($type,$xml))
		{
			if($_POST['flag']==8)
			{
				
				$result_table = $read_daybook->readDataBook($xmlfileData);
				echo $result_table;
			}
			if($_POST['flag']==9)
			{
				$result_master_table = $read_master_daybook->readDataBook($xmlfileData);
				echo $result_master_table;
			}
		
		}
		else
		{
			/*$errorLines = $validator->validateCSV($tmpName);
       		if ($errorLines == NULL)
        	{*/
            $data = array();
            $rows = $validator->getRows ($tmpName);
            $columns = $validator->getColumns ($tmpName);
            $contents = $validator->readDataOfCsv($tmpName);
            array_unshift($contents, null);

            for ($i = 0; $i < count($contents); $i++)
            {
                array_unshift($contents[$i], null);
            }
            // var_dump($contents);
            echo '<center><table style = "margin:0 auto;
                            width:95%;
                            margin: 6px;
                            overflow:auto;
                            font-family: helvetica,arial,sans-serif;
                            font-size:14px;
                            color:#333333;
                            border-width: 1px;
                            border-color: #666666;
                            border-collapse: collapse;
                            text-align: center;">';

            for ($i = 0; $i <= $rows; $i++)
            {
                echo '<tr>';
                for ($j = 0; $j <= $columns; $j++)
                {
                    if ($i == 0)
                    {
                        if ($j !== $columns)
                        {
                            echo '<td style="border-width: 1px;
                                padding: 8px;
                                border-style: solid;
                                vertical-align: center;
                                border-color: #666666;">
                                    <input type="checkbox" class="columns" onClick="selectColumns(this)">
                                </td>';
                        }
                        else
                        {
                                echo '<td style="border-width: 1px;
                                padding: 8px;
                                vertical-align: center;
                                border-color: #666666;">
                                    <input type="checkbox" style="display: none" class="columns" onClick="selectColumns(this)">
                                </td>';
                        }
                    }
                    else 
                    { 
                        if ($j == 0)
                        {
                            continue;
                        }
                        else 
                        {
                            echo '<td style="border-width: 1px;
                                    padding: 8px;
                                    vertical-align: center;
                                    border-style: solid;
                                    border-color: #666666;">'.$contents[$i][$j]. '</td>';
                        }
                    } 
                }
                echo '</tr>';
            }
            echo "</table></center>";    
        /*}
        else
        {
            $errorLines = $validator->validateCSV($tmpName);
            $validator->displayIntoTables($tmpName, $errorLines);
        }*/
		}
 ?>
    </div>
    </div>
    </center>
    
    <p id="index" style="display:none;" ></p>

<input type="hidden" name="tmpName" value="<?php echo $tmpName; ?>">
<input type="hidden" name="name" value="<?php echo $name; ?>">
<input type="hidden" name="flag" value="<?php echo $flag; ?>">
<input type="hidden" name="error" value="<?php echo $error; ?>">
<input type="hidden" name="ext" value="<?php echo $ext; ?>">
<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="select" value="<?php echo $select; ?>">
<input type="hidden" name="bankid" value="<?php echo $BankID; ?>">

<input type="hidden" name="NoteType" value="<?php echo $NoteType; ?>">

<input type="hidden" name="new_ledger" value="<?php echo $new_ledger; ?>">

<input type="hidden" name="opening_year1" value="<?php echo $opening_year1; ?>">
<input type="hidden" name="validate" value="1">

<input type="hidden" name="data">
<input type="button" style="float: right;" name="Cancel" id="Cancel" value="Cancel" class="btn btn-danger">

<?php 

if(in_array($_FILES['file']['type'][0],$xml))
{
?>   
<input type="submit" style="margin-right: 6px; float: right" name="submit" id="Import" value="Import" class="btn btn-primary">   
<?php 
}
else
{
	
	?>
    <?php if($_POST['flag'] == 13) { ?>
     <input type="button" onclick="validateData()" id="Validate" name="Validate" value="Validate" style="margin-right:4px; float: right" class="btn btn-success">
    <?php }?>
<input type="button" onclick="sendData(0)" style="margin-right: 6px; float: right" name="Import" id="Import" value="Import" class="btn btn-primary">

<input type="submit" style="display: none;" id="submitButton" name="submit">
<?php if($_POST['flag'] == 13) { ?>
<input type="button" style="display: none;" id="validateButton" name="validate">
<?php }?>
<?php

}
?>
</form>
<?php

 include_once "includes/foot.php";

 ?>
</body>
</html>