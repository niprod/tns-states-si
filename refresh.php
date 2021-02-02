<?php 
// protection contre le refresh intesif

require_once __DIR__.'/includes/httpcodes.php';
// require_once __DIR__.'/includes/doors.php';

$etat = file_get_contents(__DIR__.'/ram/etats.ini') ; 
$etat = json_decode($etat,true) ;

$token = @file_get_contents(__DIR__.'/ram/token') ; 
if (time() - $token < 150) 
{
    echo 'Un mise à jour est déjà en cours' ;
    exit();
}
file_put_contents(__DIR__.'/ram/token', time());
//refresh :
include __DIR__.'/includes/main.php' ; 
set_time_limit(10);
$result=array();
$conf = parse_ini_file(__DIR__.'/.conf.ini', true);

foreach ($conf['SITES'] as $name => $address) {
    set_time_limit(50);

    $handle = curl_init($address);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt ($handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
    curl_setopt($handle,CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($handle);
    
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode == 0) $httpCode = 504 ; 

    if ($httpCode == 404) echo $response ; 
        
    curl_close($handle);

    echo $address . " : " . $httpCode . "\r\n" ; 

	$result['SITES'][$name] = $httpCode ; 
}


// foreach ($conf['SERVICES'] as $name => $list_ip) {
//     $ips = explode(',', $list_ip,2) ; 
//     $ip = $ips[0] ; 
//     $services = explode(',',$ips[1]) ;
//     set_time_limit(10);
//     $retur_serv = array();
//     foreach ($services as $service) {
//         $retur_serv[$service] = call_user_func('test_'.$service,$ip) ; 
//     }
//     $result['SERVICES'][$name] = $retur_serv;
// }


// foreach ($conf['EQPTS'] as $name => $ip) {
//     set_time_limit(10);
//     $result['EQPTS'][$name] = ping($ip) ; 
// }

// foreach ($conf['TEMPS'] as $name => $cmd) {
//     if ($result['EQPTS'][$name] )
//     {
//         set_time_limit(10);
//         $o = array();
//         exec($cmd,$o);
//         $result['TEMPS'][$name] = $o ; 
//     }
// }

set_time_limit(10);
$result['INFO']['date'] = time();
file_put_contents(__DIR__.'/ram/token', '0');
file_put_contents(__DIR__.'/ram/etats.ini',json_encode($result));
echo "Mise à jour effectuée";

// la mise à jour est effectué ... comparont et logons : 
$log = "----------------------------------------------\n" ; 
$put = false ; 

$color = 'green' ; 

foreach($result['SITES'] as $name => $httpCode)
{
    if ($etat['SITES'][$name] != $httpCode)
    {
        $log .= date('r',$result['INFO']['date']). ' : Site '.$name.' is now '.$httpCode.'('.httpcode($httpCode). ").\n" ;
        $put = true ; 
    }
    if ($httpCode >= 300 && $color != 'red')
    {
        $color = 'orange';
    }
    if ($httpCode >= 500)
    {
        $color = 'red';
    }
}
$num = 0 ; 
// $num_ko = 0 ; 
// foreach ($result['SERVICES'] as $name => $services)
// {

//     $num = 0 ; 
//     $num_ko = 0 ; 

//     foreach($services as $service => $ok)
//     {
//         if ($etat['SERVICES'][$name][$service] != $ok)
//         {
//             $log .= date('r',$result['INFO']['date']). ' : Service '.$service.' of '.$name.' is now '.($ok ? 'alive' : 'down'). ".\n" ;
//             $put = true ; 
//         }
//         if (!$ok) $num_ko++ ; 
//         $num++ ; 
//     }

//     if ($num_ko == $num && substr($name,0,4) != 'CUBE') $color = 'red';
//     if ($num_ko > 0 && substr($name,0,4) != 'CUBE' && $color != 'red') $color = 'orange';
 
// }


// foreach($result['EQPTS'] as $name => $ping)
// {
//     if (($etat['EQPTS'][$name] == 0 && $ping != 0)
//     || ($etat['EQPTS'][$name] != 0 && $ping == 0))
//     {
//         $log .= date('r',$result['INFO']['date']). ' : Equipment '.$name.' is now '.($ping != 0 ? 'alive ('.round($ping*1000,2).'ms)' : 'down'). ".\n" ;
//         $put = true ; 
//     }
// }

// $log_temps='' ; 
// foreach($result['TEMPS'] as $name => $temps)
// {
//     $i=0 ; 
//     $temp_max = $temps[0];
//     $temp_min = $temps[0];
//     while (isset($temps[++$i]))
//     {
//         if ($temp_max < $temps[$i]) $temp_max =  $temps[$i] ;
//         if ($temp_min > $temps[$i]) $temp_min =  $temps[$i] ;
//     }
//     $temp_max = $temp_max / 1000.0 ;
//     $temp_min = $temp_min / 1000.0 ; 
//     $log_temps .= $name.';'.$result['INFO']['date'].';'.$temp_min.';'.$temp_max.";\n" ; 
// }
//
//if ($log_temps != '')  file_put_contents(__DIR__.'/ram/temps', $log_temps,FILE_APPEND);

if ($put) file_put_contents(__DIR__.'/ram/logs', $log,FILE_APPEND);
file_put_contents(__DIR__.'/ram/color', $color);
echo $log ; 