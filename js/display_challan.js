function SubmitEntry()
{      
		
   
   	//	var SubmitValue=1; 
		//var arSubmitEntry = [];
		var RedirectDate = document.getElementById('url_date').value;
		var PaidTo = document.getElementById('Paidto').value;
		var GroupID = document.getElementById('GroupID').value;
		var BankID = document.getElementById('BankID').value;
		
		var ChallanDate =document.getElementById('challan_date').value;
		var ChequeAmount = document.getElementById('income_tax').value; 
		var TotalAmount = document.getElementById('total_amount').value;
		var AssesmentYear = document.getElementById('assessmentYear').value;
		var YearID = document.getElementById('YearID').value;
		var Comp_deductees=document.getElementById('comp_deductees').value;
		var Comp_non_deductees=document.getElementById('non_comp_deductees').value;
		var Nature_of_TDS=document.getElementById('Nature_of_TDS').value;
		var TDS_taxPayer=document.getElementById('tds_taxpayer').value;
		var TDS_reg_assess=document.getElementById('tds_reg_assess').value;
		var PaybleData=document.getElementById('data_arr').value;
		var from_date = document.getElementById('from_date').value;
		var to_date = document.getElementById('to_date').value;
		if(confirm("Are you sure you want to submit the entry ?") == true)
		{
			   
		} 
		else 
		{
      		return false;
		}
		
		//console.log(txt);
		var objData = {"PaidTo" : PaidTo, "ChallanDate" : ChallanDate, "TotalAmount" : TotalAmount, "AssesmentYear" : AssesmentYear, "YearID" : YearID, "Comp_deductees" : Comp_deductees,"Comp_non_deductees" : Comp_non_deductees, "Nature_of_TDS" : Nature_of_TDS,"TDS_taxPayer":TDS_taxPayer , "TDS_reg_assess":TDS_reg_assess,'PaybleData' : PaybleData, "from":from_date, "to": to_date,"BankID":BankID,  "method" : 'AddTDSPaymentDetails'}; 
		$.ajax({ 
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{
				var arr = Array();
				arr		= data.split("@@@");
				if(arr[1]==1)
				{
					alert("Record Added Successfully...");
					//window.close();
					//window.opener.location.reload();
					window.location.replace("view_tds_report.php?lid="+PaidTo+"&gid="+GroupID+"&ckdate="+RedirectDate+"&bankid"+BankID);
				}
				else
				{
					alert("Record not updated ...");
				}	
			}
		});
		
		
		//var obj = ""; 	 
		//	obj = {"PaidTo" : PaidTo, "ChequeNumber" : ChequeNumber, "ChequeDate" : ChequeDate, "Amount" : TotalAmount, "PayerBank" : PayerBank,"LeafID" : LeafID, "VoucherDate" : ChequeDate,"AssesmentYear":AssesmentYear , "Comp_deductees":Comp_deductees,"Comp_non_deductees":Comp_non_deductees, "Nature_of_TDS":Nature_of_TDS,"TDS_taxPayer":TDS_taxPayer ,"TDS_reg_assess":TDS_reg_assess,"TdsPayment": SubmitValue };
		//arSubmitEntry.push(obj);
		//console.log(obj);
		//showLoader();		
		//var objData = {'data' : obj, "method" : 'AddTDSPaymentDetails'}; 
		//var objData = {'data' : JSON.stringify(arSubmitEntry),  "method" : 'AddTDSPaymentDetails'}; 
		/*$.ajax({ 
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				var arr = Array();
				arr		= data.split("@@@");
				//alert(data);			
				/*if(arr[1]==true)
				{
					alert("Record Updated Successfully...");
					window.close();
					window.opener.location.reload();
					
				}
				else if(arr[1]== false)
				{
					alert("Record not updated, Cheque number already used in another Cheque Leaf !");	
				}
				else
				{
					alert("Record not updated ...")
				}*/
			/*}
		
	
		
	});*/
   
}

function NumberRsInWOrd(amount){
var words = new Array();
words[0] = 'Zero';words[1] = 'One';words[2] = 'Two';words[3] = 'Three';words[4] = 'Four';words[5] = 'Five';words[6] = 'Six';words[7] = 'Seven';words[8] = 'Eight';words[9] = 'Nine';words[10] = 'Ten';words[11] = 'Eleven';words[12] = 'Twelve';words[13] = 'Thirteen';words[14] = 'Fourteen';words[15] = 'Fifteen';words[16] = 'Sixteen';words[17] = 'Seventeen';words[18] = 'Eighteen';words[19] = 'Nineteen';words[20] = 'Twenty';words[30] = 'Thirty';words[40] = 'Forty';words[50] = 'Fifty';words[60] = 'Sixty';words[70] = 'Seventy';words[80] = 'Eighty';words[90] = 'Ninety';var op;
amount = amount.toString();
var atemp = amount.split('.');
var number = atemp[0].split(',').join('');
var n_length = number.length;
var words_string = '';
if(n_length <= 9){
var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
var received_n_array = new Array();
for (var i = 0; i < n_length; i++){
received_n_array[i] = number.substr(i, 1);}
for (var i = 9 - n_length, j = 0; i < 9; i++, j++){
n_array[i] = received_n_array[j];}
for (var i = 0, j = 1; i < 9; i++, j++){
if(i == 0 || i == 2 || i == 4 || i == 7){
if(n_array[i] == 1){
n_array[j] = 10 + parseInt(n_array[j]);
n_array[i] = 0;}}}
value = '';
for (var i = 0; i < 9; i++){
if(i == 0 || i == 2 || i == 4 || i == 7){
value = n_array[i] * 10;} else {
value = n_array[i];}
if(value != 0){
words_string += words[value] + ' ';}
if((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)){
words_string += 'Crores ';}
if((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)){
words_string += 'Lakhs ';}
if((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)){
words_string += 'Thousand ';}
if(i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)){
words_string += 'Hundred and ';} else if(i == 6 && value != 0){
words_string += 'Hundred ';}}
words_string = words_string.split(' ').join(' ');}
return words_string;}
function RsPaise(n){
nums = n.toString().split('.')
var whole = Rs(nums[0])
if(nums[1]==null)nums[1]=0;
if(nums[1].length == 1 )nums[1]=nums[1]+'0';
if(nums[1].length> 2){nums[1]=nums[1].substring(2,length - 1)}
if(nums.length == 2){
if(nums[0]<=9){nums[0]=nums[0]*10} else {nums[0]=nums[0]};
var fraction = Rs(nums[1])
if(whole=='' && fraction==''){op= 'Zero only';}
if(whole=='' && fraction!=''){op= 'paise ' + fraction + ' only';}
if(whole!='' && fraction==''){op='Rupees ' + whole + ' only';} 
if(whole!='' && fraction!=''){op='Rupees ' + whole + 'and paise ' + fraction + ' only';}
amt=document.getElementById('total_amount').value;
if(amt > 999999999.99){op='Oops!!! The amount is too big to convert';}
if(isNaN(amt) == true ){op='Error : Amount in number appears to be incorrect. Please Check.';}
document.getElementById('op').innerHTML=op;}}
//RsPaise(Math.round(document.getElementById('total_amount').value*100)/100);

