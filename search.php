<?php $xrf_page_subtitle = "Advanced Search";
require_once("includes/global.php");
require_once("includes/functions_class.php");
require_once("includes/include_lconfig.php");
require_once("includes/header.php");
?>

<form action="search_results.php" method="POST">
<input type="text" name="searchterm" maxlength="128"><input type="submit" value="Search"><br>
<select name="searchwhat">
<option value="keyword">Keyword</option>
<option value="title">Title</option>
<option value="numbers">ISBN/ISSN/LCCN</option>
</select>

<p>Type: <select name="searchtype">
<option value=""></option><?php
$typesquery = "SELECT * FROM l_typecodes ORDER BY id ASC";
$typesresult = mysqli_query($xrf_db, $typesquery);
$typesnum=mysqli_num_rows($typesresult);
$typ=0;
while ($typ < $typesnum) {
	$typcode = xrf_mysql_result($typesresult,$typ,"code");
	$typdescr = xrf_mysql_result($typesresult,$typ,"descr");
	$typaccess = xrf_mysql_result($typesresult,$typ,"access_level");
	if (($typaccess == "L" && xrf_has_uclass($xrf_myuclass,"L")) || (is_numeric($typaccess) && $typaccess <= $xrf_myulevel)) {
		if ($typcode == "") { $typcode = "0"; }
		echo "<option value=\"$typcode\">$typdescr</option>";
	}
	$typ++;
} ?></select>

<br>Catalog Group: <select name="searchgroup">
<option value=""></option>
<option value="000">000s</option>
<option value="100">100s</option>
<option value="200">200s</option>
<option value="300">300s</option>
<option value="400">400s</option>
<option value="500">500s</option>
<option value="600">600s</option>
<option value="700">700s</option>
<option value="800">800s</option>
<option value="900">900s</option>
<option value="E">E</option>
<option value="F">F</option>
<option value="J">J</option>
<option value="PB">PB</option>
</select>

<br>Location: <select name="searchlocation">
<option value=""></option><?php
$locationsquery = "SELECT * FROM l_locations ORDER BY id ASC";
$locationsresult = mysqli_query($xrf_db, $locationsquery);
$locationsnum=mysqli_num_rows($locationsresult);
$loc=0;
while ($loc < $locationsnum) {
	$loccode = xrf_mysql_result($locationsresult,$loc,"code");
	$locdescr = xrf_mysql_result($locationsresult,$loc,"descr");
	echo "<option value=\"$loccode\">$locdescr</option>";
	$loc++;
} ?></select></p>

<p>View: <select name="searchview">
<option value=""></option>
<option value="shelf">Bookshelf</option>
<option value="list">Detail List</option>
</select></p></form>

<?php
require_once("includes/footer.php");
?>