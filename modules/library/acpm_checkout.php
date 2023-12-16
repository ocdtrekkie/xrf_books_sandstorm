<?php
require("ismodule.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'];
$passid = $_GET['passid'];
if ($do == "check")
{
	$patron = mysqli_real_escape_string($xrf_db, $_POST['patron']);
	$barcode = mysqli_real_escape_string($xrf_db, $_POST['barcode']);
	$duedate = mysqli_real_escape_string($xrf_db, $_POST['duedate']);
	$bookid = $barcode - $xrfl_library_barcode;

	$query="SELECT * FROM g_users WHERE email='$patron' || id='$patron'";
	$result=mysqli_query($xrf_db, $query);
	@$custid=xrf_mysql_result($result,0,"id");
	
	$query="SELECT * FROM l_books WHERE barcode='$bookid'";
	$result=mysqli_query($xrf_db, $query);
	@$oldstatus=xrf_mysql_result($result,0,"status");
	if ($oldstatus == "chked")
		mysqli_query($xrf_db, "UPDATE l_circ SET returned = NOW() WHERE bookid = '$bookid'") or die(mysqli_error($xrf_db));
	else
		mysqli_query($xrf_db, "UPDATE l_books SET status = 'chked' WHERE barcode = '$bookid'") or die(mysqli_error($xrf_db)); 

	mysqli_query($xrf_db, "INSERT INTO l_circ (uid, bookid, date, due) VALUES('$custid', $bookid, NOW(), '$duedate')") or die(mysqli_error($xrf_db)); 

	xrf_go_redir("acp.php","Checked out.",2);
}
else
{
	if ($passid != 0)
		$barcode=$xrfl_library_barcode + (int)$passid;
	else
		$barcode=substr($xrfl_library_barcode,0,8);

	echo "<b>Check Out Material</b><p>";

	echo "<form action=\"acp_module_panel.php?modfolder=library&modpanel=checkout&do=check\" method=\"POST\">
	<table><tr><td><b>Patron ID or Email:</b></td><td><input type=\"text\" name=\"patron\" size=\"50\"> <input type=\"submit\" value=\"Check Out\"></td></tr>
	<tr><td><b>Barcode:</b></td><td><input type=\"text\" name=\"barcode\" size=\"50\" value=\"$barcode\"></td></tr>
	<tr><td><b>Due Date:</b></td><td><input type=\"text\" name=\"duedate\" size=\"50\"></td></tr>
	</table></form>";
}
?>
