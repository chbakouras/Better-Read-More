jQuery( document ).ready( function () {

	jQuery( "#brm_use_css" ).change( function() {

		if ( jQuery( "#brm_use_css" ).is( ':checked' ) ) {

			jQuery( "#brm_custom_css" ).show();

		} else {

			jQuery( "#brm_custom_css" ).hide();

		}

	} ).change();

} );