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

	$('table input.check').each(function(){
		if( $(this).prop('checked') )
		{
			$(this).closest('tr').find('.menu').removeClass('disabled').prop('disabled', false);
			if( $(this).closest('tr').find('.menu').prop('checked') )
			{
				$(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
			}
		}
		else
		{
			$(this).closest('tr').find('.menu').addClass('disabled').prop('disabled', true);
			$(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
		}
	});

	$('table input.check').click(function(){
		if( $(this).prop('checked') )
		{
			$(this).closest('tr').find('.menu').removeClass('disabled').prop('disabled', false);
			if( $(this).closest('tr').find('.menu').prop('checked') )
			{
				$(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
			}
		}
		else
		{
			$(this).closest('tr').find('.menu').addClass('disabled').prop('disabled', true);
			$(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
		}
	});

	$('table input.menu').click(function(){
		if( $(this).prop('checked') )
		{
			$(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
		}
		else
		{
			$(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
		}
	});

	$('table input.menu_order').blur(function(){
		if( !$.isNumeric( $(this).val() ) )
		{
			$(this).val(0);
		}
	});

	var cpd_assignment_input			= $('.cpd_options .assignments input');
	var cpd_journal_input				= $('.cpd_options .journals input');

	cpd_journal_input.prop('disabled', true);
	cpd_journal_input.parent().addClass('disabled');
	cpd_journal_input.closest('.journal-wrapper').addClass('disabled');

	cpd_assignment_input.bind( 'click', function() {

		var checked = false;
		cpd_assignment_input.each(function() {
			if( $(this).prop('checked') )
			{
				checked = true;
			}
		});

		if( checked )
		{
			cpd_journal_input.prop('disabled', false);
			cpd_journal_input.parent().removeClass('disabled');
			cpd_journal_input.closest('.journal-wrapper').removeClass('disabled');
		}
		else
		{
			cpd_journal_input.prop('disabled', true);
			cpd_journal_input.parent().addClass('disabled');
			cpd_journal_input.closest('.journal-wrapper').addClass('disabled');
		}

	});

})( jQuery );
