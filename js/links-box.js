
// when the whole document has been loaded
$(document).ready(function(){

    // if any alert is found..
	if ( $('.alert') ) {
        // set a timeout for 5 seconds..
		setTimeout(function(){
            // and then hide it
			$('.alert').hide();
		}, 5000);
	}
	
	// if any remove-link is found..
    $('.remove-link').each(function(){
        // attach confirm dialog on click
        $(this).on('click', function(){
            // call function with data from html
           return confirmDelete($(this).data('id'), $(this).data('title')); 
        });
    });
});
	
	
function confirmDelete(id, title){

    // setup dialog
    $('.modal-title').html('Confirm delete');
    $('.modal-body').html('<p>Are you sure you want to <strong>delete</strong> "'+title+'"?');

    // add event handler for proceed
    $('.modal-footer .btn-confirm').on('click', function(){

        // hide modal
        $('#confirmModal').modal('hide');
        
        // remove event handler
        $(this).off('click');
        
        // setup form and submit
        $('.delete-form #delete_id').val(id);
        $('.delete-form').submit();
    });
    
    // show dialog
    $('#confirmModal').modal();
}