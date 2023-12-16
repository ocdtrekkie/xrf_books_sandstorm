<?php

//Function xrfl_getauthor
//Use: Returns the full name of an author.
function xrfl_getauthor($xrf_db, $id)
{
$query="SELECT name FROM l_authors WHERE id='$id'";
$result=mysqli_query($xrf_db, $query);
$name=xrf_mysql_result($result,0,"name");
return ($name);
}

//Function xrfl_getauthor_years
//Use: Returns the name of an author and their birth and death year, if known.
function xrfl_getauthor_years($xrf_db, $id)
{
$query="SELECT name, years FROM l_authors WHERE id='$id'";
$result=mysqli_query($xrf_db, $query);
$name=xrf_mysql_result($result,0,"name");
$years=xrf_mysql_result($result,0,"years");
if ($years == "") { return ($name); }
else { return ($name . " " . $years); }
}

//Function xrfl_getlocation
//Use: Returns the full name of a location.
function xrfl_getlocation($xrf_db, $code)
{
$query="SELECT descr FROM l_locations WHERE code='$code'";
$result=mysqli_query($xrf_db, $query);
$descr=xrf_mysql_result($result,0,"descr");
return ($descr);
}

//Function xrfl_getperiodical
//Use: Returns the full name of a periodical.
function xrfl_getperiodical($xrf_db, $issn)
{
$query="SELECT title FROM l_periodicals WHERE issn='$issn'";
$result=mysqli_query($xrf_db, $query);
$name=xrf_mysql_result($result,0,"title");
return ($name);
}

//Function xrfl_getstatus
//Use: Returns the full name of a status.
function xrfl_getstatus($xrf_db, $code)
{
$query="SELECT descr FROM l_statuses WHERE code='$code'";
$result=mysqli_query($xrf_db, $query);
$descr=xrf_mysql_result($result,0,"descr");
return ($descr);
}

//Function xrfl_gettype
//Use: Returns the full name of a type.
function xrfl_gettype($xrf_db, $code)
{
$query="SELECT descr FROM l_typecodes WHERE code='$code'";
$result=mysqli_query($xrf_db, $query);
$descr=xrf_mysql_result($result,0,"descr");
return ($descr);
}

//Function xrfl_isbn10checker
//Use: Function accepts either 10 or 9 digit number, and either provides or checks the validity of the 10th checksum digit. Optionally converts to ISBN 13 as well.
//Credit: Roland Tanner @ http://trwa.ca/2012/02/php-isbn-10-to-isbn-13-converter/
function xrfl_isbn10checker($input, $convert = FALSE){
	$output = FALSE;
	if (strlen($input) < 9){
		$output = array('error'=>'ISBN too short.');
	}
	if (strlen($input) > 10){
		$output = array('error'=>'ISBN too long.');
	}
	if (!$output){
		$runningTotal = 0;
		$r = 1;
		$multiplier = 10;
		for ($i = 0; $i < 10 ; $i++){
			$nums[$r] = substr($input, $i, 1);
			$r++;
		}
		$inputChecksum = array_pop($nums);
		foreach($nums as $key => $value){
			$runningTotal += $value * $multiplier;
			//echo $value . 'x' . $multiplier . ' + ';
			$multiplier --;
			if ($multiplier === 1){
				break;
			}
		}
		//echo ' = ' . $runningTotal;
		$remainder = $runningTotal % 11;
		$checksum = $remainder == 1 ? 'X' : 11 - $remainder;
		$checksum = $checksum == 11 ? 0 : $checksum;
		$output = array('checksum'=>$checksum);
		$output['isbn10'] = substr($input, 0, 9) . $checksum;
		if ($convert){
			$output['isbn13'] = xrfl_isbn10to13($output['isbn10']);
		}
		if ((is_numeric($inputChecksum) || $inputChecksum == 'X') && $inputChecksum != $checksum){
			$output['error'] = 'Input checksum digit incorrect: ISBN not valid';
			$output['input_checksum'] = $inputChecksum;
		}
	}
	return $output;
}

