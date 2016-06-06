<?php 

// get class and instansiate
require(__DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "php-json.class.php" );
$pj = new php_json();

// init vars
$error = $message = "";
$new_cat = $new_link = array();

// any post?
if ( !empty($_POST) && isset($_POST['title']) && isset($_POST['link']) ) {

  // new category?
  $category_id = "";
  if ( isset($_POST['categoryNew']) && intval($_POST['categoryNew']) == 1 ) {
     // put together data for category
	  $new_cat = array( 'id' => $pj->generate_id(), 'name' => $_POST['category'] );
	
	  // add ( validate and sanitize in class )
	  $result = $pj->append_category($new_cat);
	
    // any error?
  	if ( $result !== TRUE ) {
  		$error = (is_string($result) ? $result : 'Unkown error');
  	} else {
  	  // copy category id for link
  	   $category_id = $new_cat['id'];
  	}
  } else {
    // no new category
    $category_id = $_POST['selectedCategory'];
  }

  // only continue if no error
  if ( empty($error) ) {

    // put together data
	  $new_link = array( 'id' => $pj->generate_id(), 'title' => $_POST['title'], 'link' => $_POST['link'], 'category_id' => $category_id );

    // add ( validate and sanitize in class )
	  $result = $pj->append($new_link);

    // any error?
	  if ( $result !== TRUE ) {
  		$error = (is_string($result) ? $result : 'Unkown error');
  	} else {
		  $message = 'New link added!';
	  }
  }
}

// get all categories
$cats = $pj->get_categories();

// failed to get?
if ( !is_array($cats) ) {
	$error = "Failed to get categories: ".(is_string($cats) ? $cats : 'Unkown error');

  // set as empty array so the code below works
  $cats = array();
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
        <script src="lib/bootstrap-validator/dist/validator.min.js" type="text/javascript"></script>
        <script src="js/links-box-validate.js" type="text/javascript"></script>
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
                    
                    <form action="add.php" method="post" id="addForm" data-toggle="validator">
                        
                      <div class="form-group col-lg-3 col-md-3">
                        <label for="inputTitle">Title</label>
                        <input type="text" class="form-control" name="title" id="inputTitle" data-help-class="inputTitleHelpBlock" placeholder="Write title here" required  data-error="Write a title or .. {something witty}">
                        <div class="help-block with-errors alert alert-danger inputTitleHelpBlock"></div>
                      </div>
                      
                      <div class="form-group col-lg-3 col-md-3">
                        <label for="inputLink">Link</label>
                        <input type="text" class="form-control" name="link" id="inputLink" data-help-class="inputLinkHelpBlock" placeholder="Copy/paste link here" required  data-error="Please enter a URL or copy/paste">
                        <div class="help-block with-errors alert alert-danger inputLinkHelpBlock"></div>
                      </div> 
                      
                      <div class="form-group col-lg-3 col-md-3">
                        <label for="inputCat">Category</label>                      
                        <?php if ( empty($cats) ): /* no categories */ ?>
                        
                          <input type="text" class="form-control" name="category" id="inputCat" data-help-class="inputCatAddHelpBlock" value="" placeholder="Write name of category" required data-error="Category name must not be empty">
                          <input type="hidden" name="categoryNew" id="inputCatNew" value="1">
                          <div class="help-block with-errors alert alert-danger inputCatAddHelpBlock"></div>
                          
                        <?php else: ?>
                        
                          <select class="form-control" id="selectedCategory"  data-help-class="inputCatHelpBlock" name="selectedCategory" onChange="Validate.checkCategory()" data-error="Select a category">
                            <option value="addNew">Add new</option>
                            <option value="" selected="selected"> - - - </option>
                          <?php foreach($cats as $c): ?>
                        
                            <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                        
                          <?php endforeach; ?>
                          </select>
                          
                          <input type="text" class="form-control" name="category" data-help-class="inputCatHelpBlock" id="inputCat" style="display:none;" value="" required placeholder="Write name of category" data-error="Category name must not be empty">
                          <input type="hidden" name="categoryNew" id="inputCatNew" value="0">                          
                          <div class="help-block with-errors alert alert-danger inputCatHelpBlock"></div>
                          
                        <?php endif; ?>
                      </div>
                      
                      <div class="form-group col-lg-3 col-md-3">
                          <label class="visible-lg visible-md"><!-- So the button is aligned on the same row -->&nbsp;</label>
                          <input type="submit" class="btn btn-success" value="Submit">
                      </div>
                      
                    </form>
                    
                </div>        
            </div>
            
        </div>    
            
            
        <script type="text/javascript">
          
          $(document).ready(function(){
          
            // hide all error blocks  
            $('.help-block').hide();
            
            // form validator - something is invalid
            $('#addForm').validator().on('invalid.bs.validator', function (e) {
              // use refactored function
              return Validate.validateForm(e);           
            });
            
            // form validator - all valid
            $('#addForm').validator().on('valid.bs.validator', function (e) {
              // use refactored function
              return Validate.clearHelpBlock(e);           
            });            
            
            // additional validation before submission
            $('#addForm').on('submit', function(e){
              
              // final checks
              return Validate.submitForm(e);
            });
            
          });
          
        </script>    
            
    </body>
</html>
