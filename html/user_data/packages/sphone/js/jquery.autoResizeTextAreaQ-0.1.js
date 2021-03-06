/*
 * jQuery autoResizeTextAreaQ plugin
 * @requires jQuery v1.4.2 or later
 *
 * Copyright (c) 2010 M. Brown (mbrowniebytes A gmail.com)
 * Licensed under the Revised BSD license:
 * http://www.opensource.org/licenses/bsd-license.php
 * http://en.wikipedia.org/wiki/BSD_licenses 
 *
 * Versions:
 * 0.1 - 2010-07-21
 *       initial
 * 
 * usage:
 *  $(document).ready( function() {
 *      $('textarea').autoResizeTextAreaQ({"max_rows":8});
 *  });
 *
 */

(function($) {
	
$.fn.autoResizeTextAreaQ = function(options) {
	var opts = $.extend({
		// ya prob want to use
		max_rows: 10,	// # - max rows to resize too
		
		// ya may want to use, but defaults should be ok
		extra_rows: 1,  // # - nbr extra rows after last line ie padding; 0|1 optimal
		
		// ya should really specify in html
		rows: null,		// null|# - null infer from html; # override html
		cols: null,		// null|# - null infer from html; # override html
		
		debug: false	// true|false - turn on|off console.log()
	}, options);
	
	// extra padding based on browser
	if ($.browser.msie) {
		opts.extra_rows += 1;
	} else if ($.browser.webkit) {
		opts.extra_rows += 1;
	} // else $.browser.mozilla
			
	// iterate over passed in selector, only process actual textareas
	return $(this).filter('textarea').each(function(index) {

		var ta = $(this);
		var orig = {};
		
		// textarea rows cols current state
		if (opts.cols != null && opts.cols > 0) {				
			ta.attr('cols', opts.cols);
		}
		orig.cols = ta.attr('cols');
					
		if (opts.rows != null && opts.rows > 0) {				
			ta.attr('rows', opts.rows);
		}
		orig.rows = ta.attr('rows');
		
		// validate max extra_rows
		if (opts.max_rows == null || opts.max_rows < orig.rows) {
			opts.max_rows = orig.rows;
		}
		if (opts.extra_rows == null || opts.extra_rows < 0) {
			opts.extra_rows = 0;
		}
		
		if (opts.debug) {
			console.log('opts: ', opts, ' orig: ', orig);
		}

		// resize textares on load
		resize(ta, orig);
		
		// check resize on key input
		ta.bind('keyup', function(e) {
			resize(ta, orig);
		});			
	}); // end each()

	function resize(ta, orig) {
		
		// nbr explicit rows
		var nl_rows = ta.val().split('\n');
		
		// nbr inferred rows
		var nbr_ta_rows = 0;			
		for (index in nl_rows) {
			// overly simple check to account for text being auto wrapped and thus a new line
			nbr_ta_rows += Math.floor((nl_rows[index].length / orig.cols)) + 1;
		}
		
		// get final nbr ta rows
		var final_nbr_ta_rows = nbr_ta_rows - 1; // deduct for current line
		final_nbr_ta_rows += opts.extra_rows; // add on extra rows
		 
		// resize textarea
		// note: $.animate() doesnt work well here since only inc/dec row by one
		if (final_nbr_ta_rows >= opts.max_rows) {
			ta.attr('rows', opts.max_rows);
							
		} else if (final_nbr_ta_rows >= orig.rows) {
			ta.attr('rows', final_nbr_ta_rows);
			
		} else {
			ta.attr('rows', orig.rows);
		}
		
		if (opts.debug) {
			console.log('rows: ', ta.attr('rows'), ' nbr nl_rows: ', nl_rows.length, ' nbr_ta_rows: ', nbr_ta_rows, ' final_nbr_ta_rows: ', final_nbr_ta_rows);
		}
	} // end resize()

}; // end autoResizeTextAreaQ()

})(jQuery);