//Function xrfl_isbn10hyp
//Use: Hyphenates a 10-digit ISBN.
function xrfl_isbn10hyp($isbn)
{
$isbna = substr($isbn,0,1) . "-";

if (substr($isbn,0,1) == 0)
{
if (substr($isbn,1,2) <= 19)
$isbna = $isbna . substr($isbn,1,2) . "-" . substr($isbn,3,6);

if (substr($isbn,1,3) >= 200 && substr($isbn,1,3) <= 699)
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,4) >= 7000 && substr($isbn,1,4) <= 8499)
$isbna = $isbna . substr($isbn,1,4) . "-" . substr($isbn,5,4);

if (substr($isbn,1,5) >= 85000 && substr($isbn,1,5) <= 89999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,6) >= 900000 && substr($isbn,1,6) <= 949999)
$isbna = $isbna . substr($isbn,1,6) . "-" . substr($isbn,7,2);

if (substr($isbn,1,7) >= 9500000 && substr($isbn,1,7) <= 9999999)
$isbna = $isbna . substr($isbn,1,7) . "-" . substr($isbn,8,1);
}

if (substr($isbn,0,1) == 1)
{
if (substr($isbn,1,2) <= 9) //09
$isbna = $isbna . substr($isbn,1,2) . "-" . substr($isbn,3,6);

if (substr($isbn,1,3) >= 100 && substr($isbn,1,3) <= 399)
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,4) >= 4000 && substr($isbn,1,4) <= 5499)
$isbna = $isbna . substr($isbn,1,4) . "-" . substr($isbn,5,4);

if (substr($isbn,1,5) >= 55000 && substr($isbn,1,5) <= 86979)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,6) >= 869800 && substr($isbn,1,6) <= 998999)
$isbna = $isbna . substr($isbn,1,6) . "-" . substr($isbn,7,2);

if (substr($isbn,1,7) >= 9990000 && substr($isbn,1,7) <= 9999999)
$isbna = $isbna . substr($isbn,1,7) . "-" . substr($isbn,8,1);
}

if (substr($isbn,0,1) == 2)
{
if (substr($isbn,1,2) <= 19)
$isbna = $isbna . substr($isbn,1,2) . "-" . substr($isbn,3,6);

if (substr($isbn,1,3) >= 200 && substr($isbn,1,3) <= 349)
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,5) >= 35000 && substr($isbn,1,5) <= 39999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,3) >= 400 && substr($isbn,1,3) <= 699)
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,4) >= 7000 && substr($isbn,1,4) <= 8399)
$isbna = $isbna . substr($isbn,1,4) . "-" . substr($isbn,5,4);

if (substr($isbn,1,5) >= 84000 && substr($isbn,1,5) <= 89999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,6) >= 900000 && substr($isbn,1,6) <= 949999)
$isbna = $isbna . substr($isbn,1,6) . "-" . substr($isbn,7,2);

if (substr($isbn,1,7) >= 9500000 && substr($isbn,1,7) <= 9999999)
$isbna = $isbna . substr($isbn,1,7) . "-" . substr($isbn,8,1);
}

if (substr($isbn,0,1) == 3)
{
if (substr($isbn,1,2) <= 2) //02
$isbna = $isbna . substr($isbn,1,2) . "-" . substr($isbn,3,6);

if (substr($isbn,1,3) >= 30 && substr($isbn,1,3) <= 33) //030-033
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,4) >= 340 && substr($isbn,1,4) <= 369) //0340-0369
$isbna = $isbna . substr($isbn,1,4) . "-" . substr($isbn,5,4);

if (substr($isbn,1,5) >= 3700 && substr($isbn,1,5) <= 3999) //03700-03999
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,2) >= 4 && substr($isbn,1,2) <= 19) //04-19
$isbna = $isbna . substr($isbn,1,2) . "-" . substr($isbn,3,6);

if (substr($isbn,1,3) >= 200 && substr($isbn,1,3) <= 699)
$isbna = $isbna . substr($isbn,1,3) . "-" . substr($isbn,4,5);

if (substr($isbn,1,4) >= 7000 && substr($isbn,1,4) <= 8499)
$isbna = $isbna . substr($isbn,1,4) . "-" . substr($isbn,5,4);

