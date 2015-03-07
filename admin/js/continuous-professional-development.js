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