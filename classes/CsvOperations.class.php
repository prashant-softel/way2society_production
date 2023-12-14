<?php

include_once ("include/DbConnection.class.php");
include_once ("include/config.php");

class CsvOperations
{
    public $isValid = false;
    public $correctCommas = false;
    public $errorLineNumbers = array();

    public function readDataOfCsv ($file)
    {
        //Read the data of the file & returns the file contents as an array
        $csv = file_get_contents($file);
        // var_dump($csv);
        $array = array_map("str_getcsv", explode("\n", $csv));
        return $array;
    }

    public function readRawContents ($file)
    {
        //Reads a file and return the raw contents
        $csv = file_get_contents($file);
        return $csv;
    }

    public function getRows ($csv_file)
    {
        //Returns the total rows in a CSV File
        $numberOfRows = 0;
        if (($file = fopen($csv_file, "r")) !== FALSE)
        {
            while ($rows = fgetcsv($file, ",", '"'))
            {
                // $rows[0] = 'c';
                $numberOfRows++;
            }
        }
        // $rows[1] = $numberOfRows;
        // array_unshift($numberOfRows, 0);
        return $numberOfRows;
    }

    public function getColumnHeader ($csv_file)
    {
        //Returns the column names as an array
        $firstRow = array();

        if (($file = fopen($csv_file, "r")) !== FALSE)
        {
            $firstRow = fgetcsv($file, 0, ",", '"');
        }

        return $firstRow;
    }

    public function getColumns ($csv_file)
    {
        //Returns number of columns in CSV File
        $numberOfColumns = 0;
        $columns = array();

        if (($file = fopen($csv_file, "r")) !== FALSE)
        {
            $columns = $this->getColumnHeader($csv_file);
            $numberOfColumns = $numberOfColumns + count($columns);
        }

        return $numberOfColumns;
    }

    public function checkingCommas ($csv_file)
    {
        //Reads a CSV file and returns the number of commas in each row(index) in an array
        $commas = array();
        $temp = array();

        $rows = $this->getRows($csv_file);
        $columns = $this->getColumns($csv_file);

        $data = $this->readRawContents($csv_file);
        $rowsArray = split("\n", $data);

        for ($i = 0; $i < $rows; $i++)
        {
            for ($j = 0; $j < $columns; $j++)
            {
                $commas[$i][$j] = substr_count($rowsArray[$i], ",");
                //echo 'Row: ' . $i . "\t". 'Count: ' . $commas[$i][$j] . '<br>';
                $temp[$i] = $commas[$i][$j];
            }
        }

        for($i = 0; $i < $rows; $i++)
        {
            if ($temp[$i] < ($columns - 1) || $temp[$i] > ($columns - 1))
            {
                $lineNumber[$i] = $temp[$i];
            }
        }

        $unique = array_values(array_unique($temp));

        if (($unique[0] == $columns - 1) && $unique[1] === NULL)
        {
            $correctCommas = true;
        }
        else
        {
            $correctCommas = false;
        }

        return $lineNumber;
    }

