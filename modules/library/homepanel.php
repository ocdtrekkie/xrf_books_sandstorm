<?php
require("ismodule.php");
$zquery="SELECT * FROM l_circ WHERE uid = '$xrf_myid' AND returned = 0";
$zresult=mysqli_query($xrf_db, $zquery);
$znum=mysqli_num_rows($zresult);

if ($znum > 0)
{
echo" <tr>
<td>

<a href=\"module_page.php?modfolder=$modfolder&modpanel=checkedout\">Materials Checked Out:</a>

</td>
<td align=\"right\">

$znum

</td>
</tr>";
}

?>