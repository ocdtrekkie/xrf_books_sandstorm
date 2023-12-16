<?php
require("ismodule.php");
require("modules/$modfolder/functions_lib.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'];
$passid = mysqli_real_escape_string($xrf_db, $_GET['passid']);
if ($do == "edit")
{
	$title = mysqli_real_escape_string($xrf_db, $_POST['title']);
	$author_id = mysqli_real_escape_string($xrf_db, $_POST['author_id']);
	$author_name = mysqli_real_escape_string($xrf_db, $_POST['author_name']);
	$author_years = mysqli_real_escape_string($xrf_db, $_POST['author_years']);
	$typecode = mysqli_real_escape_string($xrf_db, $_POST['typecode']);
	$dewey = mysqli_real_escape_string($xrf_db, $_POST['dewey']);
	$format = mysqli_real_escape_string($xrf_db, $_POST['format']);
	$copyright = mysqli_real_escape_string($xrf_db, $_POST['copyright']);
	$isbn10 = mysqli_real_escape_string($xrf_db, $_POST['isbn10']);
	$isbn13 = mysqli_real_escape_string($xrf_db, $_POST['isbn13']);
	$issn = mysqli_real_escape_string($xrf_db, $_POST['issn']);
	$lccn = mysqli_real_escape_string($xrf_db, $_POST['lccn']);
	$lccat = mysqli_real_escape_string($xrf_db, $_POST['lccat']);
	$tags = mysqli_real_escape_string($xrf_db, $_POST['tags']);
	$series = mysqli_real_escape_string($xrf_db, $_POST['series']);
	// $serial = mysqli_real_escape_string($xrf_db, $_POST['serial']);
	// $steam_id = mysqli_real_escape_string($xrf_db, $_POST['steam_id']);
	
	$locationquery = "SELECT default_location FROM l_typecodes WHERE code = '$typecode'";
	$locationresult = mysqli_query($xrf_db, $locationquery);
	$locationnum=mysqli_num_rows($locationresult);
	if ($locationnum > 0 ) { $location = xrf_mysql_result($locationresult,0,"default_location"); }
	else { $location = ""; }
	
	if ($dewey == "") { $status = "uncat"; }
	else { $status = "avail"; $dewey = str_replace("/","",trim($dewey)); }
	
	$isbn10 = str_replace("-","",trim($isbn10));
	$isbn13 = str_replace("-","",trim($isbn13));
	$issn = str_replace("-","",trim($issn));
	$lccn = trim($lccn);
	$lccat = trim($lccat);
	
	if ($isbn13 == "" && $isbn10 != "") { $isbn13 = xrfl_isbn10to13($isbn10); }
	
	if ($author_id == "" && $author_name != "") {
		$addauthor = mysqli_prepare($xrf_db, "INSERT INTO l_authors (name, years) VALUES(?, ?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addauthor,"ss", $author_name, $author_years);
		mysqli_stmt_execute($addauthor) or die(mysqli_error($xrf_db));
		$author_id = mysqli_insert_id($xrf_db);
		echo $author_name . " added with author ID " . $author_id . ".<br>";
	}
	
	$editbook = mysqli_prepare($xrf_db, "UPDATE l_books SET typecode = ?, dewey = ?, author = ?, title = ?, format = ?, year = ?, isbn10 = ?, isbn13 = ?, issn = ?, lccn = ?, lccat = ?, status = ?, location = ?, tags = ? WHERE barcode = ? LIMIT 1") or die(mysqli_error($xrf_db));
	mysqli_stmt_bind_param($editbook,"ssssssssssssssi", $typecode, $dewey, $author_id, $title, $format, $copyright, $isbn10, $isbn13, $issn, $lccn, $lccat, $status, $location, $tags, $passid);
	mysqli_stmt_execute($editbook) or die(mysqli_error($xrf_db));
	$barcode = $passid + $xrfl_library_barcode;
	echo "Media with barcode <b>" . $barcode . "</b> edited.";
	
	if ($issn != "" && $series != "" && xrfl_getperiodical($xrf_db, $issn) == "") {
		if ($typecode == "EPER") {
		$addseries = mysqli_prepare($xrf_db, "INSERT INTO l_periodicals (issn, title, lccn, lccat) VALUES(?,?,?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addseries,"ssss", $issn, $series, $lccn, $lccat); }
		else {
		$addseries = mysqli_prepare($xrf_db, "INSERT INTO l_periodicals (issn, title) VALUES(?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addseries,"ss", $issn, $series); }
		mysqli_stmt_execute($addseries) or die(mysqli_error($xrf_db));
		echo "<br>ISSN added to database.";
	}
	
	/* if ($serial != "") {
		$addserial = mysqli_prepare($xrf_db, "INSERT INTO l_serials (barcode, serial) VALUES(?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addserial,"is", $book_id, $serial);
		mysqli_stmt_execute($addserial) or die(mysqli_error($xrf_db));
		echo "<br>Serial added to database.";
	} */
	
	/* if ($steam_id != "" && $xrfl_steam_enable == 1) {
		$addsteamid = mysqli_prepare($xrf_db, "INSERT INTO l_externals (barcode, steam_id) VALUES(?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addsteamid,"ii", $book_id, $steam_id);
		mysqli_stmt_execute($addsteamid) or die(mysqli_error($xrf_db));
		echo "<br>Steam ID added to database.";
	} */
	
	echo "<p><font size=\"2\"><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook&copyfrom=$barcode\">[Clone This Book]</a> <a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=uploadcovers\">[Upload Covers]</a></font></p>";
}
else
{
	$sourcedataquery = "SELECT * FROM l_books WHERE barcode = $passid";
	$sourcedataresult = mysqli_query($xrf_db, $sourcedataquery);
	$sourcetitle = xrf_mysql_result($sourcedataresult,0,"title");
	$sourceauthorid = xrf_mysql_result($sourcedataresult,0,"author");
	$sourcetypecode = xrf_mysql_result($sourcedataresult,0,"typecode");
	$sourcedewey = xrf_mysql_result($sourcedataresult,0,"dewey");
	$sourceformat = xrf_mysql_result($sourcedataresult,0,"format");
	$sourceyear = xrf_mysql_result($sourcedataresult,0,"year");
	$sourceisbn10 = xrf_mysql_result($sourcedataresult,0,"isbn10");
	$sourceisbn13 = xrf_mysql_result($sourcedataresult,0,"isbn13");
	$sourceissn = xrf_mysql_result($sourcedataresult,0,"issn");
	$sourcelccn = xrf_mysql_result($sourcedataresult,0,"lccn");
	$sourcelccat = xrf_mysql_result($sourcedataresult,0,"lccat");
	$sourcetags = xrf_mysql_result($sourcedataresult,0,"tags");
	
	echo "<b>Edit Library Media</b><p>";

	echo "<form action=\"acp_module_panel.php?modfolder=$modfolder&modpanel=editbook&do=edit&passid=$passid\" method=\"POST\">
	<table><tr><td width=\"200\"><b>Title:</b></td><td width=\"400\"><textarea name=\"title\" rows=\"3\" cols=\"34\">$sourcetitle</textarea></td></tr>
	<tr><td><b>Author:</b></td><td><input type=\"text\" name=\"author_id\" size=\"3\" value=\"$sourceauthorid\"> <input type=\"text\" name=\"author_name\" size=\"22\" value=\"$sourceauthorname\"> <input type=\"text\" name=\"author_years\" size=\"8\" value=\"$sourceauthoryears\"></td></tr>
	<tr><td><b>Type/Dewey:</b></td><td><input type=\"text\" name=\"typecode\" size=\"3\" value=\"$sourcetypecode\"> <input type=\"text\" name=\"dewey\" size=\"36\" value=\"$sourcedewey\"></td></tr>
	<tr><td><b>Format/Year:</b></td><td><input type=\"text\" name=\"format\" size=\"33\" value=\"$sourceformat\"> <input type=\"text\" name=\"copyright\" size=\"6\" value=\"$sourceyear\"></td></tr>
	<tr><td><b>ISBN10/13/ISSN:</b></td><td><input type=\"text\" name=\"isbn10\" size=\"10\" value=\"$sourceisbn10\"> <input type=\"text\" name=\"isbn13\" size=\"16\" value=\"$sourceisbn13\"> <input type=\"text\" name=\"issn\" size=\"7\" value=\"$sourceissn\"></td></tr>";

	if ($xrfl_locgov_enable == 1)
		echo "<tr><td><b>LCCN/Cat:</b></td><td><input type=\"text\" name=\"lccn\" size=\"14\" value=\"$sourcelccn\"> <input type=\"text\" name=\"lccat\" size=\"25\" value=\"$sourcelccat\"></td></tr>";
	
	echo "<tr><td><b>Tags:</b></td><td><textarea name=\"tags\" rows=\"3\" cols=\"34\">$sourcetags</textarea></tr>
	<tr><td><b>Series:</b></td><td><input type=\"text\" name=\"series\" size=\"44\"></td></tr>";
	/* echo "<tr><td><b>Serial #:</b></td><td><input type=\"text\" name=\"serial\" size=\"44\"></td></tr>"; */
	
	/* if ($xrfl_steam_enable == 1)
		echo "<tr><td><b>Steam ID:</b></td><td><input type=\"text\" name=\"steam_id\" size=\"10\"></td></tr>"; */
	
	echo "<tr><td></td><td><input type=\"submit\" value=\"Edit\"></td></tr></table></form>";
}
?>
