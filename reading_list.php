<?php
require_once("includes/global.php");
require_once("includes/functions_lib.php");
require_once("includes/include_lconfig.php");
require_once("includes/header.php");

if ($xrf_myulevel > 1) {
	$getlist = mysqli_query($xrf_db, "SELECT * FROM l_readlist WHERE uid=$xrf_myid");
	$num=mysqli_num_rows($getlist);

	echo "Your Reading List: $num<br>&nbsp;<br><table width=\"100%\">";
	
	if ($num > 0) {
		$qr=0;
		while ($qr < $num) {
			$bookid = xrf_mysql_result($getlist,$qr,"bookid");
			$date = xrf_mysql_result($getlist,$qr,"date");
			
			$query = "SELECT * FROM l_books WHERE status != 'wdraw' AND status != 'rstrc' AND barcode = $bookid LIMIT 1";
			$result = mysqli_query($xrf_db, $query);
			$barcode = xrf_mysql_result($result,0,"barcode");
			$typecode = xrf_mysql_result($result,0,"typecode");
			$author = xrf_mysql_result($result,0,"author");
			$title = xrf_mysql_result($result,0,"title");
			$barcode = $barcode + $xrfl_library_barcode;
			if ($author <> "")
			{
			$aname = xrfl_getauthor($xrf_db, $author);
			}
			else
			{
			$aname = "";
			}
			
			if ($typecode != "") { $typecode = " (" . $typecode . ")"; }
			if ($qr == 0 || $qr % 4 == 0) { echo "<tr>"; }
			echo "<td height=\"250\" width=\"250\" align=\"center\"><a href=\"viewrecord.php?barcode=$barcode\">";
			$filename = "covers/$barcode.png"; 
			if (file_exists($filename)) { 
			echo "<img src=\"$filename\" style=\"height:250px;max-width:250px;\" alt=\"$title$typecode\" title=\"$title$typecode\" border=1>"; 
			} else echo "<div class=\"bookcover\"><p>$title$typecode</p><p>$aname</p></div>";
			echo "</a><br><font size=\"2\">Added: $date<br><a href=\"remove_from_reading_list.php?passid=$bookid\">[Remove from Reading List]</a></font></td>";
			if ($qr % 4 == 3) { echo "</tr>"; }
			$qr++;
		}
	}
	
	echo "</table>";
}
else { echo "Not authorized!"; }

require_once("includes/footer.php");
?>
