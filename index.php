<?php 

ini_set('display_errors',1);

setlocale(LC_TIME,"fr_FR.utf-8") ;

$request = explode('/',substr($_SERVER['REQUEST_URI'],1));

$page = 'etat';

$pages = array('','migration','etat');

if (!in_array($page,$pages))
{
  header("HTTP/1.0 404 Not Found");
  echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>'; 
  exit();
}

if ($page == '') $page = 'index';


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
    <link href="/css/bootstrap.css" rel="stylesheet">
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
  <?php $color = file_get_contents('./ram/color'); 
    $bg = '' ;
    if ($color == 'red') $bg = 'bg-danger'; 
    if ($color == 'orange') $bg = 'bg-warning'; 
    if ($color == 'green') $bg = 'bg-success'; 
    $bg='';
    ?>
  <body style='padding-top:70px;'>
    
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container  <?php echo $bg ; ?>">
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
      <?php include __DIR__.'/pages/'.$page.'.php' ;  ?>
    </div>


    <footer class="footer">
      <div class="container">
        <p class="text-muted"><a class='unlink' href='admin.php' >Nicolas Dumas &copy; <?= date('Y') ?></a></p>
      </div>
    </footer>

    <script type="text/javascript">
      $(document).ready(function () {
                $("#refresh").click(function($e){
                  reloadState() ; 
          });

          var timeout = setInterval(reloadState, 15000);    
          function reloadState () {
              $('#refresh').html('<i id="loader" class="fa-spin glyphicon glyphicon-refresh text-primary"></i>') ;

              $.ajax({
                    async:true, 
                    dataType:"html", 
                    success:function (txt, textStatus) {
                      data = $.parseJSON(txt) ; 

                      $('#last_monitoring').html(data.last_monitoring ); 

                      if (data.maintenance != false)
                      {
                        $('#maintenance_txt').html(data.maintenance);
                        $('#maintenance').show();
                      }
                      else
                      {
                        $('#maintenance').hide();
                      }

                       // -- SITES
                       $.each(data.sites, function(key,item)
                      {
                          $('#'+key).html(item.button ); 
                      });


                      // -- DOORS
                      $('#licences').html(data.doors.num) ; 
                      $('#doors').html(data.doors.labels) ;

                      // -- SERVICES
                      $.each(data.services, function(key,item)
                      {
                          $('#'+key).html(item.labels ); 
                          $('#link_'+key).attr( "class", "label "+ item.style) ; 
                          $('#link_'+key).html(item.html) ; 
                      });



                      // -- EQPTS
                      $.each(data.eqpts, function(key,item)
                      {
                          $('#'+key).html(item.text ); 
                          $('#'+key).attr( "class", "label "+ item.style) ; 
                          if (item.temp != false) 
                          {
                            $('#temp_'+key).html(item.temp) ;
                            $('#temp_'+key).show()
                          }
                          else
                          {
                            $('#temp_'+key).hide() ;
                          }
                      });

                    }, 
                    type:"post", 
                    url:'./ajax/etats.php'
                  });

                  $('#refresh').html('<i id="loader" class="glyphicon glyphicon-refresh text-primary"></i>') ;
          }

            });
    </script>
  </body>
</html>
