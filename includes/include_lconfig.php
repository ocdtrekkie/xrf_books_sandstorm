<?php
$xrfl_config_query="SELECT * FROM l_config";
$xrfl_config_result=mysqli_query($xrf_db, $xrfl_config_query);

$xrfl_library_home=xrf_mysql_result($xrfl_config_result,0,"library_home");
$xrfl_library_barcode=xrf_mysql_result($xrfl_config_result,0,"library_barcode");
$xrfl_library_local_repository=xrf_mysql_result($xrfl_config_result,0,"library_local_repository");
$xrfl_library_remote_mailto=xrf_mysql_result($xrfl_config_result,0,"library_remote_mailto");
$xrfl_locgov_enable=xrf_mysql_result($xrfl_config_result,0,"locgov_enable");
$xrfl_steam_enable=xrf_mysql_result($xrfl_config_result,0,"steam_enable");
?>