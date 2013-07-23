jQuery( document ).ready( function () {

	jQuery( "#brm_use_css" ).change( function() {

		if ( jQuery( "#brm_use_css" ).is( ':checked' ) ) {

			jQuery( "#brm_settings_5" ).show();

		} else {

			jQuery( "#brm_settings_5" ).hide();

		}

	} ).change();

	jQuery( "#brm_show_less" ).change( function() {

		if ( jQuery( "#brm_show_less" ).is( ':checked' ) ) {

			jQuery( "#brm_settings_3" ).show();

		} else {

			jQuery( "#brm_settings_3" ).hide();

		}

	} ).change();

} );