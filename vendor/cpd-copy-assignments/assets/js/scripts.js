var cpdca_assignment_input			= jQuery('.cpdca_options .assignments input');
var cpdca_journal_input				= jQuery('.cpdca_options .journals input');

cpdca_journal_input.prop('disabled', true);
cpdca_journal_input.parent().addClass('disabled');
cpdca_journal_input.closest('.journal-wrapper').addClass('disabled');

cpdca_assignment_input.bind( 'click', function() {

	var checked = false;
	cpdca_assignment_input.each(function() {
		if( jQuery(this).prop('checked') )
		{
			checked = true;
		}
	});

	if( checked )
	{
		cpdca_journal_input.prop('disabled', false);
		cpdca_journal_input.parent().removeClass('disabled');
		cpdca_journal_input.closest('.journal-wrapper').removeClass('disabled');
	}
	else
	{
		cpdca_journal_input.prop('disabled', true);
		cpdca_journal_input.parent().addClass('disabled');
		cpdca_journal_input.closest('.journal-wrapper').addClass('disabled');
	}

});