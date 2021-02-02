<?php

ini_set('error_reporting', E_ALL); 

require_once __DIR__ . '/includes/security.php' ; 

$error = '' ;

// si on a du POST c'est qu'une requette d'authentification est en cours,
// on vérifie ça ! 
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $user=$_POST['user'];
    $pass=$_POST['pass'];
    if (Authentification::auth()->checkUser($user,$pass))
    {
        session_start();
        $_SESSION['auth'] = true;
        header('location: /admin.php');
    exit();
    }
    
    $error='<div class="alert alert-danger" role="alert">
    <strong>Failed :</strong> Bad username or password.
  </div>';
}


?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Administration Login</title>
    <link rel="icon" href="favicon.ico" />

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/fontawesome-all.css" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="/css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <form class="form-signin" method='post'>
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputUser" class="sr-only">Username</label>
        <input name='user' type="text" id="inputUser" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name='pass' type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <?php echo $error ; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->

  </body>
</html>
