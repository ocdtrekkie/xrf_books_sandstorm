<?php
require_once("includes/global.php");
require_once("includes/functions_lib.php");
require_once("includes/include_lconfig.php");

$xrf_page_subtitle = "Serials and Periodicals";

require_once("includes/header.php");

$query = "SELECT * FROM l_periodicals ORDER BY title";
$result = mysqli_query($xrf_db, $query);
$num=mysqli_num_rows($result);

echo "<table width=\"100%\">";

$qq=0;
while ($qq < $num) {

$issn = xrf_mysql_result($result,$qq,"issn");
$title = xrf_mysql_result($result,$qq,"title");
$issnhyp = xrfl_issnhyp($issn);

echo "<tr><td>$issnhyp</td><td><a href=\"search_results.php?issn=$issn\">$title</a></td></tr>";

$qq++;
}

echo "</table>";
require_once("includes/footer.php");
?>