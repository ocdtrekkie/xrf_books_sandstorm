<?php
require_once("includes/global.php");
require_once("includes/include_lconfig.php");
require_once("includes/functions_lib.php");

$letter = substr($_GET['letter'],0,1);
$xrf_page_subtitle = "Authors beginning with " . $letter;

require_once("includes/header.php");

?><div align="center"><table width="100%"><tr><td align="center">Authors: 
<a href="authors.php?letter=A">A</a> 
<a href="authors.php?letter=B">B</a> 
<a href="authors.php?letter=C">C</a> 
<a href="authors.php?letter=D">D</a> 
<a href="authors.php?letter=E">E</a> 
<a href="authors.php?letter=F">F</a> 
<a href="authors.php?letter=G">G</a> 
<a href="authors.php?letter=H">H</a> 
<a href="authors.php?letter=I">I</a> 
<a href="authors.php?letter=J">J</a> 
<a href="authors.php?letter=K">K</a> 
<a href="authors.php?letter=L">L</a> 
<a href="authors.php?letter=M">M</a> 
<a href="authors.php?letter=N">N</a> 
<a href="authors.php?letter=O">O</a> 
<a href="authors.php?letter=P">P</a> 
<a href="authors.php?letter=Q">Q</a> 
<a href="authors.php?letter=R">R</a> 
<a href="authors.php?letter=S">S</a> 
<a href="authors.php?letter=T">T</a> 
<a href="authors.php?letter=U">U</a> 
<a href="authors.php?letter=V">V</a> 
<a href="authors.php?letter=W">W</a> 
<a href="authors.php?letter=X">X</a> 
<a href="authors.php?letter=Y">Y</a> 
<a href="authors.php?letter=Z">Z</a></td></tr></table></div><?php

if ($letter != "")
$ltrstr = " WHERE name LIKE '" . $letter . "%'";
else
$ltrstr = "";

$query = "SELECT * FROM l_authors$ltrstr ORDER BY name, years";
$result = mysqli_query($xrf_db, $query);
$num=mysqli_num_rows($result);

echo "<table width=\"100%\">";

$qq=0;
while ($qq < $num) {

$id = xrf_mysql_result($result,$qq,"id");
$name = xrf_mysql_result($result,$qq,"name");
$years = xrf_mysql_result($result,$qq,"years");

echo "<tr><td>$id</td><td><a href=\"search_results.php?author=$id\">$name $years</a></td></tr>";

$qq++;
}

echo "</table>";
require_once("includes/footer.php");
?>