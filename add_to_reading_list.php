<?php
require_once("includes/global.php");
require_once("includes/include_lconfig.php");
require_once("includes/header.php");

$bookid = (int)$_GET['passid'];
$barcode = $bookid + $xrfl_library_barcode;

if ($xrf_myulevel > 1) {
	$addtolist = mysqli_prepare($xrf_db, "INSERT INTO l_readlist (uid, bookid, date) VALUES(?, ?, NOW())");
	mysqli_stmt_bind_param($addtolist,"ii", $xrf_myid, $bookid);
	mysqli_stmt_execute($addtolist) or die(mysqli_error($xrf_db));

	xrf_go_redir("viewrecord.php?barcode=$barcode","Added to your reading list.",2);
}

require_once("includes/footer.php");
?>
