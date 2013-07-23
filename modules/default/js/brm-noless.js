jQuery( 'document' ).ready( function() {

	jQuery( '.brm' ).hide();

	jQuery( '.brm-more-link' ).click( function() {

		jQuery( '.brm, .brm-more-link' ).toggle();

	} );

} );