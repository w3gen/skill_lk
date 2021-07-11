/**
 * Customizer Communicator
 *
 * @since 1.0.23
 * @version 1.5.2
 */
( function ( exports, $ ) {
	"use strict";

	var api = wp.customize, OldPreviewer;

	// Custom Customizer Previewer class (attached to the Customize API)
	api.myCustomizerPreviewer = {
		// Init
		init: function () {
			var self = this; // Store a reference to "this" in case callback functions need to reference it

			// Listen to the "customize-section-back" event for removing 'active' class from customize-partial-edit-shortcut.
			$(document).on( 'click', '.customize-section-back', function() {
			// if not multisites. 1.1.3
			if (! $("#customize-preview iframe").hasClass('loginpress_multisite_active') ) {

					$('#customize-preview iframe').contents().find('.loginpress-partial.customize-partial-edit-shortcut').each( function(){
						$(this).removeClass('active');
					} );
				}
			} );

			// activated loginpress partial icons
			$(document).on( 'click', '.control-subsection', function() {
				// if not multisites. 1.1.3
				if (! $("#customize-preview iframe").hasClass('loginpress_multisite_active') ) {
					if( $(this).attr('aria-owns') !== undefined ){
						var trigger = $(this).attr('aria-owns').replace("sub-accordion-section-", "");
						$('#customize-preview iframe').contents().find('[data-customizer-event="'+trigger+'"]').parent().addClass('active');
					}
				}
			} );
			$('#customize-controls h3.loginpress-group-heading').each(function(){
				if($(this).next('.loginpress-group-info').length>0){
					$(this).next('.loginpress-group-info').hide();
					$(this).append('<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text">Help</span></button>');
				}
			});
			$(document).on('click', '#customize-controls h3.loginpress-group-heading .customize-help-toggle', function(){
				$(this).parent().next('.loginpress-group-info').slideToggle();
			});

			$( '<li class="accordion-section control-section control-section-default control-subsection"><h4 class="accordion-section-title"><a href="https://wordpress.org/support/plugin/loginpress/reviews/#new-post" target="_blank">Like our plugin? Leave a review here!</a></h4></li><li style="padding: 10px; text-align: center;">Made with ❤ by <a href="https://WPBrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=made-with&utm_campaign=pro-upgrade" target="_blank">Adnan</a></li>' ).appendTo( '#sub-accordion-panel-loginpress_panel' );

		}
	};


	/**
	 * Capture the instance of the Preview since it is private (this has changed in WordPress 4.0).
	 */
	OldPreviewer = api.Previewer;
	api.Previewer = OldPreviewer.extend( {
		initialize: function( params, options ) {
			// Store a reference to the Previewer.
			api.myCustomizerPreviewer.preview = this;

			// Call the old Previewer's initialize function.
			OldPreviewer.prototype.initialize.call( this, params, options );
		}
	} );

	// Document Ready.
	$( function() {
		// Initialize our Previewer.
		api.myCustomizerPreviewer.init();
	} );
} )( wp, jQuery );
