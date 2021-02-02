<?php

function array2ini_file($array, $file)
//opération inverse de "$array = parse_ini_file($file, TRUE);"
{
  $fp = fopen($file,"w")
    or die ('errro');
  while(list($key, $value) = each($array)) 
  {
    fwrite ($fp, "\n[".$key."]\n");
    while(list($k, $v) = each($value)) fwrite($fp, $k." = ".$v."\n");
  }
  return true;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function _test_tcp($ip,$port)
{
	$fp = @fsockopen('tcp://'.$ip,$port,$errCode,$errStr,3) ;
	echo $ip.":".$port . " : " . $errCode . " - " . $errStr . "\n\r" ; 
	if ($fp === false) return false ; 
	fclose($fp) ; 
	return true ;
}

function test_rdp($ip) {
	return _test_tcp($ip,3389) ;
}

function test_dns($ip) {
	return _test_tcp($ip,53) ;
}

function test_ssh($ip) {
	return _test_tcp($ip,22) ;
}

function test_https($ip) {
	return _test_tcp($ip,443) ;
}

function test_doors($ip) {
	return _test_tcp($ip,36677) ;
}

function test_licence($ip) {
	return _test_tcp($ip,19354) ;
}

function test_http($ip) {
	return _test_tcp($ip,80) ;
}

function test_ftp($ip) {
    return _test_tcp($ip,21) ;
}

function test_bitbucket($ip) {
    return _test_tcp($ip,7990) ;
}

function test_jenkins($ip) {
    return _test_tcp($ip,8080) ;
}

function test_sonarqube($ip) {
    return _test_tcp($ip,9009) ;
}

function test_testlink($ip) {
    return _test_tcp($ip,81) ;
}

function test_jira($ip) {
    return _test_tcp($ip,8080) ;
}

function test_hawkbit($ip) {
    return _test_tcp($ip,9001) ;
}

function test_cubeOverPi($ip) {
	return _test_tcp($ip,2022) ;
}

function ping($host, $timeout = 5) {
	/* ICMP ping packet with a pre-calculated checksum */
	$package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
	$socket  = socket_create(AF_INET, SOCK_RAW, getprotobyname('icmp'));
	socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
	@socket_connect($socket, $host, null);

	$ts = microtime(true);
	@socket_send($socket, $package, strLen($package), 0);
	if (socket_read($socket, 255))
			$result = microtime(true) - $ts;
	else    $result = false;
	socket_close($socket);

	return $result;
}

function test_ping($ip) {
    return ping($ip,5) != 0 ;
}