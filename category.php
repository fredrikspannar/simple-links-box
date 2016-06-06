<?php 

// get class and instansiate
require(__DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "php-json.class.php" );
$pj = new php_json();

// init vars
$error = $message = "";
$links = $category = array();

// any post
if ( !empty($_POST['delete_id']) ) {

  // delete link
	$result = $pj->delete($_POST['delete_id']);
	
	// any error?
	if ( $result !== TRUE ) {
		$error = "<strong>Error deleting link:</strong> ".(is_string($result) ? $result : 'Unkown error');
	} else {
		$message = 'Link has been deleted!';
	}
}

// get all links in category
$cat_id = $_GET['id'];

if ( !is_string($cat_id) || strlen($cat_id) != 32 ) {
  // invalid id
  $error = "<strong>Error:</strong> Category is not valid";
} else {

  // get selected category
  $category = $pj->get_categories($cat_id);

  // failed to get?
  if ( !is_object($category) ) {
  	$error = "<strong>Error:</strong> Failed to get category - ".(is_string($category) ? $category : 'Unkown error');
  
    // set as empty array so the code below works
    $category = array();
  } else { 
  
    // get all links by category
    $links = $pj->get_by_category($cat_id);
      
    // failed to get?
    if ( !is_array($links) ) {
    	$error = "<strong>Error:</strong> Failed to get links in category - ".(is_string($links) ? $links : 'Unkown error');
    
      // set as empty array so the code below works
      $links = array();
    }    
  }
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
            		   
            			<h3>Links in storage</h3>
                                
            			<?php
            
            			
            			if ( empty($links) || empty($category) ): ?>
            
            				<strong>No links found or category not found.</strong>
            
            			<?php else: ?>
            
                      
                        <div class="link-category-list">
                          <h4><?php echo $category->name; ?></h4>
                          
                  				<ul class="link-list">
                  				<?php foreach($links as $l):
  
                  				    $image = ( !isset($l->image) || empty($l->image) ? 'images/empty-site.jpg' : $l->image);
                  				    ?>
                  
                    					<li>
                    					    <a href="edit.php?id=<?php echo $l->id; ?>" title="Edit '<?php echo $l->title; ?>'" class="edit-link"></a>
                    					    <a href="javascript:void(0);" class="remove-link" title="Delete '<?php echo $l->title; ?>'" data-title="<?php echo $l->title; ?>" data-id="<?php echo $l->id; ?>"></a>
                    					    <a href="<?php echo $l->link; ?>"  title="Open '<?php echo $l->title; ?>'" class="image-link" target="_blank">
                    					        <img src="<?php echo $image; ?>">
                    					        <?php echo $l->title; ?>
                    					   </a>
                    					</li>
                            
                  				<?php endforeach; ?>
                  				</ul>
                				</div>
                				
                    
            			<?php endif; ?>

                </div>        
            </div>
            
        </div>    
  
      <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Modal title</h4>
          </div>
          <div class="modal-body">
            <p>One fine body&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-cancel btn-danger" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-confirm btn-success">Proceed</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->      

    <form class="delete-form" action="category.php?id=<?php echo $cat_id; ?>" method="post"><input type="hidden" id="delete_id" name="delete_id" value=""><input type="submit"></form>

    </body>
</html>
