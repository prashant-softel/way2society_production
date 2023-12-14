<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>W2S - Professional Listing</title>
</head>

<?php if(!isset($_SESSION)){ session_start(); } 
include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/directory.class.php");

$objDirectory = new mDirectory($m_dbConn);
//$result = $objDirectory->FetchUnits();
?>
<div class="panel panel-info" id="panel" style="margin-top:3.5%;margin-left:3.5%; border:none;width:70%">
  <div class="panel-heading" id="pageheader" style="font-size:20px">Professional Listing</div>
    <!--<div class="panel-body">-->
      <center>
        <table align="center" border="0" width="100%">
          <tr>
            <td valign="top" align="center"><font color="red"><?php if(isset($_GET['del'])){echo "<b id=error_del>Record deleted Successfully</b>";}else{echo '<b id=error_del></b>';} ?></font></td>
          </tr>
          <tr>
            <td>
              <?php
                echo "<br>";
                echo $str1 = $objDirectory->ProfessionalListings();
              ?>
            </td>
          </tr>
        </table>
      </center>
    </div>
  <!--</div>-->
</div>
<script>
/* Formating function for row details */
/*function fnFormatDetails ( oTable, nTr )
{
	
    var aData = oTable.fnGetData( nTr );
	//alert(aData);
	//alert(document.getElementById(aData[4].id).innerHTML);
	//alert($.parseHTML(aData[4]));
	//alert($('<div/>').html(aData[4]).text() );
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
  	sOut += '<tr><td>Mobile No.:</td><td>'+aData[5]+'</td></tr>';
	sOut += '<tr><td>Email Id:</td><td>'+$('<div/>').html(aData[6]).text()+'</td></tr>';
    sOut += '</table>';
     
    return sOut;
}
 
$(document).ready(function() {
	
	
	 $('#example').dataTable(
    {
	   "bDestroy": true
    }).fnDestroy();
    /*
     * Insert a 'details' column to the table
     */
  /*  var nCloneTh = document.createElement( 'th' );
    var nCloneTd = document.createElement( 'td' );
    nCloneTd.innerHTML = '<img src="imagess/button_a.jpg">';
    nCloneTd.className = "center";
     
    $('#example thead tr').each( function () {
        this.insertBefore( nCloneTh, this.childNodes[0] );
    } );
     
    $('#example tbody tr').each( function () {
        this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
    } );
     
    /*
     * Initialse DataTables, with no sorting on the 'details' column
     */
  /* var oTable = $('#example').dataTable( {
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 0 ] }
        ],
        "aaSorting": [[1, 'asc']]
    });
     
    /* Add event listener for opening and closing details
     * Note that the indicator for showing which row is open is not controlled by DataTables,
     * rather it is done here
     */
 /*   $('#example tbody td img').live('click', function () {*/
	/*$('#example tbody ').on( 'click', 'tr td', function () {
        var nTr = $(this).parents('tr')[0];
        if ( oTable.fnIsOpen(nTr) )
        {
            /* This row is already open - close it */
            //this.src = "imagess/button_a.jpg";
           /* oTable.fnClose( nTr ); 
        }
        else
        {
            /* Open this row */
           // this.src = "imagess/button_a.jpg";
           /* oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
        }
    } );
} );*/
</script>
<?php include_once "includes/foot.php"; ?>