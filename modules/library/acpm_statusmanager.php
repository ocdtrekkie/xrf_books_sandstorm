<?php
require("ismodule.php");
$do = $_GET['do'] ?? '';
if ($do == "add")
{
    $newcode = mysqli_real_escape_string($xrf_db, $_POST['newcode']);
    $newdescr = mysqli_real_escape_string($xrf_db, $_POST['newdescr']);
    $addstatus = mysqli_prepare($xrf_db, "INSERT INTO l_statuses (code, descr) VALUES(?,?)");
    mysqli_stmt_bind_param($addstatus,"ss", $newcode, $newdescr);
    mysqli_stmt_execute($addstatus) or die(mysqli_error($xrf_db));

    $lognewstatus = mysqli_prepare($xrf_db, "INSERT INTO g_log (uid, date, event) VALUES (?, NOW(), ?)");
    $lognewstatustext = "Library: " . $newdescr . " added to statuses.";
    mysqli_stmt_bind_param($lognewstatus, "is", $xrf_myid, $lognewstatustext);
    mysqli_stmt_execute($lognewstatus) or die(mysqli_error($xrf_db));

    echo "$newdescr added to statuses.";
}

echo "<p><b>Status Manager</b></p>";

$query="SELECT * FROM l_statuses ORDER BY code ASC";
$result=mysqli_query($xrf_db, $query);

$num=mysqli_num_rows($result);

echo "<table><tr><td width=100><b>Code</b></td><td><b>Description</b></td></tr>";
$qq=0;
while ($qq < $num) {

$id=xrf_mysql_result($result,$qq,"id");
$code=xrf_mysql_result($result,$qq,"code");
$descr=xrf_mysql_result($result,$qq,"descr");

echo "<tr><td>$code</td><td>$descr</td></tr>";
$qq++;
}

echo "</table><form method='post' action='acp_module_panel.php?modfolder=$modfolder&modpanel=statusmanager&do=add'>
<p>
<input type='text' name='newcode' size='10' placeholder='Status code'>
<input type='text' name='newdescr' size='40' placeholder='Status description'>
<input type='submit' value='Add Status'>
</p>
</form>";
?>