<?php
use myCache\phpCache as phpCache;
require_once('phpCache.php');

$params=array(
    'mod'=>"getkurs",
    'date'=>'2019-10-22',
);

$cache=new phpCache();
$result=$cache->result($params);
echo '<pre>'.print_r($result,true).'</pre>';
