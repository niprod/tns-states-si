<?php $etat = file_get_contents(__DIR__.'/../ram/etats.ini') ; 
$etat = json_decode($etat,true) ; 

require_once __DIR__.'/../includes/httpcodes.php';

$ajax_return = array();

if (file_exists(__DIR__.'/../ram/maintenance.ini'))
{
    $ajax_return['maintenance'] = nl2br(file_get_contents(__DIR__.'/../ram/maintenance.ini'));
}
else 
{
    $ajax_return['maintenance'] = false ; 
}

$ajax_return['last_monitoring'] = date('r',$etat['INFO']['date']) ; 

foreach ($etat['SITES'] as $name => $code) 
{
    $style = '<span class="label label-success">'.$code.'</span>';
    if ($code <= 199) $style = '<span class="label label-primary">Information : '.httpcode($code).'</span>';
    else if ($code <= 299) $style = '<span class="label label-success">Success : '.httpcode($code).'</span>';
    else if ($code <= 399) $style = '<span class="label label-primary">Redirectional : '.httpcode($code).'</span>';
    else if ($code <= 499) $style = '<span class="label label-warning">Client Error : '.httpcode($code).'</span>';
    else if ($code <= 599) $style = '<span class="label label-danger">Server Error : '.httpcode($code).'</span>';
    else if ($code >= 700) $style = '<span class="label label-danger">Unknow Error : '.$code.'</span>';
    if ($code == -1 ) $style = '<span class="label label-danger">Off-line</span>';

    $url=explode('@',$name) ; 

    $ajax_return['sites'][substr(sha1($url[0]),0,6)]['button'] = $style ;

}
/*
$ajax_return['doors']['num'] = $etat['DOORS']['nb'].'/'.$etat['DOORS']['tt'].' licences' ; 
$labels = '';
foreach ($etat['DOORS']['list'] as $name)
{
    $style='label-default' ;
    $labels .= '&nbsp;<span class="label '.$style.'">'.$name.'</span><br/>';
}
$ajax_return['doors']['labels'] = $labels ; 

foreach ($etat['SERVICES'] as $serveur => $services) {
		$labels = '';
		$total = 0 ; 
		$ok = 0 ;
		foreach ($services as $name => $state)
		{
			$total ++ ; 
			if ($state) $ok ++ ; 
        }

        foreach ($services as $name => $state) 
		{
			$style='label-default' ;
			if ($state) $style='label-primary' ;
			$labels .= '&nbsp;<span class="label '.$style.'">'.$name.'</span><br/>';
        }

        $style = 'label-success' ;
		$html = 'Alive ('.$ok.'/'.$total.')';
        if ($ok < $total) 
        {
            $style = 'label-warning' ;
            $html = 'Sick ('.$ok.'/'.$total.')';
        }
        if ($ok == 0) 
        {
            $style = 'label-danger' ;
            $html = 'Dead ('.$ok.'/'.$total.')';
        }
		
		$ajax_return['services'][substr(sha1($serveur),0,6)] = ['html'=> $html, 'style' => $style, 'labels' => $labels];
    }
    
foreach ($etat['EQPTS'] as $name => $ok) {
		// recherche de temps : 
		$temp_max = false ;
		$temp_min = false ;
		if (isset($etat['TEMPS'][$name][0]))
		{
			$i=0 ; 
			$temp_max = $etat['TEMPS'][$name][0];
			$temp_min = $etat['TEMPS'][$name][0];
			while (isset($etat['TEMPS'][$name][++$i]))
			{
				if ($temp_max < $etat['TEMPS'][$name][$i]) $temp_max =  $etat['TEMPS'][$name][$i] ;
				if ($temp_min > $etat['TEMPS'][$name][$i]) $temp_min =  $etat['TEMPS'][$name][$i] ;
			}
			$temp_max = $temp_max / 1000.0 ;
			$temp_min = $temp_min / 1000.0 ;
		}
        $time = round($ok*1000,2) ; // ms avec 2 dec
        $text = 'Alive ('.$time.'ms)';
		$style = 'label-success';
		if ($temp_max)
		{
			$temp = $temp_min.'-'.$temp_max.'°C';
        }
        else
        {
            $temp = false ; 
        }

        if (! $ok) 
        {
            $text = 'Off-line';
		    $style = 'label-danger';
        }

        $ajax_return['eqpts'][substr(sha1($name),0,6)] = ['text'=> $text, 'style' => $style, 'temp' => $temp] ;
    }
    */
    $ajax_return['color'] = file_get_contents(__DIR__.'/../ram/color');

    echo json_encode( $ajax_return);