if (substr($isbn,1,5) >= 85000 && substr($isbn,1,5) <= 89999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,6) >= 900000 && substr($isbn,1,6) <= 949999)
$isbna = $isbna . substr($isbn,1,6) . "-" . substr($isbn,7,2);

if (substr($isbn,1,7) >= 9500000 && substr($isbn,1,7) <= 9539999)
$isbna = $isbna . substr($isbn,1,7) . "-" . substr($isbn,8,1);

if (substr($isbn,1,5) >= 95400 && substr($isbn,1,5) <= 96999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);

if (substr($isbn,1,7) >= 9700000 && substr($isbn,1,7) <= 9899999)
$isbna = $isbna . substr($isbn,1,7) . "-" . substr($isbn,8,1);

if (substr($isbn,1,5) >= 99000 && substr($isbn,1,5) <= 99999)
$isbna = $isbna . substr($isbn,1,5) . "-" . substr($isbn,6,3);
}


$isbna = $isbna . "-" . substr($isbn,9,1);
return ($isbna);
}

//Function xrfl_isbn10to13
//Use: Function returns the ISBN-13 of an ISBN-10.
//Credit: Roland Tanner @ http://trwa.ca/2012/02/php-isbn-10-to-isbn-13-converter/
function xrfl_isbn10to13($isbn10){
	$isbnStem = strlen($isbn10) == 10 ? substr($isbn10, 0,9) : $isbn10;
	$isbn13data = xrfl_isbn13checker('978' . $isbnStem);
	return $isbn13data['isbn13'];
}

//Function xrfl_isbn13checker
//Use: Function accepts either 12 or 13 digit number, and either provides or checks the validity of the 13th checksum digit. Optionally converts to ISBN 10 as well.
//Credit: Roland Tanner @ http://trwa.ca/2012/02/php-isbn-10-to-isbn-13-converter/
function xrfl_isbn13checker($input, $convert = FALSE){
	$output = FALSE;
	if (strlen($input) < 12){
		$output = array('error'=>'ISBN too short.');
	}
	if (strlen($input) > 13){
		$output = array('error'=>'ISBN too long.');
	}
	if (!$output){
		$runningTotal = 0;
		$r = 1;
		$multiplier = 1;
		for ($i = 0; $i < 13 ; $i++){
			$nums[$r] = substr($input, $i, 1);
			$r++;
		}
		$inputChecksum = array_pop($nums);
		foreach($nums as $key => $value){
			$runningTotal += $value * $multiplier;
			$multiplier = $multiplier == 3 ? 1 : 3;
		}
		$div = $runningTotal / 10;
		$remainder = $runningTotal % 10;

		$checksum = $remainder == 0 ? 0 : 10 - substr($div, -1);

		$output = array('checksum'=>$checksum);
		$output['isbn13'] = substr($input, 0, 12) . $checksum;
		if ($convert){
			$output['isbn10'] = xrfl_isbn13to10($output['isbn13']);
		}
		if (is_numeric($inputChecksum) && $inputChecksum != $checksum){
			$output['error'] = 'Input checksum digit incorrect: ISBN not valid';
			$output['input_checksum'] = $inputChecksum;
		}
	}
	return $output;
}

//Function xrfl_isbn13hyp
//Use: Hyphenates a 13-digit ISBN.
function xrfl_isbn13hyp($isbn)
{
$isbna = xrfl_isbn10hyp(substr($isbn,3,10));
$isbnb = substr($isbn,0,3) . "-" . $isbna;
return ($isbnb);
}

//Function xrfl_isbn13to10
//Use: Function returns the ISBN-10 of an ISBN-13.
//Credit: Roland Tanner @ http://trwa.ca/2012/02/php-isbn-10-to-isbn-13-converter/
function xrfl_isbn13to10($isbn13){
	$isbnStem = strlen($isbn13) == 13 ? substr($isbn13, 12) : $isbn13;
	$isbnStem = substr($isbn13, -10);
	$isbn10data = xrfl_isbn10checker($isbnStem);
	return $isbn10data['isbn10'];
}

//Function xrfl_issnhyp
//Use: Hyphenates an ISSN.
function xrfl_issnhyp($issn)
{
$issnb = substr($issn,0,4) . "-" . substr($issn,4,4);
return ($issnb);
}

?>