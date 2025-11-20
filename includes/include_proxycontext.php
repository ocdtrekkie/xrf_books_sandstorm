<?php

$xrf_proxyopts = array(
    'http' => array(
        'proxy'  => 'tcp://127.0.0.1:4000',
        'ignore_errors' => true,
    ),
);
$xrf_proxycontext  = stream_context_create($xrf_proxyopts);

// $proxyrequest = file_get_contents("https://sandstorm.io", false, $xrf_proxycontext);

?>