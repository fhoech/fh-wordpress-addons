(function ($) {

	var previous_url = History.getState().url,
		page = 0;

	History.replaceState({'page': page}, document.title, location.href);

	$.fn.extend({
		timeout: function (fn, ms) {
			this.each(function () {
				this._timeout = setTimeout($.proxy(fn, this), ms);
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

	function get_query(href, start_index) {
		var params = href.split(/\?/).pop().split(/&/).slice(start_index).join('&');
		return params ? '?' + params : '';
	};

	function log() {
		if (window.console && typeof console.log == 'function') console.log.apply(console, arguments);
	};

	function ready() {
		if ($('#hyper-cache-utility .overview').length) mainready();
		if ($('#hyper-cache-utility .view-single').length) viewready();
		$('.hyper-cache-utility-content').css('opacity', 1);
	};

	function mainready() {
		var offset = $('thead').offset(),
			offsetTop = offset ? offset.top : 0,
			adminbar_height = $('#wpadminbar').height(),
			laststate = 'hidden';

		// Table sorting
		$('#hyper-cache-utility table').bind('initialized sortEnd', function () {
			setTimeout(function () {
				var sort_columns = $('#hyper-cache-utility thead [class*="tablesorter-sort-column-"]').filter('.tablesorter-headerAsc, .tablesorter-headerDesc');
				$('#hyper-cache-utility th span[data-sort-column]').removeAttr('data-sort-column');
				if (sort_columns.length > 2) {  // Remember: We have two headers (normal + sticky)
					sort_columns.each(function () {
						var column = $(this).attr('class').match(/tablesorter-sort-column-(\d+)/);
						$(this).find('span').attr('data-sort-column', column[1]);
					});
				}
			}, 50);
		});
		$('#hyper-cache-utility table th:not(.status, .cache-filedate, .cache-filesize, .options)').data('sorter', 'text');
		$('#hyper-cache-utility table th.cache-filedate').data('sorter', 'isoDate');
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
			usNumberFormat: hyper_cache_utility.usNumberFormat,
			widgets: ['columns', 'filter', 'resizable', 'saveSort', 'stickyHeaders'],
			widgetOptions: {
			  columns: $.map(new Array($('#hyper-cache-utility table:first thead tr:first th').length), function (e, i) {
				  return 'tablesorter-sort-column-' + (i + 1);
			  }),
			  stickyHeaders: 'tablesorter-stickyHeader',
			  stickyHeaders_offset: adminbar_height
			}
		}).bind('pagerBeforeInitialized', function () {
			$('.tablesorter-filter-row').find('td:last').append('<button>' + hyper_cache_utility.resetText + '</button>');
			$('.tablesorter-filter-row').find('td:last button').click(function () {
				$('#hyper-cache-utility table.hasStickyHeaders').trigger('filterReset');
			});
		}).tablesorterPager({
			// target the pager markup - see the HTML block below
			container: $(".pager"),
			// output string - default is '{page}/{totalPages}'; possible variables: {page}, {totalPages}, {startRow}, {endRow} and {totalRows}
			output: hyper_cache_utility.pagerOutput,
			// if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
			// table row set to a height to compensate; default is false
			fixedHeight: true,
			// remove rows from the table to speed up the sort of large tables.
			// setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
			removeRows: false,
			// go to page selector - select dropdown that sets the current page
			cssGoto:   '.gotoPage'
		}).trigger('pageSet', page + 1).bind('pageMoved', function (e, c) {
			log('pageMoved ' + page + ' -> ' + c.page);
			// c.totalPages contains the total number of pages
			page = c.page;
			History.pushState({'page': page}, document.title, location.href);
			$(window).resize();
		});

		// Fix sticky header position & cell dimensions
		var $table = $('#hyper-cache-utility table.hasStickyHeaders');
		log('#hyper-cache-utility table.hasStickyHeaders ' + $table.get(0));
		if ($table.length) {
			function fix_table_width() {
				if ($table[0].style.width) return;
				var w = $table[0].offsetWidth + 'px';
				log('fix_table_width ' + w);
				$table[0].style.width = w;
				$('#hyper-cache-utility table.containsStickyHeaders')[0].style.width = w;
			};
			function fix_table_dimensions() {
				log('fix_table_dimensions');
				var widths = [], heights = [],
					$inner = $('#hyper-cache-utility table.hasStickyHeaders thead .tablesorter-header-inner'),
					$this;
				$inner.each(function (i) {
					// Step 1: Record the dimensions
					$this = $(this);
					widths[i] = $this.width(),
					heights[i] = $this.height();
				});
				$inner.each(function (i) {
					// Step 2: Set the dimensions
					$this = $(this);
					$this.width(widths[i]);
					$this.height(heights[i]);
					$('#hyper-cache-utility table.containsStickyHeaders thead .tablesorter-header-inner').eq(i).width(widths[i]).height(heights[i]);
				});
				fix_table_width();
			};
			function reset_table_dimensions(reset_table_width) {
				log('reset_table_dimensions reset_table_width = ' + (reset_table_width !== false));
				$('#hyper-cache-utility table.hasStickyHeaders thead .tablesorter-header-inner').each(function (i) {
					var $this = $(this);
					$this.width('');
					$this.height('');
				});
				if (reset_table_width !== false) $table.width('');
			};
			if ($('#hyper-cache-utility table.containsStickyHeaders').length) $(window).unbind('scroll.tsSticky resize.tsSticky').bind('scroll.tsSticky resize.tsSticky', function (e) {
				if (e.target == $table[0] || $table.is(':hidden')) {
					log(e.type + ' ' + e.target + ' ' + e.currentTarget + ' ' + e.relatedTarget);
					return;
				}
				if ($(window).scrollTop() + adminbar_height > offsetTop) {
					$('#hyper-cache-utility table.containsStickyHeaders').css({'left': 'auto', 'margin-left': -$(window).scrollLeft() + 'px'})
					if (e.type == 'resize' || laststate == 'hidden') {
						log(e.type + ' ' + e.target);
						reset_table_dimensions();
						fix_table_dimensions();
					}
					if (laststate == 'hidden') {
						$('#hyper-cache-utility table.containsStickyHeaders').css('visibility', 'visible');
						laststate = 'visible';
					}
				}
				else {
					if (e.type == 'resize') {
						log(e.type + ' ' + e.target);
						reset_table_dimensions();
						fix_table_dimensions();
						reset_table_dimensions(false);
					}
					if (laststate == 'visible') {
						$('#hyper-cache-utility table.containsStickyHeaders').css('visibility', 'hidden');
						laststate = 'hidden';
					}
				}
			});

			$(window).resize();

			// Table column resizing
			$('#hyper-cache-utility th.uri .tablesorter-resizer').mousedown(function () {
				$('#hyper-cache-utility tbody:visible td.uri a span').addClass('disabled').css('max-width', '300px');
			});
			$('body, #hyper-cache-utility th').mouseup(function () {
				$('#hyper-cache-utility tbody:visible td.uri a span').removeClass('disabled').css('max-width', $('#hyper-cache-utility tbody td.uri:visible:first').width() + 'px');
			});
		}

		// Custom CSS title tooltips
		$('#hyper-cache-utility [title]:not(button)').each(function () {
			var scrollW = document.documentElement.scrollWidth;
			$(this).attr('data-title', $(this).attr('title'));
			if (document.documentElement.scrollWidth > scrollW) log('Warning: The following element\'s tooltip exceeds the document\'s scroll width: ' + this.outerHTML);
		}).removeAttr('title');

		// Ajax & history
		$(document).ajaxError(function (jqXHR, textStatus, thrownError) {
			alert(thrownError);
		});
		$('#hyper-cache-utility [class^="delete"]').click(function () {
			$.get(hyper_cache_utility.ajax_uri + get_query(this.href, 1), function (response, textStatus, jqXHR) {
				if (response.responseText) response = response.responseText;
				if ((response + '').match(/\S/)) {
					alert('Unexpected data returned from AJAX call:\n' + $('<p>' + response + '</p>').text());
				}
				else if (textStatus == 'success') {
					var count = jqXHR.getResponseHeader('X-HyperCache-Count'),
						deleted = jqXHR.getResponseHeader('X-HyperCache-Deleted'),
						expired = jqXHR.getResponseHeader('X-HyperCache-Expired-Count'),
						status301 = jqXHR.getResponseHeader('X-HyperCache-Status-301-Count'),
						status404 = jqXHR.getResponseHeader('X-HyperCache-Status-404-Count');
					if (deleted == 'all')
						count = expired = status301 = status404 = 0;
					else if (deleted == 'expired')
						expired = 0;
					else if (deleted == 'status-404')
						status404 = 0;
					else {
						count = parseInt($('#hyper-cache-utility .count').text()) - 1;
						expired = parseInt($('#hyper-cache-utility .expired-count').text());
						status301 = parseInt($('#hyper-cache-utility .status-301-count').text());
						status404 = parseInt($('#hyper-cache-utility .status-404-count').text());
						$('#' + deleted.replace(/=/, '-')).hasClass('expired') && expired --;
						$('#' + deleted.replace(/=/, '-')).hasClass('status-301') && status301 --;
						$('#' + deleted.replace(/=/, '-')).hasClass('status-404') && status404 --;
					}
					$('#hyper-cache-utility .count').html(count);
					$('#hyper-cache-utility .expired-count').html(expired);
					$('#hyper-cache-utility .status-301-count').html(status301);
					$('#hyper-cache-utility .status-404-count').html(status404);
					if (!parseInt(expired)) $('#hyper-cache-utility .delete-expired').fadeOut();
					if (!parseInt(status404)) $('#hyper-cache-utility .delete-status-404').fadeOut();
					if (!parseInt(count)) $('#hyper-cache-utility .delete-all, #hyper-cache-utility table, #hyper-cache-utility .pager').fadeOut(function () {
						var $table = $('#hyper-cache-utility table.hasStickyHeaders');
						$table.find('tbody tr').remove();
						$table.trigger('disable.pager');
					});
					else {
						function callback() {
							$(this).remove();
							if (deleted == 'hash=_archives' || deleted == 'hash=_global') {
								if (deleted == 'hash=_global') hyper_cache_invalidation_global_time = 0;
								else if (deleted == 'hash=_archives') hyper_cache_invalidation_archives_time = 0;
								$('#hyper-cache-utility tbody > tr').each(function () {
									var $this = $(this),
										$time = $(this).find('time'),
										hc_file_time = new Date($time.attr('datetime')) / 1000,
										hc_file_age = hyper_cache_utility_time - hc_file_time;
									if (hc_file_age <= hyper_cache_timeout &&
										(isNaN(hyper_cache_invalidation_global_time) || hc_file_time >= hyper_cache_invalidation_global_time) &&
										((!$this.hasClass('type-blog') && !$this.hasClass('type-home') && !$this.hasClass('type-archive') && !$this.hasClass('type-feed')) ||
										 (isNaN(hyper_cache_invalidation_archives_time) || hc_file_time >= hyper_cache_invalidation_archives_time))) $(this).removeClass('expired');
								});
							}
						};
						if (deleted == 'expired' || deleted == 'status=404')
							$('#hyper-cache-utility tbody > tr' + (deleted == 'expired' ? '.expired' : '.status-404')).addClass('zoom-out').timeout(callback, 500);
						else if (deleted != 'all')
							$('#' + deleted.replace(/=/, '-')).addClass('zoom-out').timeout(callback, 500);
						if (count) $('#hyper-cache-utility table.hasStickyHeaders').timeout(function () {
							$(this).trigger('disable.pager').trigger('enable.pager');
						}, 500);
					}
				}
			});
			return false;
		});
		$('#hyper-cache-utility .view').click(function () {
			History.pushState(null, document.title, this.href);
			return false;
		});

	};
	
	function viewready($contentelement) {
		if (!$contentelement) $contentelement = $('#hyper-cache-utility .view-single');
		$contentelement.find('.back').click(function () {
			if (history.length > 1) History.back();
			else History.pushState(null, document.title, this.href);
			return false;
		});

		Prism.highlightElement($contentelement.find('pre.language-markup code').get(0));
	};
	
	function update_content(state, newcontent) {
		var $newcontent,
			$scripts;
		$('.hyper-cache-utility-content:visible').hide();
		if (newcontent) {
			if ($('#hyper-cache-utility .view-single').length == 5)
				$('#hyper-cache-utility .view-single:first').remove();  // Keep at most 5
			$newcontent = $(newcontent);
			$newcontent.css('opacity', 0);
			$scripts = $newcontent.find('.script').detach();
			$('#hyper-cache-utility').append($newcontent);
			if ($scripts) $scripts.each(function(){
				var $script = $(this),
					scriptText = $script.text(),
					scriptNode = document.createElement('script');
				if ($script.attr('src')) {
					if (!$script[0].async) scriptNode.async = false;
					scriptNode.src = $script.attr('src');
				}
				scriptNode.appendChild(document.createTextNode(scriptText));
				$('[data-uri$="' + state.url + '"]')[0].appendChild(scriptNode);
			});
			if ($newcontent.hasClass('overview')) mainready();
			if ($newcontent.hasClass('view-single')) viewready($newcontent);
		}
		$('[data-uri$="' + state.url + '"]').show().css('opacity', 1);
		previous_url = state.url;
	};

	History.Adapter.bind(window, 'statechange', function() {
		var $content, state = History.getState();
		log('statechange ', state);
		if (state.url != previous_url) {
			log(previous_url + ' -> ' + state.url);
			if ($('[data-uri$="' + state.url + '"]').length) update_content(state);
			else {
				$content = $('.hyper-cache-utility-content:visible');
				$content.css('opacity', .5);
				$.get(hyper_cache_utility.ajax_uri + get_query(state.url, 1), function (response, textStatus, jqXHR) {
					var content = response.replace(/<(script)(\s+[^<]*|>)/g, '<div class="$2"$3').replace(/<\/(script)\s*>/g, '</div>').match(/<div class="hyper-cache-utility-content[\S\s]*<\/div><!-- hyper-cache-utility-content -->/);
					if (content) $content.css('opacity', 0).timeout(function () {
						update_content(state, content[0]);
					}, 500);
					else {
						$content.css('opacity', 1);
						alert('Unexpected data returned from AJAX call');
					}
				});
			}
		}
		else if (state.data.page != null && state.data.page != page) {
			log('page ' + page + ' -> ' + state.data.page);
			$('.hyper-cache-utility-content table.hasStickyHeaders').trigger('pageSet', state.data.page + 1);
			page = state.data.page;
		}
	});

	document.removeEventListener('DOMContentLoaded', Prism.highlightAll)

	$(ready);

})(jQuery);
