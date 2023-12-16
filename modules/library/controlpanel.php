<?php
require("ismodule.php");

echo "<p><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=config\">Library Configuration</a></p>";

echo "<p><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=checkout\">Check Out Material</a><br>
<a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=checkin\">Return Material</a></p>";

echo "<p><a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addbook\">Add Book</a><br>
<a href=\"acp_module_panel.php?modfolder=$modfolder&modpanel=uploadcovers\">Upload Covers</a></p>";

?>