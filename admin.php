<?php 
session_start(); 
if ($_SESSION['auth'] !== true) 
{
    header('location: /auth.php');
    exit();
}

require_once __DIR__ . '/includes/security.php' ; 

// Gestion des erreurs ----------------------- 
function error($txt)
{
    $_SESSION['error'] = $txt ; 
    header('location: /admin.php');
    exit();
}
$error = '';
if (isset($_SESSION['error']))
{
    $error = $_SESSION['error'] ; 
    unset($_SESSION['error']);
    $error='<div class="alert alert-danger" role="alert">
    <strong>Error :</strong> '.$error.'
  </div>';
}
function success($txt)
{
    $_SESSION['success'] = $txt ; 
    header('location: /admin.php');
    exit();
}
$success = '';
if (isset($_SESSION['success']))
{
    $success = $_SESSION['success'] ; 
    unset($_SESSION['success']);
    $success='<div class="alert alert-success" role="alert">
    <strong>Success :</strong> '.$success.'
  </div>';
}
// FIN ------------------- Gestion des erreurs 

// ----------------------- Gestions des actions :
$action = null ; 
if (isset($_GET['a'])) $action = $_GET['a']; 

if ($action == 'logout') 
{
    session_destroy();
    header('location: /');
    exit();
}

if ($action == 'adduser' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    $user = $_POST['user']; 
    $pass = $_POST['pass'];
    $return = Authentification::auth()->addUser($user,$pass);
    if ($return !== true) error($return) ; 
    success('New user added');
}

if ($action == 'deluser' && $_SERVER['REQUEST_METHOD'] == 'GET')
{
    $user = $_GET['user']; 
    $return = Authentification::auth()->delUser($user,$pass);
    if ($return !== true) error($return) ; 
    success('User removed');
}

if ($action == 'addmaintenance' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    file_put_contents(__DIR__.'/ram/maintenance.ini',$_POST['mnt']);
    success('Message add - Maintenance actived');
}

if ($action == 'delmaintenance' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    unlink(__DIR__.'/ram/maintenance.ini');
    success('Message removed - Maintenance unactived');
}


if (isset($_GET['a']))    error('Undefined action');

// FIN ---------------------------- Gestion des actions

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>TnS Monitoring</title>
    <link rel="icon" href="favicon.ico" />

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/fontawesome-all.css" rel="stylesheet">
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>
  </head>
  <body style='padding-top:70px;'>
    
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">T&amp;S Monitoring</a>
        </div>
      </div>
    </nav>

    <div class="container" id="main">
        <div class='row'>
            <div class='col-md-8 col-sm-12'>
                <h2>Administration</h2>
                <?php echo $error . $success;  ?>
            </div>
            <div class='col-md-4 col-sm-12'>
                <a href='?a=logout' class='btn btn-default pull-right' ><i class="glyphicon glyphicon-lock text-primary"></i> Disconnect</a>
            </div>
        </div>
      
        <div class='row'>
            <div class='col-md-4 col-sm-6 col-xs-12'> 
                <table class="table"><tr><th colspan='2'>Admin account</th></tr> 
                <?php
                foreach (Authentification::auth() as $user => $pwd) {
                    $link = '<a class="btn btn-xs btn-danger" href="?a=deluser&user='.urlencode($user).'">delete</a>' ;
                    if ($user=='admin')$link = '' ;
                    echo '<tr><td>'.$user.'<td><td class="text-right">'.$link.'</td></tr>';
                }
                ?>
                </table>
                <form action='?a=adduser' method=post>
                    <h5 class=text-center>Add new admin account : </h5>
                    <input type=text name=user placeholder='Username' class='col-xs-12' />
                    <input type=password name=pass placeholder='Password' class='col-xs-12'/>
                    <input type=submit value="Add new user" class='col-xs-12' />
                </form>
            </div>

            <div class='col-md-4 col-sm-6 col-xs-12'> 
                <?php 
                 if (file_exists(__DIR__.'/ram/maintenance.ini'))
                 {
                     $maintenance = file_get_contents(__DIR__.'/ram/maintenance.ini');
                     $active = "<span class='label label-success text-right'> Active </span>" ; 
                 }
                 else
                 {
                    $maintenance = false ;
                    $active = "<span class='label label-default  text-right'> Unactive </span>" ;
                 }
                 ?>
                <h5><strong>Maintenance mode</strong> <?php echo $active ;?></h5>
                <?php 
                    if ($maintenance) echo nl2br($maintenance);
                ?>
                <form action='?a=addmaintenance' method=post>
                    <h5 class=text-center>Custumise maintenance message : </h5>
                    <textarea type=text name=mnt class='col-xs-12' rows=6 ><?php
                            if ($maintenance) echo ($maintenance);
                        ?></textarea>
                    <input type=submit value="Active/Update Maintenance text" class='col-xs-12' />
                </form>
                <form action='?a=delmaintenance' method=post>
                    <input type=submit value="Unactive Maintenance text" class='col-xs-12' />
                </form>
            </div>

            <div class='col-md-4 col-sm-6 col-xs-12'> 
                <h5><strong>Configuration file</strong></h5>
                <p> File availabale on <i>/var/www/html/.conf.ini</i> </p>
                <pre><?php 
                    echo (file_get_contents('.conf.ini')) ; 
                ?></pre>
            </div>

        </div>

        <div>
        <div class='col-md-12 col-sm-12 col-xs-12'> 
                <h5><strong>Log file</strong></h5>
                <p> File availabale on <i>/var/www/html/ram/logs</i> </p>
                <pre><?php 
                    echo (file_get_contents('./ram/logs')) ; 
                ?></pre>
            </div>
        </div>

    </div>


    <footer class="footer">
      <div class="container">
        <p class="text-muted">Nicolas Dumas &copy; <?= date('Y') ?></p>
      </div>
    </footer>

  </body>
</html>
