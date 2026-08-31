<?php
require("ismodule.php");
require("modules/$modfolder/functions_lib.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'] ?? '';
$passid = mysqli_real_escape_string($xrf_db, $_GET['passid']);
if ($do == "edit")
{
	$title = $_POST['title'];
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
	$lccn = mysqli_real_escape_string($xrf_db, $_POST['lccn'] ?? '');
	$lccat = mysqli_real_escape_string($xrf_db, $_POST['lccat'] ?? '');
	$tags = mysqli_real_escape_string($xrf_db, $_POST['tags']);
	$series = mysqli_real_escape_string($xrf_db, $_POST['series']);
	$serial = mysqli_real_escape_string($xrf_db, $_POST['serial'] ?? '');
	$steam_id = (int)mysqli_real_escape_string($xrf_db, $_POST['steam_id'] ?? 0);
	$status = mysqli_real_escape_string($xrf_db, $_POST['status']);
	$location = mysqli_real_escape_string($xrf_db, $_POST['location']);
	
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
		$addseries = mysqli_prepare($xrf_db, "INSERT INTO l_periodicals (issn, title, lccn, lccat) VALUES(?,?,?,?)") or die(mysqli_error($xrf_db));
		if ($typecode == "EPER") { mysqli_stmt_bind_param($addseries,"ssss", $issn, $series, $lccn, $lccat); }
		else { mysqli_stmt_bind_param($addseries,"ssss", $issn, $series, "", ""); }
		mysqli_stmt_execute($addseries) or die(mysqli_error($xrf_db));
		echo "<br>ISSN added to database.";
	}
	
	$serial_exists = false;
	$old_serial = null;
	$old_serial_stmt = mysqli_prepare($xrf_db, "SELECT serial FROM l_serials WHERE barcode = ?") or die(mysqli_error($xrf_db));
	mysqli_stmt_bind_param($old_serial_stmt,"i",$passid);
	mysqli_stmt_execute($old_serial_stmt) or die(mysqli_error($xrf_db));
	mysqli_stmt_bind_result($old_serial_stmt, $old_serial);
	if (mysqli_stmt_fetch($old_serial_stmt)) { $serial_exists = true; }
	mysqli_stmt_close($old_serial_stmt);
	
	if ($serial !== "") {
		if (!$serial_exists) {
			$addserial = mysqli_prepare($xrf_db, "INSERT INTO l_serials (barcode, serial) VALUES(?,?)") or die(mysqli_error($xrf_db));
			mysqli_stmt_bind_param($addserial,"is",$passid,$serial);
			mysqli_stmt_execute($addserial) or die(mysqli_error($xrf_db));
			echo "<br>Serial added to database.";
		} elseif ($serial !== $old_serial) {
			$updateserial = mysqli_prepare($xrf_db, "UPDATE l_serials SET serial = ? WHERE barcode = ? LIMIT 1") or die(mysqli_error($xrf_db));
			mysqli_stmt_bind_param($updateserial,"si",$serial,$passid);
			mysqli_stmt_execute($updateserial) or die(mysqli_error($xrf_db));
			echo "<br>Serial updated in database.";
		}
	} elseif ($serial_exists) {
		$deleteserial = mysqli_prepare($xrf_db, "DELETE FROM l_serials WHERE barcode = ? LIMIT 1") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($deleteserial,"i",$passid);
		mysqli_stmt_execute($deleteserial) or die(mysqli_error($xrf_db));
		if (mysqli_stmt_affected_rows($deleteserial) == 1) { echo "<br>Serial removed from database."; }
	}
	
	if ($xrfl_steam_enable == 1) {
		$steam_exists = false;
		$old_steam_id = null;
		$old_steam_stmt = mysqli_prepare($xrf_db, "SELECT steam_id FROM l_externals WHERE barcode = ?") or die (mysqli_error($xrf_db));
		mysqli_stmt_bind_param($old_steam_stmt,"i",$passid);
		mysqli_stmt_execute($old_steam_stmt) or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_result($old_steam_stmt, $old_steam_id);
		if (mysqli_stmt_fetch($old_steam_stmt)) { $steam_exists = true; }
		mysqli_stmt_close($old_steam_stmt);
		
		if ($steam_id !== 0) {
			if (!$steam_exists) {
				$addsteamid = mysqli_prepare($xrf_db, "INSERT INTO l_externals (barcode, steam_id) VALUES(?,?)") or die(mysqli_error($xrf_db));
				mysqli_stmt_bind_param($addsteamid,"ii",$passid,$steam_id);
				mysqli_stmt_execute($addsteamid) or die(mysqli_error($xrf_db));
				echo "<br>Steam ID added to database.";
			} elseif ($steam_id !== $old_steam_id) {
				$updatesteamid = mysqli_prepare($xrf_db, "UPDATE l_externals SET steam_id = ? WHERE barcode = ? LIMIT 1") or die(mysqli_error($xrf_db));
				mysqli_stmt_bind_param($updatesteamid,"ii",$steam_id,$passid);
				mysqli_stmt_execute($updatesteamid) or die(mysqli_error($xrf_db));
				echo "<br>Steam ID updated in database.";
			}
		} elseif ($steam_exists) {
			$deletesteamid = mysqli_prepare($xrf_db, "DELETE FROM l_externals WHERE barcode = ? LIMIT 1") or die(mysqli_error($xrf_db));
			mysqli_stmt_bind_param($deletesteamid,"i",$passid);
			mysqli_stmt_execute($deletesteamid) or die(mysqli_error($xrf_db));
			if (mysqli_stmt_affected_rows($deletesteamid) == 1) { echo "<br>Steam ID removed from database."; }
		}
	}
	
	echo "<p><font size=\"2\"><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook&copyfrom=$barcode\">[Clone This Book]</a> <a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=uploadcovers\">[Upload Covers]</a></font></p>";
}
else
{
	$sourcedataquery = "SELECT * FROM l_books WHERE barcode = $passid";
	$sourcedataresult = mysqli_query($xrf_db, $sourcedataquery);
	$sourcetitle = xrf_mysql_result($sourcedataresult,0,"title");
	$sourceauthorid = xrf_mysql_result($sourcedataresult,0,"author");
	$sourceauthorname = ""; $sourceauthoryears = "";
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
	
	$sourceserial = "";
	$serialresult = mysqli_query($xrf_db, "SELECT serial FROM l_serials WHERE barcode = $passid");
	if (mysqli_num_rows($serialresult) > 0) { $sourceserial = xrf_mysql_result($serialresult,0,"serial"); }
	
	if ($xrfl_steam_enable == 1) {
		$sourcesteamid = "";
		$steamresult = mysqli_query($xrf_db, "SELECT steam_id FROM l_externals WHERE barcode = $passid");
		if (mysqli_num_rows($steamresult) > 0) { $sourcesteamid = xrf_mysql_result($steamresult,0,"steam_id"); }
	}
	
	$sourcestatus = xrf_mysql_result($sourcedataresult,0,"status");
	$sourcelocation = xrf_mysql_result($sourcedataresult,0,"location");
	
	$statusquery = "SELECT code, descr FROM l_statuses ORDER BY id";
	$statusresult = mysqli_query($xrf_db, $statusquery);
	
	$locationquery = "SELECT code, descr FROM l_locations ORDER BY id";
	$locationresult = mysqli_query($xrf_db, $locationquery);
	
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
	
	echo "<tr><td><b>Serial #:</b></td><td><input type=\"text\" name=\"serial\" size=\"44\" value=\"$sourceserial\"></td></tr>";
	
	if ($xrfl_steam_enable == 1)
		echo "<tr><td><b>Steam ID:</b></td><td><input type=\"text\" name=\"steam_id\" size=\"10\" value=\"$sourcesteamid\"></td></tr>";
	
	echo "<tr><td><b>Status/Location:</b></td><td><select name=\"status\">";
	
	while ($row = mysqli_fetch_assoc($statusresult)) {
		$code = mysqli_real_escape_string($xrf_db, $row['code']);
		$descr = mysqli_real_escape_string($xrf_db, $row['descr']);
		$selected = ($code == $sourcestatus) ? " selected=\"selected\"" : "";
		echo "<option value=\"{$code}\"{$selected}>{$descr}</option>";
	}
	
	echo "</select><select name=\"location\">";
	
	while ($row = mysqli_fetch_assoc($locationresult)) {
		$code = mysqli_real_escape_string($xrf_db, $row['code']);
		$descr = mysqli_real_escape_string($xrf_db, $row['descr']);
		$selected = ($code == $sourcelocation) ? " selected=\"selected\"" : "";
		echo "<option value=\"{$code}\"{$selected}>{$descr}</option>";
	}
	
	echo "</select></td></tr>";
	
	echo "<tr><td></td><td><input type=\"submit\" value=\"Edit\"></td></tr></table></form>";
}
?>