    public function displayIntoTables($csv_file, $lineNumbers = null)
    {
        //Takes the rows, columns, contents of a file and display into a table
        $rows = $this->getRows ($csv_file);
        $columns = $this->getColumns ($csv_file);
//        var_dump($rows);
//       var_dump($columns);

        $contents = $this->readDataOfCsv($csv_file);

        if (!empty($lineNumbers))
            $lines = array_keys($lineNumbers);

        if (count ($contents) > 0)
        {
            // if (count($lines) == 0)
            // {
            //     echo '<br><b><p align = "center" style = "font-size: 1.6em;">There are no invalid records </b>' . '<br><br>';
            //     echo '<input type = "checkbox" id="select-all" onClick="selectAllValidRows(this)"> Select All </p>';
            // }
            // else if (count($lines) == 1)
            // {
            //     echo '<b><p align = "center" style = "font-size: 1.6em;"> There is a invalid record at line ' . $lines[0] . ' </b><br><br>';
            //     echo '<input type = "checkbox" onClick="selectAllRows(this)"> Select All <br>';
            //     echo '<input type = "checkbox" name = "error-select-all" onClick="selectErrorRows(this)">Select error row</p>';
            // }
            // else
            // {
            //     echo '<b><p align = "center" style = "font-size: 1.6em;"> There are '. count($lines) . ' invalid records' . ' </p></b><br><br>';
            //     echo '<input type = "checkbox" onClick="selectAllRows(this)"> Select All';
            //     echo '<input type = "checkbox" name= "error-select-all" onClick="selectErrorRows(this)">Select error rows</p>';
            // }

            echo '<table style = "margin:0 auto;
                            width:95%;
                            padding: 0.5em 0px 0px 0px;
                            overflow:auto;
                            font-family: helvetica,arial,sans-serif;
                            font-size:14px;
                            color:#333333;
                            border-width: 1px;
                            border-color: #666666;
                            border-collapse: collapse;
                            text-align: center;">';

            for ($i = 0; $i < $rows; $i++)
            {
                echo '<tr>';
                for ($j = 0; $j < $columns; $j++)
                {
                    if (in_array($i, $lines))
                    {
                        echo '<td style="background-color: red;
                                color: white;
                                border-width: 1px;
                                padding: 8px;
                                border-style: solid;
                                border-color: #666666;">'.$contents[$i][$j].'</td>';               
                    }
                    else
                    {
                        if ($i == 0)
                        {
                            // echo '';
                            echo '<th style="border-width: 1px;
                                    padding: 8px;
                                    border-style: solid;
                                    border-color: #666666;">'.$contents[$i][$j]. '</th>';
                        }
                        else
                        {
                            echo '<td style="border-width: 1px;
                                padding: 8px;
                                border-style: solid;
                                border-color: #666666;">'.$contents[$i][$j]. '</td>';
                        }
                    } 
                }
                echo '</tr>';
            }
            echo "</table>";
        }
    }

    public function validateCSV ($csv_file)
    {
        //Reads a CSV file and verifies that the file is valid or not
        //Return the validation in terms of null or line numbers of the error rows

        $errorLineNumbers = $this->checkingCommas($csv_file);

        if(empty($errorLineNumbers))
            $isValid = true;
        else
            $isValid = false;

        if ($isValid == TRUE)
        {
            return null;
        }
        else
        {
            return $errorLineNumbers;
        }
    }

    public function getColumnData($file)
    {
        //Returns data of columns
        $array = $this->readDataOfCsv($file);
        $rows = $this->getRows($file);
        $columns = $this->getColumns($file);
        $columnData = array();
        $data = array();

        for ($i = 0; $i < $columns; $i++)
        {
            for ($j = 0; $j < $rows; $j++)
            {
                $columnData[$i][$j] = $array[$j][$i]; 
            }
        }

        return $columnData;
    }

    public function getColumnDataByIndex($file ,$columnIndex)
    {
        //Returns data of column specified by columnIndex
        $data = array();
        $columnsData = $this->getColumnData($file);
        $rows = $this->getRows($file);

        for ($i = 0; $i < $rows; $i++)
        {
            if ($columnsData[$columnIndex][$i] !== NULL)
            {
                $data[$i] = $columnsData[$columnIndex][$i];
            }
        }
        // var_dump($data);
        return $data;
    }

    public function getColumnDataByName($file, $name)
    {
        //Returns data of column specified by columnIndex
        $data = array();
        $columnsData = $this->getColumnData($file);
        $rows = $this->getRows($file);
        $columns = $this->getColumns($file);
        $j = 0;
        $column = array();
        // var_dump($columnData);

        for ($i = 0; $i < $columns; $i++)
        {
            if (strcmp($columnsData[$i][0], $name) == 0)
            {
                $data[$j] = $columnsData[$i];
                $j++;
            }
        }

        var_dump($data);

        for ($i = 1; $i < $rows; $i++)
        {
            $column[$i] = $data[0][$i];
        }
        return $column;
    }

    public function getRowDataByIndex($file, $rowIndex)
    {
        $rowsData = $this->readDataOfCsv($file);
        $data = array();

        // var_dump($rowsData);
        for ($i = 0; $i < $this->getColumns($file); $i++)
        {
            if ($rowsData[$rowIndex][$i] !== NULL)
            {
                $data[$i] = $rowsData[$rowIndex][$i];
            }
        }
        // var_dump($data);
        

        return $data;
    }
}

?>
