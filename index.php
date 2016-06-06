<?php 

// get class and instansiate
require(__DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "php-json.class.php" );
$pj = new php_json();

// init vars
$error = $message = "";

// get all categories
$cats = $pj->get_categories();

// failed to get?
if ( !is_array($cats) ) {
	$error = "<strong>Error:</strong> Failed to get categories - ".(is_string($cats) ? $cats : 'Unkown error');

  // set as empty array so the code below works
  $cats = array();
}

?>
<!DOCTYPE html>
<html lang=en>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="images/favicon.ico" rel="icon" type="image/x-icon" />
        
        <title>Simple Links Box</title>
        
        <link href="lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/site.css" rel="stylesheet" type="text/css" />
        
        <script src="lib/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="lib/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
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
                <li class="active"><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                <li><a href="add.php"><span class="glyphicon glyphicon-plus"></span> Add new</a></li>
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
            			    <?php echo $error; ?>
            			</div>
        		   <?php elseif ( !empty($message) ) : ?>
            			<div class="alert alert-success">
            			  <strong>Success!</strong> <?php echo $message; ?>
            			</div>
        		   <?php endif; ?>
            		   
            			<h3>All categories in storage</h3>
                                
            			<?php
            			
            			if ( empty($cats) ): ?>
            
            				<strong>None found, add a link with a category first.</strong>
            
            			<?php else: ?>
    
                        <div class="link-category">
                          
                  				<ul>
                  				<?php foreach($cats as $c): ?>
      
                    					<li>
                    					    <a href="category.php?id=<?php echo $c->id; ?>"><?php echo $c->name; ?></a>
                    					</li>
                            
                  				<?php endforeach; ?>
                  				</ul>
                  				
                				</div>
                    
            			<?php endif; ?>

                </div>        
            </div>
            
        </div>    
  
    </body>
</html>
