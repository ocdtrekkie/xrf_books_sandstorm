<?php
header('Content-Type: text/html; charset=iso-8859-15');
if ($xrf_mystylepref == "") {$xrf_style = $xrf_style_default;}
else {$xrf_style = $xrf_mystylepref;}
if (isset($xrf_page_subtitle)) { $xrf_title_nugget = " - "; } else { $xrf_page_subtitle = ""; $xrf_title_nugget = ""; }
echo "<html><head><title>$xrf_site_name Library$xrf_title_nugget$xrf_page_subtitle</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/$xrf_style/style.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/print/style.css\" media=\"print\" />
</head><body>";

echo "<div class=\"header\" align=\"center\">
<table width=\"100%\"><tr><td>
<p align=\"left\">
<font size=\"6\" color=\"white\">$xrf_site_name Library</font><br>
<font color=\"white\" class=\"navigation-box\"><a href=\"$xrf_site_url\"><font color=\"white\">$xrf_myusername</font></a></font>
</p>
</td><td>
<p align=\"right\" class=\"navigation-box\">
<font color=\"white\"><a href=\"index.php\"><font color=\"white\">Home</font></a> - <a href=\"search.php\"><font color=\"white\">Search</font></a></font>
</p>
</td></tr></table>
</div>

<div class=\"container\" align=\"left\">";
?>
