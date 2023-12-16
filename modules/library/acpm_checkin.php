<?php
require("ismodule.php");
require("modules/$modfolder/include_lconfig.php");
$do = $_GET['do'];
$passid = $_GET['passid'];
if ($do == "check")
{
	$barcode = mysqli_real_escape_string($xrf_db, $_POST['barcode']);
	$waive = mysqli_real_escape_string($xrf_db, $_POST['waive']);
	if ($waive == 'N')
		$waive = "";
	$bookid = $barcode - $xrfl_library_barcode;
	
	$query="SELECT * FROM l_circ WHERE bookid='$bookid' AND returned = 0";
	$result=mysqli_query($xrf_db, $query);
	$uid=xrf_mysql_result($result,0,"uid");
	$duedate=xrf_mysql_result($result,0,"due");
	
	mysqli_query($xrf_db, "UPDATE l_circ SET returned = NOW() WHERE bookid = '$bookid'") or die(mysqli_error($xrf_db));
	mysqli_query($xrf_db, "UPDATE l_books SET status = 'avail' WHERE barcode = '$bookid'") or die(mysqli_error($xrf_db)); 
	
	// Levy fines if billing system exists.
	$billingquery = "SELECT folder FROM g_modules WHERE name = 'Billing'";
	$billingresult = mysqli_query($xrf_db, $billingquery) or die(mysqli_error($xrf_db));
	@$billingfolder = xrf_mysql_result($billingresult,0,"folder");
	if ($billingfolder != "")
	{
		$chkdate = strtotime($duedate);

		$diff = time() - $chkdate;
		$dayslate = floor($diff / 60 / 60 / 24);
		if ($dayslate > 1)
		{
			if ($waive != "W")
			{
				include("modules/$billingfolder/functions_billing.php");

				$fine = $dayslate * 10; // 10 cents per day
				$notes = "Late fine for $barcode, $dayslate days late.";
				mysqli_query($xrf_db, "INSERT INTO b_orders (uid, date, aid, notes) VALUES('$uid', NOW(), 1, '$notes')") or die(mysqli_error($xrf_db)); 
				$oidres = mysqli_query($xrf_db, "SELECT id FROM b_orders WHERE uid = '$uid' AND aid = 1 AND notes = '$notes' ORDER BY id DESC LIMIT 1") or die(mysqli_error($xrf_db));
				$oid = xrf_mysql_result($oidres,0,"id");
				mysqli_query($xrf_db, "INSERT INTO b_charges (uid, oid, iid, amt, quantity, status) VALUES('$uid', '$oid', 9, '$fine', 1, '$waive')") or die(mysqli_error($xrf_db));
				xrfb_update_order($oid); 
				$finedetail = xrfb_disp_cash($fine);
				$finedetail = " Late fee $finedetail, $dayslate days late.";
			}
			else
			{
				$finedetail = " Late fee waived. $dayslate days late.";
			}
		}
	}

	xrf_go_redir("acp.php","Checked in.$finedetail",2);
}
else
{
	if ($passid != 0)
		$barcode=$xrfl_library_barcode + (int)$passid;
	else
		$barcode=substr($xrfl_library_barcode,0,8);

	echo "<b>Return Material</b><p>";

	echo "<form action=\"acp_module_panel.php?modfolder=library&modpanel=checkin&do=check\" method=\"POST\">
	<table><tr><td><b>Barcode:</b></td><td><input type=\"text\" name=\"barcode\" size=\"50\" value=\"$barcode\"> <input type=\"submit\" value=\"Check In\"></td></tr>
	<tr><td><b>Waive Late Fees?</b></td><td><select name=\"waive\"><option value=\"N\">No</option><option value=\"W\">Yes</option></select></td></tr>
	</table></form>";
}
?>