<?php
require("ismodule.php");
require("modules/$modfolder/functions_lib.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'];
if ($do == "add")
{
	$title = $_POST['title'];
	$author_id = mysqli_real_escape_string($xrf_db, $_POST['author_id']);
	$author_name = $_POST['author_name'];
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
	$serial = mysqli_real_escape_string($xrf_db, $_POST['serial']);
	$steam_id = mysqli_real_escape_string($xrf_db, $_POST['steam_id']);
	
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
	
	$addbook = mysqli_prepare($xrf_db, "INSERT INTO l_books (typecode, dewey, author, title, format, year, isbn10, isbn13, issn, lccn, lccat, status, location, tags) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)") or die(mysqli_error($xrf_db));
	mysqli_stmt_bind_param($addbook,"ssssssssssssss", $typecode, $dewey, $author_id, $title, $format, $copyright, $isbn10, $isbn13, $issn, $lccn, $lccat, $status, $location, $tags);
	mysqli_stmt_execute($addbook) or die(mysqli_error($xrf_db));
	$book_id = mysqli_insert_id($xrf_db);
	$barcode = $book_id + $xrfl_library_barcode;
	echo "Book added with barcode <b>" . $barcode . "</b>.";
	
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
	
	if ($serial != "") {
		$addserial = mysqli_prepare($xrf_db, "INSERT INTO l_serials (barcode, serial) VALUES(?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addserial,"is", $book_id, $serial);
		mysqli_stmt_execute($addserial) or die(mysqli_error($xrf_db));
		echo "<br>Serial added to database.";
	}
	
	if ($steam_id != "" && $xrfl_steam_enable == 1) {
		$addsteamid = mysqli_prepare($xrf_db, "INSERT INTO l_externals (barcode, steam_id) VALUES(?,?)") or die(mysqli_error($xrf_db));
		mysqli_stmt_bind_param($addsteamid,"ii", $book_id, $steam_id);
		mysqli_stmt_execute($addsteamid) or die(mysqli_error($xrf_db));
		echo "<br>Steam ID added to database.";
	}
	
	echo "<p><font size=\"2\"><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook\">[Add Another Book]</a> <a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook&copyfrom=$barcode\">[Clone This Book]</a> <a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=uploadcovers\">[Upload Covers]</a></font></p>";
}
else
{
	$copyfrom = mysqli_real_escape_string($xrf_db, $_GET['copyfrom']);
	$postcopyfrom = mysqli_real_escape_string($xrf_db, $_POST['copyfrom']);
	if ($copyfrom == "" && $postcopyfrom != "") { $copyfrom = $postcopyfrom;}
	$copyfrom = trim($copyfrom);
	if ($copyfrom != "" && substr($copyfrom,0,4) == substr($xrfl_library_barcode,0,4)) {
		// clone from existing record
		$sourcebookid = $copyfrom - $xrfl_library_barcode;
		$sourcedataquery = "SELECT * FROM l_books WHERE barcode = $sourcebookid";
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
	} elseif ($copyfrom != "" && $xrfl_locgov_enable == 1) {
		// import from library of congress
		$marcxml = simplexml_load_file("https://lccn.loc.gov/$copyfrom/marcxml");
		foreach($marcxml as $datafield) {
			if ($datafield['tag'] == "245") {
				// title
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "a")
					{ $loctitle = $subfield; }
					if ($subfield['code'] == "b")
					{ $locsubtitle = $subfield; }
					$sourcetitle = ucwords(str_replace(" :",": ",trim($loctitle . $locsubtitle,' /')));
				}
			}
			if ($datafield['tag'] == "100") {
				// primary author
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "a")
					{ $sourceauthormainname = $subfield; }
					if ($subfield['code'] == "q")
					{ $sourceauthormorename = $subfield; }
					if ($subfield['code'] == "d")
					{ $sourceauthoryears = $subfield; }
					$sourceauthorname = $sourceauthormainname . " " . $sourceauthormorename;
				}
			}
			if ($datafield['tag'] == "260" || $datafield['tag'] == "264") {
				// year
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "c")
					{ $sourceyear = filter_var($subfield,FILTER_SANITIZE_NUMBER_INT); }
				}
			}
			if ($datafield['tag'] == "010") {
				// lccn
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "a")
					{ $sourcelccn = trim($subfield); }
				}
			}
			if ($datafield['tag'] == "050") {
				// lccat
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "a")
					{ $lccat1 = $subfield; }
					if ($subfield['code'] == "b")
					{ $lccat2 = $subfield; }
					$sourcelccat = $lccat1 . " " . $lccat2;
				}
			}
			if ($datafield['tag'] == "082") {
				// dewey
				foreach($datafield->subfield as $subfield) {
					if ($subfield['code'] == "a")
					{ $sourcedewey = $subfield; }
				}
			}
			if ($datafield['tag'] == "020") {
				if (substr($datafield->subfield[0],0,3) == "978" && $sourceisbn13 == "")
				{$sourceisbn13 = $datafield->subfield[0];}
				elseif (substr($datafield->subfield[0],0,3) != "978" && $sourceisbn10 == "")
				{$sourceisbn10 = $datafield->subfield[0];}
			}
		}
		if ($sourcedewey != "" && $sourceauthorname != "") {
			$sourcedewey = $sourcedewey . " " . strtoupper(substr($sourceauthorname,0,3));
		} elseif ($sourcedewey != "" & $sourcetitle != "") {
			$sourcedewey = $sourcedewey . " " . strtoupper(substr($sourcetitle,0,3));
		}
	}
	
	echo "<b>Add Library Media</b><p>";

	echo "<form action=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook&do=add\" method=\"POST\">
	<table><tr><td width=\"200\"><b>Title:</b></td><td width=\"400\"><textarea name=\"title\" rows=\"3\" cols=\"34\">$sourcetitle</textarea></td></tr>
	<tr><td><b>Author:</b></td><td><input type=\"text\" name=\"author_id\" size=\"3\" value=\"$sourceauthorid\"> <input type=\"text\" name=\"author_name\" size=\"22\" value=\"$sourceauthorname\"> <input type=\"text\" name=\"author_years\" size=\"8\" value=\"$sourceauthoryears\"></td></tr>
	<tr><td><b>Type/Dewey:</b></td><td><input type=\"text\" name=\"typecode\" size=\"3\" value=\"$sourcetypecode\"> <input type=\"text\" name=\"dewey\" size=\"36\" value=\"$sourcedewey\"></td></tr>
	<tr><td><b>Format/Year:</b></td><td><input type=\"text\" name=\"format\" size=\"33\" value=\"$sourceformat\"> <input type=\"text\" name=\"copyright\" size=\"6\" value=\"$sourceyear\"></td></tr>
	<tr><td><b>ISBN10/13/ISSN:</b></td><td><input type=\"text\" name=\"isbn10\" size=\"10\" value=\"$sourceisbn10\"> <input type=\"text\" name=\"isbn13\" size=\"16\" value=\"$sourceisbn13\"> <input type=\"text\" name=\"issn\" size=\"7\" value=\"$sourceissn\"></td></tr>";

	if ($xrfl_locgov_enable == 1)
		echo "<tr><td><b>LCCN/Cat:</b></td><td><input type=\"text\" name=\"lccn\" size=\"14\" value=\"$sourcelccn\"> <input type=\"text\" name=\"lccat\" size=\"25\" value=\"$sourcelccat\"></td></tr>";
	
	echo "<tr><td><b>Tags:</b></td><td><textarea name=\"tags\" rows=\"3\" cols=\"34\">$sourcetags</textarea></tr>
	<tr><td><b>Series:</b></td><td><input type=\"text\" name=\"series\" size=\"44\"></td></tr>
	<tr><td><b>Serial #:</b></td><td><input type=\"text\" name=\"serial\" size=\"44\"></td></tr>";
	
	if ($xrfl_steam_enable == 1)
		echo "<tr><td><b>Steam ID:</b></td><td><input type=\"text\" name=\"steam_id\" size=\"10\"></td></tr>";
	
	echo "<tr><td></td><td><input type=\"submit\" value=\"Add\"></td></tr></table></form>";
	
	if ($copyfrom == "") {
		echo "<p><form action=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook\" method=\"POST\">
		<table><tr><td width=\"200\"><b>Import From:</b></td><td width=\"400\"><input type=\"text\" name=\"copyfrom\" size=\"44\"></td></tr>
		<tr><td></td><td><input type=\"submit\" value=\"Import\"></td></tr></table></form>";
	}
}
?>
