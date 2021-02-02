<?php $etat = file_get_contents(__DIR__.'/../ram/etats.ini') ; 
$etat = json_decode($etat,true) ; 

require_once __DIR__.'/../includes/httpcodes.php';

 ?>

<div id='maintenance' class="jumbotron row <?php if (!file_exists(__DIR__.'/../ram/maintenance.ini')) echo 'hidden' ; ?>">
	<div class='col-md-3'>
		<img class='img-thumbnail' src='/img/giphy.gif'/>
	</div>
	<div class='col-md-9'>
		<h3>Maintenance is on going ... </h3>
		<p id='maintenance_txt' ><?php if (file_exists(__DIR__.'/../ram/maintenance.ini')) echo nl2br(file_get_contents(__DIR__.'/../ram/maintenance.ini')); ?></p>
	</div>	
</div>

<div class='row'>
	<div class='col-md-8 col-sm-12'>
		<p>Last monitoring date : <span id='last_monitoring'> <?php echo strftime('%a %d %b %Y %T %z (%Z)',$etat['INFO']['date']); unset($etat['INFO']); ?></span> <br/>
			<i>Monitoring are refresh each 10 minutes.</i>
		</p>
	</div>
	<div class='col-md-4 col-sm-12'>
		<button class='btn btn-default pull-right' id='refresh'><i id="loader" class="glyphicon glyphicon-refresh text-primary"></i></button>
	</div>
</div>

<div class="row table-responsive">
	<div class='col-md-4 col-sm-2 col-xs-12'> 
		
	</div>
	<div class='col-md-4 col-sm-6 col-xs-12'> 
		<table class="table"><tr><th colspan='2'>Web tools (http return code)</th></tr> 
<?php
	foreach ($etat['SITES'] as $name => $code) {
		$style = '<span class="label label-success">'.$code.'</span>';
		if ($code <= 199) $style = '<span class="label label-primary">Information : '.httpcode($code).'</span>';
		else if ($code <= 299) $style = '<span class="label label-success">Success : '.httpcode($code).'</span>';
		else if ($code <= 399) $style = '<span class="label label-primary">Redirectional : '.httpcode($code).'</span>';
		else if ($code <= 499) $style = '<span class="label label-warning">Client Error : '.httpcode($code).'</span>';
		else if ($code <= 599) $style = '<span class="label label-danger">Server Error : '.httpcode($code).'</span>';
		else if ($code >= 700) $style = '<span class="label label-danger">Unknow Error : '.$code.'</span>';
		if ($code == -1 ) $style = '<span class="label label-danger">Off-line</span>';

		$url=explode('@',$name) ; 

		?><tr><td><a href='<?php echo $url[1] ; ?>' target='_blank'><?php echo $url[0] ; ?></a></td><td class='text-right' id='<?php echo substr(sha1($url[0]),0,6) ; ?>'><?php echo $style ; ?></td></tr> <?php
	}
	?>
		</table>

	</div>
	<div class='col-md-4 col-sm-2 col-xs-12'> 
		
	</div>
</div>
