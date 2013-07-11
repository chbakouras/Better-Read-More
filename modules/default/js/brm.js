jQuery( 'document' ).ready( function() {
	
	jQuery( '.brm' ).hide();

	jQuery( '.brm-more-link' ).click( function() {

		jQuery( '.brm' ).toggle();

	} );

} );