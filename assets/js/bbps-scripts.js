/**
 *  bbPress Support JavaScript
 *
 *  @description: Provides client side functionality for bbPress Support
 */

var bbps_vars;
(function ( $ ) {


	//Topic Flags
	$( '#bbp_topic_title' ).on( 'blur', function () {

		//Sanity check
		if ( bbps_vars.enable_topic_flags !== 'on' ) {
			return false;
		}

		//Cleanup before starting
		$('.bbp-template-notice' ).remove();
		$( '.bbp-submit-wrapper' ).show();

		//These are the words typed by the user into an array
		var topic_words = $( this ).val().toLowerCase().split( ' ' );

		//Loop through topic title flags
		$.each( bbps_vars.topic_title_flags, function ( index, value ) {

			//Split words into array and lowercase standardize
			var flag_words = value.flag_words.toLowerCase().split( ',' );
			var flag_message = value.flag_message;

			//Loop through words the user typed
			$.each( topic_words, function ( index, value ) {

				//Check if flag word are here
				if ( flag_words.indexOf( value ) > -1 ) {

					//Flag word detected so append message
					$( '.bbp-the-content-wrapper' ).prepend( '<div class="bbp-template-notice bbp-topic-flag-notice"><p class="bbp-support-alert">' + flag_message + '</p></div>' );
					//Prevent Submission
					$( '.bbp-submit-wrapper' ).hide();

				}

			} );


		} );

	} );


	//Topic Gates
	$( '#bbp-new-topic-fields' ).hide();
	//Hide/Show Answers from
	$( '#give-bbp-common-issues-select' ).change( function () {
		var val = $( this ).val();
		$( '#give-common-ticket-answers div' ).hide();
		$( '#give-common-ticket-answers #give-common-issue-' + val ).show();
	} );
	$( 'input[name="give-bbp-docs-help"]' ).change( function () {
		if ( $( this ).val() == '3' ) {
			$( '#bbp-new-topic-fields' ).hide();
			$( '#give-bbp-google-search' ).show();
		} else {
			$( '#give-bbp-google-search' ).hide();
			$( '#bbp-new-topic-fields' ).show();
		}
	} );

})( jQuery );