<?php
require("ismodule.php");
require("modules/$modfolder/include_lconfig.php");

$do = $_GET['do'];
if ($do == "change")
{
	$new_library_home = mysqli_real_escape_string($xrf_db, $_POST['library_home']);
	$new_library_barcode = mysqli_real_escape_string($xrf_db, $_POST['library_barcode']);
	$new_library_local_repository = mysqli_real_escape_string($xrf_db, $_POST['library_local_repository']);
	$new_library_remote_mailto = mysqli_real_escape_string($xrf_db, $_POST['library_remote_mailto']);
	$new_locgov_enable = $_POST['locgov_enable'];
	$new_steam_enable = $_POST['steam_enable'];
	
	if ($new_library_home != $xrfl_library_home)
	{
		$query = "UPDATE l_config SET library_home = '$new_library_home'";
		mysqli_query($xrf_db, $query);
		$xrfl_library_home = $new_library_home;
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Changed library URL to $new_library_home.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	if ($new_library_barcode != $xrfl_library_barcode)
	{
		$query = "UPDATE l_config SET library_barcode = '$new_library_barcode'";
		mysqli_query($xrf_db, $query);
		$xrfl_library_barcode = $new_library_barcode;
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Changed library barcode to $new_library_barcode.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	if ($new_library_local_repository != $xrfl_library_local_repository)
	{
		$query = "UPDATE l_config SET library_local_repository = '$new_library_local_repository'";
		mysqli_query($xrf_db, $query);
		$xrfl_library_local_repository = $new_library_local_repository;
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Changed library local repository to $new_library_local_repository.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	if ($new_library_remote_mailto != $xrfl_library_remote_mailto)
	{
		$query = "UPDATE l_config SET library_remote_mailto = '$new_library_remote_mailto'";
		mysqli_query($xrf_db, $query);
		$xrfl_library_remote_mailto = $new_library_remote_mailto;
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Changed library remote mailto address to $new_library_remote_mailto.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	if ($new_locgov_enable == "enabled" && $xrfl_locgov_enable == 0)
	{
		$query = "UPDATE l_config SET locgov_enable = 1";
		mysqli_query($xrf_db, $query);
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Enabled Library of Congress integration.')";
			mysqli_query($xrf_db, $query);
		}
	} elseif ($new_locgov_enable != "enabled" && $xrfl_locgov_enable == 1)
	{
		$query = "UPDATE l_config SET locgov_enable = 0";
		mysqli_query($xrf_db, $query);
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Disabled Library of Congress integration.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	if ($new_steam_enable == "enabled" && $xrfl_steam_enable == 0)
	{
		$query = "UPDATE l_config SET steam_enable = 1";
		mysqli_query($xrf_db, $query);
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Enabled Steam integration.')";
			mysqli_query($xrf_db, $query);
		}
	} elseif ($new_steam_enable != "enabled" && $xrfl_steam_enable == 1)
	{
		$query = "UPDATE l_config SET steam_enable = 0";
		mysqli_query($xrf_db, $query);
		if ($xrf_vlog_enabled == 1)
		{
			$query="INSERT INTO g_log (uid, date, event) VALUES ('$xrf_myid',NOW(),'Disabled Steam integration.')";
			mysqli_query($xrf_db, $query);
		}
	}
	
	xrf_go_redir("acp.php","Settings changed.",2); 
}
else
{
	if ($xrfl_locgov_enable == 1)
		$locgov_checked = " checked";
	if ($xrfl_steam_enable == 1)
		$steam_checked = " checked";
	echo "
	<p><b>Library Configuration</b></p>
	<form action=\"acp_module_panel.php?modfolder=library&modpanel=config&do=change\" method=\"POST\">
	<table><tr><td>
	<table>
	<tr><td>Library URL:</td><td><input type=\"text\" name=\"library_home\" value=\"$xrfl_library_home\" size=\"30\"><br>
	<font size=\"2\">This is the location of the library<br>index, as users will browse to it.<br><i>https://catalog.example.com</i><p></font></td></tr>
	<tr><td>Base Barcode:</td><td><input type=\"text\" name=\"library_barcode\" value=\"$xrfl_library_barcode\" size=\"30\"><br>
	<font size=\"2\">This is the base number for the<br>library barcode. It should be a<br>12-digit number with trailing zeroes.<br><i>ex. 123400000000</i><p></font></td></tr>
	<tr><td>Local Repository:</td><td><input type=\"text\" name=\"library_local_repository\" value=\"$xrfl_library_local_repository\" size=\"30\"><br>
	<font size=\"2\">If any electronic media is browsable<br>from a file share or drive letter,<br>place the common part of the<br>directory address here.<br><i>ex. E:/Books</i><p></font></td></tr>
	<tr><td>Remote Mailto:</td><td><input type=\"text\" name=\"library_remote_mailto\" value=\"$xrfl_library_remote_mailto\" size=\"30\"><br>
	<font size=\"2\">If any electronic media can be<br>remotely requested by email,<br>place a mailto: string here.<br><i>ex. mailto:checkout@example.com<br>?subject=check&#37;20out&#37;20</i><p></font></td></tr>
	</table>
	</td><td width=\"50\"></td><td width=\"300\"><b>Integration Features</b><br><font size=\"2\">Check to enable</font>
	<p><input type=\"checkbox\" name=\"locgov_enable\" value=\"enabled\"$locgov_checked> Library of Congress integration<br><font size=\"2\">Display Library of Congress control number and catalog fields, enable import from LOC MARCXML records.</font>
	<p><input type=\"checkbox\" name=\"steam_enable\" value=\"enabled\"$steam_checked> Steam integration<br><font size=\"2\">Display Steam ID field.</font></td></tr></table>
	<input type=\"submit\" value=\"Submit!\">
	</form>";
}
?>