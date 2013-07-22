jQuery( document ).ready( function () {

	jQuery( "#brm_use_css" ).change( function() {

		if ( jQuery( "#brm_use_css" ).is( ':checked' ) ) {

			jQuery( "#brm_settings_2" ).show();

		} else {

			jQuery( "#brm_settings_2" ).hide();

		}

	} ).change();

} );