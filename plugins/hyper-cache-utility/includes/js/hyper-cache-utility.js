jQuery.fn.extend({
	timeout: function (fn, ms) {
		this.each(function () {
			this._timeout = setTimeout(jQuery.proxy(fn, this), ms);
		});
		return this;
	},
	clearTimeout: function () {
		this.each(function () {
			if (this._timeout) {
				clearTimeout(this._timeout);
				delete this._timeout;
			}
		});
		return this;
	}
});

jQuery(function ($) {

	var offsetTop = $('thead').offset().top,
		adminbar_height = $('#wpadminbar').height();

	// Table sorting
	$('#hyper-cache-utility table th:not(.status, .cache-filedate, .cache-filesize, .options)').data('sorter', 'text');
	$('#hyper-cache-utility table th.cache-filesize').data('sorter', 'digit');
	$('#hyper-cache-utility table th.options').data('sorter', false);
	$('#hyper-cache-utility table').tablesorter({
		sortReset: true,
		sortRestart: true,
		textSorter : function (a, b, table, column) {
			if (column == 0) {
				a = a.replace(/\w+\.dat\s*$/, '');
				b = b.replace(/\w+\.dat\s*$/, '');
			}
			if (table.config.sortLocaleCompare) return a.localeCompare(b);
			return a < b ? -1 : (a > b ? 1 : 0);
		},
		usNumberFormat: usNumberFormat,
		widgets: ['saveSort', 'stickyHeaders'],
		widgetOptions: {
		  stickyHeaders: 'tablesorter-stickyHeader',
		  stickyHeaders_offset: adminbar_height
		}
	});

	// Fix sticky header position & width
	var laststate = 'hidden';
	$(window).unbind('scroll.tsSticky resize.tsSticky').bind('scroll.tsSticky resize.tsSticky', function (e) {
		if (e.type == 'resize') {
			// Fix cell widths
			$('#hyper-cache-utility table.hasStickyHeaders thead .tablesorter-header-inner').each(function (i) {
				$(this).width('auto').height('auto');
				var w = $(this).width(),
					h = $(this).height();
				$(this).width(w).height(h)
				$('#hyper-cache-utility table.containsStickyHeaders thead .tablesorter-header-inner').eq(i).width(w).height(h);
			});
		}
		if ($(window).scrollTop() + adminbar_height > offsetTop) {
			$('#hyper-cache-utility table.containsStickyHeaders').css('margin-left', -$(window).scrollLeft() + 'px')
			if (laststate == 'hidden' || e.type == 'resize') {
				$('#hyper-cache-utility table.containsStickyHeaders').css({'left': 'auto', 'visibility': 'visible'});
				laststate = 'visible';
			}
		}
		else if (laststate == 'visible') {
			$('#hyper-cache-utility table.containsStickyHeaders').css('visibility', 'hidden');
			laststate = 'hidden';
		}
	});
	
	$(window).resize();

	// Custom CSS title tooltips
	$('#hyper-cache-utility [title]:not(button)').each(function () {
		var scrollW = document.documentElement.scrollWidth;
		$(this).attr('data-title', $(this).attr('title'));
		if (document.documentElement.scrollWidth > scrollW) alert(this.outerHTML);
	}).removeAttr('title');

	// Ajax form
	$('#hyper-cache-utility form').ajaxForm({
		success: function (responseText, statusText, xhr, $form) {
			if (statusText == 'success') {
				var count = xhr.getResponseHeader('X-HyperCache-Count'),
					deleted = xhr.getResponseHeader('X-HyperCache-Deleted'),
					expired = xhr.getResponseHeader('X-HyperCache-Expired-Count'),
					status301 = xhr.getResponseHeader('X-HyperCache-Status-301-Count'),
					status404 = xhr.getResponseHeader('X-HyperCache-Status-404-Count');
				$('#hyper-cache-utility .count').html(count);
				$('#hyper-cache-utility .expired-count').html(expired);
				$('#hyper-cache-utility .status-301-count').html(status301);
				$('#hyper-cache-utility .status-404-count').html(status404);
				if (!parseInt(expired)) $('#hyper-cache-utility .delete-expired').fadeOut();
				if (!parseInt(status404)) $('#hyper-cache-utility .delete-status-404').fadeOut();
				if (!parseInt(count)) $('#hyper-cache-utility .delete-all, #hyper-cache-utility table').fadeOut();
				else {
					function callback() {
						$(this).remove();
						if (deleted == 'hash=_archives' || deleted == 'hash=_global') {
							if (deleted == 'hash=_global') hc_invalidation_global_time = 0;
							else if (deleted == 'hash=_archives') hc_invalidation_archives_time = 0;
							$('#hyper-cache-utility tbody > tr').each(function () {
								var $this = $(this),
									$time = $(this).find('time'),
									hc_file_time = new Date($time.attr('datetime')) / 1000,
									hc_file_age = time - hc_file_time;
								if (hc_file_age <= hyper_cache_timeout &&
									(isNaN(hc_invalidation_global_time) || hc_file_time >= hc_invalidation_global_time) &&
									((!$this.hasClass('type-blog') && !$this.hasClass('type-home') && !$this.hasClass('type-archive') && !$this.hasClass('type-feed')) ||
									 (isNaN(hc_invalidation_archives_time) || hc_file_time >= hc_invalidation_archives_time))) $(this).removeClass('expired');
							});
						}
					};
					if (deleted == 'expired' || deleted == 'status=404')
						$('#hyper-cache-utility tbody > tr' + (deleted == 'expired' ? '.expired' : '.status-404')).addClass('zoom-out').timeout(callback, 500);
					else if (deleted != 'all')
						$('#' + deleted.replace(/=/, '-')).addClass('zoom-out').timeout(callback, 500);
				}
			}
		}
	});

});
