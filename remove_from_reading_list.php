<?php
require_once("includes/global.php");
require_once("includes/include_lconfig.php");
require_once("includes/header.php");

$bookid = (int)$_GET['passid'];

if ($xrf_myulevel > 1) {
	$removefromlist = mysqli_prepare($xrf_db, "DELETE FROM l_readlist WHERE uid = ? AND bookid = ?");
	mysqli_stmt_bind_param($removefromlist,"ii", $xrf_myid, $bookid);
	mysqli_stmt_execute($removefromlist) or die(mysqli_error($xrf_db));

	xrf_go_redir("reading_list.php","Removed from your reading list.",2);
}

require_once("includes/footer.php");
?>
