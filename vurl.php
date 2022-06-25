<?php
/* vurltool 1.0
https://gist.github.com/luqaska/836caf2b121863df405f430d79d825b4 */
$vrl = $_SERVER["REQUEST_URI"];
$vrl = preg_replace("/#(.*)$/", "", $vrl);
$vrl = preg_replace("/\?(.*)$/", "", $vrl);
$v = explode("/", $vrl);
unset($v[0]);
$vi = implode("/", $v);
$v = explode("/", $vi);