jQuery( 'document' ).ready( function() {
	
	jQuery( '.brm' ).hide();

	jQuery( '.brm-more-link' ).click( function() {

		jQuery( '.brm' ).toggle();

		if ( jQuery( this ).text() == brm_text.more ) {
			jQuery( this ).text( brm_text.less );
		} else {
			jQuery( this ).text( brm_text.more );
		}

	} );

} );