<?php
require("ismodule.php");
echo "<p><b>Your Checked Out Materials</b><p>";

$query="SELECT * FROM l_circ WHERE uid = $xrf_myid AND returned = 0";
$result=mysqli_query($xrf_db, $query);
$num=mysqli_num_rows($result);

if ($num > 0)
{
echo "<table><tr><td width=400><b>Title</b><td width=200><b>Checked Out</b></td><td width=200><b>Date Due</b></td></tr>";
$qq=0;
while ($qq < $num) {

$id=xrf_mysql_result($result,$qq,"id");
$bookid=xrf_mysql_result($result,$qq,"bookid");
$date=xrf_mysql_result($result,$qq,"date");
$due=xrf_mysql_result($result,$qq,"due");

$pquery="SELECT title FROM l_books WHERE barcode = '$bookid'";
$presult=mysqli_query($xrf_db, $pquery);
$title=xrf_mysql_result($presult,0,"title");

echo "<tr><td>$title</td><td>$date</td><td>$due</td></tr>";

$qq++;
}
}
else
{
echo "You do not have any materials checked out.";
}

echo "</table>";
?>