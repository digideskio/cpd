jQuery(document).ready(function($) {
	showRelationships($('#cpd_role').val());
	$('#cpd_role').change(function(e) {
		showRelationships();
	});
	function showRelationships() {
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
	$('.latest_posts_histogram_bar').click( function (e) {
		if($(e.target).next().hasClass('posted_in_week')) {
			$(e.target).next().remove();
		} else {
			$.post(ajaxurl, {action:'posts_in_week', weeks_ago: $(e.target).attr('id').match(/_(\d+)$/)[1]}, 'html')
			.done(function (data) {
				$(e.target).after(data);
			});
		}
	});
	$('.user_posts_barchart_bar').click( function (e) {
		if($(e.target).next().hasClass('posts_by_user')) {
			$(e.target).next().remove();
		} else {
			$.post(ajaxurl, {action:'posts_by_user', user_nicename: $(e.target).attr('id').match(/_([^_]+)$/)[1]}, 'html')
			.done(function (data) {
				$(e.target).after(data);
			});
		}
	});
})

(function( $ ) {
	'use strict';
	
	 var resize_content_count = 0;
	 var resize_rows = 0;

	 function resize_content() {

			var cards = {};
		
			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox, .toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox, .toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

						var offset = $(this).offset();
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
		}

		function resize_inside() {

			var cards = {};
		
			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside, .toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside, .toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

						var offset = $(this).offset();
						
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
		}

		$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .hndle, .toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .handlediv').click(function(){

			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			setTimeout(function() {
				
				$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
				$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');
				$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
				$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

				//if($(this).closest('.postbox').hasClass('closed'))
				{	
					resize_content();
					resize_inside();

					resize_rows = Math.ceil(resize_content_count / 3);

				}
			}, 100);
			
		});

		resize_content();
		resize_inside();

		resize_rows = Math.ceil(resize_content_count / 3);

		$(window).resize(function(){
			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
			$('.toplevel_page_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
			$('.toplevel_page_subscriber_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

			resize_content();
			resize_inside();
			resize_rows = Math.ceil(resize_content_count / 3);
			for( var i = 1; i == resize_rows; i++);
			{
				resize_content();
				resize_inside();
			}
		});

})( jQuery );

(function( $ ) {
	'use strict';
	
	 var resize_content_count = 0;
	 var resize_rows = 0;

	 function resize_content() {

			var cards = {};
		
			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox, .toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox, .toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

						var offset = $(this).offset();
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
		}

		function resize_inside() {

			var cards = {};
		
			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside, .toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside, .toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

						var offset = $(this).offset();
						
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
		}

		$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .hndle, .toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .handlediv').click(function(){

			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			setTimeout(function() {
				
				$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
				$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');
				$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
				$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

				//if($(this).closest('.postbox').hasClass('closed'))
				{	
					resize_content();
					resize_inside();

					resize_rows = Math.ceil(resize_content_count / 3);

				}
			}, 100);
			
		});

		resize_content();
		resize_inside();

		resize_rows = Math.ceil(resize_content_count / 3);

		$(window).resize(function(){
			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
			$('.toplevel_page_supervisor_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
			$('.toplevel_page_participant_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

			resize_content();
			resize_inside();
			resize_rows = Math.ceil(resize_content_count / 3);
			for( var i = 1; i == resize_rows; i++);
			{
				resize_content();
				resize_inside();
			}
		});

})( jQuery );

(function( $ ) {
	'use strict';
	
	 var resize_content_count = 0;
	 var resize_rows = 0;

	 function resize_content() {

			var cards = {};
		
			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').each(function(){

						var offset = $(this).offset();
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
		}

		function resize_inside() {

			var cards = {};
		
			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

				resize_content_count++;

				var offset = $(this).offset();

				if( !(offset.top in cards) )
				{
					var array = [];
					array.push($(this).height());
					cards[offset.top] = array;
				}
				else
				{
					cards[offset.top].push($(this).height());
				}
			});

			for (var key in cards) {
				if( cards[key].length > 1 )
				{
					$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').each(function(){

						var offset = $(this).offset();
						
						if( offset.top == parseFloat(key) || ( offset.top > parseFloat(key) - 100 && offset.top < parseFloat(key) + 100 ) )
						{
							$(this).height( Math.max.apply( Math, cards[key] ) );
						}
					});
				}
			}

			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
		}

		$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .hndle, .toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .handlediv').click(function(){

			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed').removeAttr('style');
			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .closed .inside').removeAttr('style');
			setTimeout(function() {
				
				$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
				$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

				//if($(this).closest('.postbox').hasClass('closed'))
				{	
					resize_content();
					resize_inside();

					resize_rows = Math.ceil(resize_content_count / 3);

				}
			}, 100);
			
		});

		resize_content();
		resize_inside();

		resize_rows = Math.ceil(resize_content_count / 3);

		$(window).resize(function(){
			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox').removeAttr('style');
			$('.toplevel_page_network_dashboard #dashboard-widgets-wrap #dashboard-widgets .postbox-container .postbox .inside').removeAttr('style');

			resize_content();
			resize_inside();
			resize_rows = Math.ceil(resize_content_count / 3);
			for( var i = 1; i == resize_rows; i++);
			{
				resize_content();
				resize_inside();
			}
		});

})( jQuery );
