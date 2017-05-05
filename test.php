<?php

function randomString () {
	$result = array();
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_";
	$len = 63;
	for ($i = 0; $i < 22; $i++) {
		$result[] = $chars[rand(0, $len)];
	}
	return implode("", $result);
}

$str = randomString();

echo $str . "\n\n";
echo password_hash($str, PASSWORD_BCRYPT) . "\n\n";
