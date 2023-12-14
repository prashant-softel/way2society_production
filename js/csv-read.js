var csvData = [];
var unitInterests = [];
var unitCSGT = [];
var unitSGST = [];
var units = [];

function parseUnits(jsonUnits) {
  units = jsonUnits;
}

function handleFiles(files) {
  if (window.FileReader) {
    getAsText(files[0]);
  } else {
    alert("File Reading is not supported in this browser.");
  }
}

function getAsText(fileToRead) {
  var reader = new FileReader();
  reader.readAsText(fileToRead);
  reader.onload = loadHandler;
  reader.onerror = errorHandler;
}

function changeInput(i) {
	
  	document.getElementById("Interest" + i).style.backgroundColor = "#ff000080";
}

function loadHandler(event) {
  var csv = event.target.result;
  
  csvData = CSVToArray(csv, ",");
  //console.log(csvData);
  //Here we are chcking request come from which page 
  var IsgstUpdatePage  = document.getElementById('IsgstUpdatePage').value;
  
  //Finding the index of flat colunm passing colunm name and units need to update
  var unitIndex = getIndex(csvData,"Fcode");
  var unitArray = getArrayValue(csvData, unitIndex);
  
  //According to page request code executes
  if(IsgstUpdatePage == true)
  {
	//Declaring the array to store data  
	var MainData = [];  
	var CGSTData = [];
	var SGSTData = [];
	
	//Here is Cgst Data 
	var CsgtIndex = getIndex(csvData,'CGST');
	unitCGST = getArrayValue(csvData, CsgtIndex);
	
	//Pushing the data into mainData Array
	CGSTData = ['CGST', unitCGST];
	MainData.push(CGSTData);

	//Here is Sgst Data 
	var SgstIndex = getIndex(csvData,'SGST'); 
	unitSGST = getArrayValue(csvData, SgstIndex); 
	
	//Pushing the data into mainData Array
	SGSTData = ['SGST',unitSGST]
	MainData.push(SGSTData);
	
	for(var x = 0; x < MainData.length; x++)
	{
		for(var i = 0; i < units.length; i++)
		{
			//Finding the row in which we have to set
			var index = $.inArray(units[i], unitArray);

			if (index != -1) 
			{
				document.getElementById(MainData[x][0] + i).value = MainData[x][1][index];
				document.getElementById(MainData[x][0] + i).style.backgroundColor = "yellow";
			}
	   }	
	}
  }
  else
  {
	 var billInterestIndex = getIndex(csvData,'BillInterest');
	 unitInterests = getArrayValue(csvData, billInterestIndex);
	 
		for(var i = 0; i < units.length; i++) {
		var index = $.inArray(units[i], unitArray);

		if (index != -1) {
			document.getElementById("Interest" + i).value = unitInterests[index];
			document.getElementById("Interest" + i).style.backgroundColor = "yellow";
		}
	  } 
  }
}

function errorHandler(evt) {
  if (evt.target.error.name == "NotReadableError") {
    alert("Cannot read file !");
  }
}

function CSVToArray(strData, strDelimiter) {
  strDelimiter = strDelimiter || ",";

  var objPattern = new RegExp(
    "(\\" +
      strDelimiter +
      "|\\r?\\n|\\r|^)" +
      '(?:"([^"]*(?:""[^"]*)*)"|' +
      '([^"\\' +
      strDelimiter +
      "\\r\\n]*))",
    "gi"
  );

  var arrData = [[]];

  var arrMatches = null;

  while ((arrMatches = objPattern.exec(strData))) {
    var strMatchedDelimiter = arrMatches[1];

    if (strMatchedDelimiter.length && strMatchedDelimiter !== strDelimiter) {
      arrData.push([]);
    }

    var strMatchedValue;

    if (arrMatches[2]) {
      strMatchedValue = arrMatches[2].replace(new RegExp('""', "g"), '"');
    } else {
      strMatchedValue = arrMatches[3];
    }

    arrData[arrData.length - 1].push(strMatchedValue);
  }
  return arrData;
}

function getIndex(array,colunmName) {
	
	return array[0].indexOf(colunmName);
}

function getArrayValue(array, colunmIndex) {
  var colunmArray = [];	
  var j = 0;
  for (var i = 1; i < array.length - 1; i++) {
    colunmArray[j] = array[i][colunmIndex];
    j++;
  }

  return colunmArray;
}
/*
function getIndexOfUnit(array) {
  return array[0].indexOf("Fcode");
}*/

/*function getArrayOfUnit(array, unitIndex) {
  var unitsArray = [];
  var j = 0;
  for (var i = 1; i < array.length - 1; i++) {
    unitsArray[j] = array[i][unitIndex];
    j++;
  }

  return unitsArray;
}*/
