<?php
require("ismodule.php");
$do = $_GET['do'] ?? '';
if ($do == "add")
{
    $newcode = mysqli_real_escape_string($xrf_db, $_POST['newcode']);
    $newdescr = mysqli_real_escape_string($xrf_db, $_POST['newdescr']);
    $addlocation = mysqli_prepare($xrf_db, "INSERT INTO l_locations (code, descr) VALUES(?,?)");
    mysqli_stmt_bind_param($addlocation,"ss", $newcode, $newdescr);
    mysqli_stmt_execute($addlocation) or die(mysqli_error($xrf_db));

    $lognewlocation = mysqli_prepare($xrf_db, "INSERT INTO g_log (uid, date, event) VALUES (?, NOW(), ?)");
    $lognewlocationtext = "Library: " . $newdescr . " added to locations.";
    mysqli_stmt_bind_param($lognewlocation, "is", $xrf_myid, $lognewlocationtext);
    mysqli_stmt_execute($lognewlocation) or die(mysqli_error($xrf_db));

    echo "$newdescr added to locations.";
}

echo "<p><b>Location Manager</b></p>";

$query="SELECT * FROM l_locations ORDER BY code ASC";
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

echo "</table><form method='post' action='acp_module_panel.php?modfolder=$modfolder&modpanel=locationmanager&do=add'>
<p>
<input type='text' name='newcode' size='10' placeholder='Location code'>
<input type='text' name='newdescr' size='40' placeholder='Location description'>
<input type='submit' value='Add Location'>
</p>
</form>";
?>