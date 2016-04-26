<?php 

// get class and instansiate
require(__DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "php-json.class.php" );
$pj = new php_json();

// init vars
$error = "";
$message = "";

// any post?
if ( !empty($_POST) && isset($_POST['title']) && isset($_POST['link']) ) {

  // put together data
	$new = array( 'id' => uniqid(), 'title' => $_POST['title'], 'link' => $_POST['link'] );

  // add
	$result = $pj->append($new);

  // any error?
	if ( $result !== TRUE ) {
		$error = $result;	
	} else {
		$message = 'New link added!';
	}
}



?>
<!DOCTYPE html>
<html lang=en>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link href="images/favicon.ico" rel="icon" type="image/x-icon" />
        
        <title>Simple Links Box</title>
        
        <link href="lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/site.css" rel="stylesheet" type="text/css" />
        
        <script src="lib/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="lib/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/links-box.js" type="text/javascript"></script>
    </head>
    <body>

   
        <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-cloud"></span> Simple Links Box</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <li class="active"><a href="add.php"><span class="glyphicon glyphicon-plus"></span> Add new</a></li>
                <li><a href="settings.php"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav> 


        
        <div class="container content">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    
          		    <?php if ( !empty($error) ): ?>
              			<div class="alert alert-danger">
              			    <strong>Error adding new link:</strong> <?php echo $error; ?>
              			</div>
          		   <?php elseif ( !empty($message) ) : ?>
              			<div class="alert alert-success">
              			  <strong>Success!</strong> <?php echo $message; ?>
              			</div>
          		   <?php endif; ?>

                    <h3>Add new</h3>
                    
                    <form action="add.php" method="post">
                        
                      <div class="form-group col-lg-4 col-md-4">
                        <label for="inputTitle">Title</label>
                        <input type="text" class="form-control" name="title" id="inputTitle" placeholder="Write title here">
                      </div>
                      
                      <div class="form-group col-lg-4 col-md-4">
                        <label for="inputLink">Link</label>
                        <input type="text" class="form-control" name="link" id="inputLink" placeholder="Copy/paste link here">
                      </div> 
                      
                      <div class="col-lg-4 col-md-4">
                          <label class="visible-lg visible-md"><!-- So the button is aligned on the same row -->&nbsp;</label>
                          <input type="submit" class="btn btn-success" value="Submit">
                      </div>
                      
                    </form>
                    
                </div>        
            </div>
            
        </div>    
            
    </body>
</html>