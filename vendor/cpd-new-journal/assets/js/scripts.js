jQuery('table input.check').each(function(){
	if( jQuery(this).prop('checked') )
	{
		jQuery(this).closest('tr').find('.menu').removeClass('disabled').prop('disabled', false);
		if( jQuery(this).closest('tr').find('.menu').prop('checked') )
		{
			jQuery(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
		}
	}
	else
	{
		jQuery(this).closest('tr').find('.menu').addClass('disabled').prop('disabled', true);
		jQuery(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
	}
});

jQuery('table input.check').click(function(){
	if( jQuery(this).prop('checked') )
	{
		jQuery(this).closest('tr').find('.menu').removeClass('disabled').prop('disabled', false);
		if( jQuery(this).closest('tr').find('.menu').prop('checked') )
		{
			jQuery(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
		}
	}
	else
	{
		jQuery(this).closest('tr').find('.menu').addClass('disabled').prop('disabled', true);
		jQuery(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
	}
});

jQuery('table input.menu').click(function(){
	if( jQuery(this).prop('checked') )
	{
		jQuery(this).closest('tr').find('.menu_order').removeClass('disabled').prop('disabled', false);
	}
	else
	{
		jQuery(this).closest('tr').find('.menu_order').addClass('disabled').prop('disabled', true);
	}
});

jQuery('table input.menu_order').blur(function(){
	if( !jQuery.isNumeric( jQuery(this).val() ) )
	{
		jQuery(this).val(0);
	}
});