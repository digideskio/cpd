jQuery(document).ready(function( $ ) {
	'use strict';

	var text = $('label[for=user_login]').html();
	text = text.replace('Username', 'Email or Username');
	$('label[for=user_login]').html(text);
});