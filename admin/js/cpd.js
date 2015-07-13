(function( $ ) {
	'use strict';
	
	showRelationships($('#cpd_role').val());
	$('#cpd_role, #cpd_journal').change(function(e) {
		showRelationships();
	});
	
	function showRelationships() {

		if( $('#cpd_role').length > 0 ) {

			cpd_role=$('#cpd_role').val();
			cpd_journal=$('#cpd_journal').val();
			if(cpd_role=='participant') {
				$('.cpd_journals').show();
				$('.cpd_journals_base').hide();
				$('.cpd_supervisors').show();
				$('.cpd_participants').hide();

				if( $('#cpd_journal').val() == 'new' ) {
					$('.cpd_journals_base').show();
				}

			} else if(cpd_role=='supervisor') {
				$('.cpd_journals').hide();
				$('.cpd_journals_base').hide();
				$('.cpd_supervisors').hide();
				$('.cpd_participants').show();
			} else {
				$('.cpd_journals').hide();
				$('.cpd_journals_base').hide();
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

	var cpd_pages_input			= $('.cpd_options .pages input');
	var cpd_journal_input				= $('.cpd_options .journals input');

	cpd_journal_input.prop('disabled', true);
	cpd_journal_input.parent().addClass('disabled');
	cpd_journal_input.closest('.journal-wrapper').addClass('disabled');

	cpd_pages_input.bind( 'click', function() {

		var checked = false;
		cpd_pages_input.each(function() {
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

	// PPD Evidence
	
	// Hide not used on load
	function cpd_meta_box_evidence_filter() {

		$('#cpd_meta_box_evidence').find('div[data-class="CMB_Radio_Field"]').each(function(){
			
			var input 		= $(this).find('input');
			var is_checked 	= false;
			input.each( function() {
				if( $(this).is(':checked') ) {
					is_checked = true;
				}
			});

			if( !is_checked ) {
				$(this).find('input:first').prop('checked', true);
			}
		});

		$('#cpd_meta_box_evidence').find('div[data-class="CMB_Radio_Field"] input:checked').each(function(){

			var group 	= $(this).closest('div[data-class="CMB_Group_Field"]');
			var upload 	= group.find('div.CMB_File_Field');
			var journal = group.find('div.CMB_Select');
			var url 	= group.find('div.CMB_URL_Field');
			var text 	= group.find('div.CMB_Text_Field');

			upload.hide();
			journal.hide();
			url.hide();
			text.hide();

			upload.css('border', '0px');
			journal.css('border', '0px');
			url.css('border', '0px');
			text.css('border', '0px');

			if( $(this).val() == 'upload' ) {
				upload.show();
				text.show();
			} else if( $(this).val() == 'journal' ) {
				journal.show();
			} else if( $(this).val() == 'url' ) {
				url.show();
				text.show();
			}
			
		});
	}
	cpd_meta_box_evidence_filter();
	

	$('body').on( 'click', '#cpd_meta_box_evidence div[data-class="CMB_Radio_Field"] input', function(){
		cpd_meta_box_evidence_filter();
	});

	$('body').on( 'click', '#cpd_meta_box_evidence button.repeat-field', function(){
		cpd_meta_box_evidence_filter();
	});


})( jQuery );
