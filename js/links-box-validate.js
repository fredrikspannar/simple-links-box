
// group all validation functions
var Validate = function() {
    
     function _validateForm(e) {
      // refactored to be used in multiple forms
        
      // no validation error?
      if ( !e.detail || e.detail.length == 0 ) return;
    
      // hide all error blocks  
      $('.help-block').hide();            
    
      // match error ( e.detail from jquery plugin )
      for(var n=0; n<e.detail.length; n++) {
      
        // check all inputs 
        $('.form-control').each(function(){
    
          // is it the same error?
          if ( $(this).data('error') == e.detail[n] ) {
            // ok, show help-block
            var helpBlock = $('.' + $(this).data("help-class") );
            helpBlock.show();
          }
          
        });
        
      } 
    }
    
    function _clearHelpBlock(e) {
        // clear previous error
        
      // no validation error?
      if ( !e.detail || e.detail.length == 0 ) return;
      
      // match error ( e.detail from jquery plugin )
      for(var n=0; n<e.detail.length; n++) {
      
        // check all inputs 
        $('.form-control').each(function(){
    
          // is it the same error?
          if ( $(this).data('error') == e.detail[n] ) {
            // ok, show help-block
            var helpBlock = $('.' + $(this).data("help-class") );
            helpBlock.hide();
            
            // exit function, all done
            return;
          }
          
        });
        
      }   
      
    }
    
    function _checkCategory() {
        // onChange event with select category
    
        var selCat = $('#selectedCategory');
        
        if ( selCat.val() == "addNew" ) {
          // add new
          selCat.hide();
          $('#inputCat').show();
          
          // set trigger for new
          $('#inputCatNew').val('1');
          
          // ok to exit now
          return;
          
        } else if ( selCat.val() == "" ) {
          // show and set error
          var helpBlock = $('.' + selCat.data("help-class") );
          helpBlock.show();
          helpBlock.html( selCat.data("error") );              
          
          // ok to exit now
          return;
        }
        
        // hide error if shown
        var helpBlock = $('.' + selCat.data("help-class") );
        helpBlock.hide().html("");            
    }
    
    function _submitForm(e) {
        // final validation before submitting
        
        var selCat = $("#selectedCategory");
        
        // is selected category empty?
        if ( selCat.val() == "" ) {
            // is error visible?
            var helpBlock = $('.' + selCat.data("help-class") );
            if ( helpBlock.is(":visible") == false  ) {
              // show error
              helpBlock.show();
              helpBlock.html( selCat.data("error") );  
            }
            
            // do NOT submit form, error is shown with onChange-event
            e.preventDefault();
            return false;
        }
        
        // is new category empty?
        var newCat = $('#inputCat');
        
        if ( selCat.val() == "addNew" && newCat.val() == "" ) {
            // is error visible?
            var helpBlock2 = $('.' + newCat.data("help-class") );
            if ( helpBlock2.is(":visible") == false  ) {
              // show error
              helpBlock2.show();
              helpBlock2.html( newCat.data("error") );  
            }            
            
            // do NOT submit form, error is shown with onChange-event
            e.preventDefault();
            return false;
        }    
        
        // all ok, submit form
        return true;
    }   
    
    // exposed to public
   return {
     validateForm: _validateForm,
     checkCategory: _checkCategory,
     clearHelpBlock: _clearHelpBlock,
     submitForm: _submitForm
   }
   
}();

