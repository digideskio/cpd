(function( $ ) {
	'use strict';
	
	showRelationships($('#cpd_role').val());
	$('#cpd_role').change(function(e) {
		showRelationships();
	});
	function showRelationships() {

		if( $('#cpd_role').length > 0 ) {

			cpd_role=$('#cpd_role').val();
			cpd_journal=$('#cpd_journal').val();
			if(cpd_role=='participant') {
				$('.cpd_journals').show();
				$('.cpd_supervisors').show();
				$('.cpd_participants').hide();
			} else if(cpd_role=='supervisor') {
				$('.cpd_journals').hide();
				$('.cpd_supervisors').hide();
				$('.cpd_participants').show();
			} else {
				$('.cpd_journals').hide();
				$('.cpd_supervisors').hide();
				$('.cpd_participants').hide();
			}
		}
	}
	$('.latest_posts_histogram_bar').click( function (e) {
		$( this ).parent().find( 'ul' ).slideToggle();
	});
	$('.user_posts_barchart_bar').click( function (e) {
		$( this ).parent().find( 'ul' ).slideToggle();
	});

})( jQuery );
