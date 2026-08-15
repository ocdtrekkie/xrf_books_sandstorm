<?php
require("ismodule.php");
$do = $_GET['do'] ?? '';
if ($do == "add")
{
    $newcode = mysqli_real_escape_string($xrf_db, $_POST['newcode']);
    $newdescr = mysqli_real_escape_string($xrf_db, $_POST['newdescr']);
    $newlocation = mysqli_real_escape_string($xrf_db, $_POST['newlocation']);
    $newaccess = mysqli_real_escape_string($xrf_db, $_POST['newaccess']);
    $addtype = mysqli_prepare($xrf_db, "INSERT INTO l_typecodes (code, descr, default_location, access_level) VALUES(?,?,?,?)");
    mysqli_stmt_bind_param($addtype,"ssss", $newcode, $newdescr, $newlocation, $newaccess);
    mysqli_stmt_execute($addtype) or die(mysqli_error($xrf_db));

    $lognewtype = mysqli_prepare($xrf_db, "INSERT INTO g_log (uid, date, event) VALUES (?, NOW(), ?)");
    $lognewtypetext = "Library: " . $newdescr . " added to types.";
    mysqli_stmt_bind_param($lognewtype, "is", $xrf_myid, $lognewtypetext);
    mysqli_stmt_execute($lognewtype) or die(mysqli_error($xrf_db));

    echo "$newdescr added to types.";
}

echo "<p><b>Type Manager</b></p>";

$query="SELECT * FROM l_typecodes ORDER BY code ASC";
$result=mysqli_query($xrf_db, $query);

$num=mysqli_num_rows($result);

echo "<table><tr><td width=100><b>Code</b></td><td width=200><b>Description</b></td><td width=100><b>Location</b></td><td width=100><b>Access</b></td></tr>";
$qq=0;
while ($qq < $num) {

$id=xrf_mysql_result($result,$qq,"id");
$code=xrf_mysql_result($result,$qq,"code");
$descr=xrf_mysql_result($result,$qq,"descr");
$default_location=xrf_mysql_result($result,$qq,"default_location");
$access_level=xrf_mysql_result($result,$qq,"access_level");

echo "<tr><td>$code</td><td>$descr</td><td>$default_location</td><td>$access_level</td></tr>";
$qq++;
}

echo "</table><form method='post' action='acp_module_panel.php?modfolder=$modfolder&modpanel=typemanager&do=add'>
<p>
<input type='text' name='newcode' size='10' placeholder='Type code'>
<input type='text' name='newdescr' size='25' placeholder='Type description'>
<input type='text' name='newlocation' size='10' placeholder='Default location'>
<input type='text' name='newaccess' size='10' placeholder='Access level'>
<input type='submit' value='Add Type'>
</p>
</form>";
?>