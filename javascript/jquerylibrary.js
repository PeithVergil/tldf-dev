/*
 * jQuery Tooltip plugin 1.3
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-tooltip/
 * http://docs.jquery.com/Plugins/Tooltip
 *
 * Copyright (c) 2006 - 2008 Jörn Zaefferer
 *
 * $Id: jquery.tooltip.js,v 1.1 2009/01/26 13:09:29 pavel Exp $
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
 
(function($) {
	
		// the tooltip element
	var helper = {},
		// the current tooltipped element
		current,
		// the title of the current element, used for restoring
		title,
		// timeout id for delayed tooltips
		tID,
		// IE 5.5 or 6
		IE = $.browser.msie && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent),
		// flag for mouse tracking
		track = false;
	
	$.tooltip = {
		blocked: false,
		defaults: {
			delay: 200,
			fade: false,
			showURL: true,
			extraClass: "",
			top: 15,
			left: 15,
			id: "tooltip"
		},
		block: function() {
			$.tooltip.blocked = !$.tooltip.blocked;
		}
	};
	
	$.fn.extend({
		tooltip: function(settings) {
			settings = $.extend({}, $.tooltip.defaults, settings);
			createHelper(settings);
			return this.each(function() {
					$.data(this, "tooltip", settings);
					this.tOpacity = helper.parent.css("opacity");
					// copy tooltip into its own expando and remove the title
					this.tooltipText = this.title;
					$(this).removeAttr("title");
					// also remove alt attribute to prevent default tooltip in IE
					this.alt = "";
				})
				.mouseover(save)
				.mouseout(hide)
				.click(hide);
		},
		fixPNG: IE ? function() {
			return this.each(function () {
				var image = $(this).css('backgroundImage');
				if (image.match(/^url\(["']?(.*\.png)["']?\)$/i)) {
					image = RegExp.$1;
					$(this).css({
						'backgroundImage': 'none',
						'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop, src='" + image + "')"
					}).each(function () {
						var position = $(this).css('position');
						if (position != 'absolute' && position != 'relative')
							$(this).css('position', 'relative');
					});
				}
			});
		} : function() { return this; },
		unfixPNG: IE ? function() {
			return this.each(function () {
				$(this).css({'filter': '', backgroundImage: ''});
			});
		} : function() { return this; },
		hideWhenEmpty: function() {
			return this.each(function() {
				$(this)[ $(this).html() ? "show" : "hide" ]();
			});
		},
		url: function() {
			return this.attr('href') || this.attr('src');
		}
	});
	
	function createHelper(settings) {
		// there can be only one tooltip helper
		if( helper.parent )
			return;
		// create the helper, h3 for title, div for url
		helper.parent = $('<div id="' + settings.id + '"><h3></h3><div class="body"></div><div class="url"></div></div>')
			// add to document
			.appendTo(document.body)
			// hide it at first
			.hide();
			
		// apply bgiframe if available
		if ( $.fn.bgiframe )
			helper.parent.bgiframe();
		
		// save references to title and url elements
		helper.title = $('h3', helper.parent);
		helper.body = $('div.body', helper.parent);
		helper.url = $('div.url', helper.parent);
	}
	
	function settings(element) {
		return $.data(element, "tooltip");
	}
	
	// main event handler to start showing tooltips
	function handle(event) {
		// show helper, either with timeout or on instant
		if( settings(this).delay )
			tID = setTimeout(show, settings(this).delay);
		else
			show();
		
		// if selected, update the helper position when the mouse moves
		track = !!settings(this).track;
		$(document.body).bind('mousemove', update);
			
		// update at least once
		update(event);
	}
	
	// save elements title before the tooltip is displayed
	function save() {
		// if this is the current source, or it has no title (occurs with click event), stop
		if ( $.tooltip.blocked || this == current || (!this.tooltipText && !settings(this).bodyHandler) )
			return;

		// save current
		current = this;
		title = this.tooltipText;
		
		if ( settings(this).bodyHandler ) {
			helper.title.hide();
			var bodyContent = settings(this).bodyHandler.call(this);
			if (bodyContent.nodeType || bodyContent.jquery) {
				helper.body.empty().append(bodyContent)
			} else {
				helper.body.html( bodyContent );
			}
			helper.body.show();
		} else if ( settings(this).showBody ) {
			var parts = title.split(settings(this).showBody);
			helper.title.html(parts.shift()).show();
			helper.body.empty();
			for(var i = 0, part; (part = parts[i]); i++) {
				if(i > 0)
					helper.body.append("<br/>");
				helper.body.append(part);
			}
			helper.body.hideWhenEmpty();
		} else {
			helper.title.html(title).show();
			helper.body.hide();
		}
		
		// if element has href or src, add and show it, otherwise hide it
		if( settings(this).showURL && $(this).url() )
			helper.url.html( $(this).url().replace('http://', '') ).show();
		else 
			helper.url.hide();
		
		// add an optional class for this tip
		helper.parent.addClass(settings(this).extraClass);

		// fix PNG background for IE
		if (settings(this).fixPNG )
			helper.parent.fixPNG();
			
		handle.apply(this, arguments);
	}
	
	// delete timeout and show helper
	function show() {
		tID = null;
		if ((!IE || !$.fn.bgiframe) && settings(current).fade) {
			if (helper.parent.is(":animated"))
				helper.parent.stop().show().fadeTo(settings(current).fade, current.tOpacity);
			else
				helper.parent.is(':visible') ? helper.parent.fadeTo(settings(current).fade, current.tOpacity) : helper.parent.fadeIn(settings(current).fade);
		} else {
			helper.parent.show();
		}
		update();
	}
	
	/**
	 * callback for mousemove
	 * updates the helper position
	 * removes itself when no current element
	 */
	function update(event)	{
		if($.tooltip.blocked)
			return;
		
		if (event && event.target.tagName == "OPTION") {
			return;
		}
		
		// stop updating when tracking is disabled and the tooltip is visible
		if ( !track && helper.parent.is(":visible")) {
			$(document.body).unbind('mousemove', update)
		}
		
		// if no current element is available, remove this listener
		if( current == null ) {
			$(document.body).unbind('mousemove', update);
			return;	
		}
		
		// remove position helper classes
		helper.parent.removeClass("viewport-right").removeClass("viewport-bottom");
		
		var left = helper.parent[0].offsetLeft;
		var top = helper.parent[0].offsetTop;
		if (event) {
			// position the helper 15 pixel to bottom right, starting from mouse position
			left = event.pageX + settings(current).left;
			top = event.pageY + settings(current).top;
			var right='auto';
			if (settings(current).positionLeft) {
				right = $(window).width() - left;
				left = 'auto';
			}
			helper.parent.css({
				left: left,
				right: right,
				top: top
			});
		}
		
		var v = viewport(),
			h = helper.parent[0];
		// check horizontal position
		if (v.x + v.cx < h.offsetLeft + h.offsetWidth) {
			left -= h.offsetWidth + 20 + settings(current).left;
			helper.parent.css({left: left + 'px'}).addClass("viewport-right");
		}
		// check vertical position
		if (v.y + v.cy < h.offsetTop + h.offsetHeight) {
			top -= h.offsetHeight + 20 + settings(current).top;
			helper.parent.css({top: top + 'px'}).addClass("viewport-bottom");
		}
	}
	
	function viewport() {
		return {
			x: $(window).scrollLeft(),
			y: $(window).scrollTop(),
			cx: $(window).width(),
			cy: $(window).height()
		};
	}
	
	// hide helper and restore added classes and the title
	function hide(event) {
		if($.tooltip.blocked)
			return;
		// clear timeout if possible
		if(tID)
			clearTimeout(tID);
		// no more current element
		current = null;
		
		var tsettings = settings(this);
		function complete() {
			helper.parent.removeClass( tsettings.extraClass ).hide().css("opacity", "");
		}
		if ((!IE || !$.fn.bgiframe) && tsettings.fade) {
			if (helper.parent.is(':animated'))
				helper.parent.stop().fadeTo(tsettings.fade, 0, complete);
			else
				helper.parent.stop().fadeOut(tsettings.fade, complete);
		} else
			complete();
		
		if( settings(this).fixPNG )
			helper.parent.unfixPNG();
	}
	
})(jQuery);

/*
 * END: jQuery Tooltip plugin 1.3
 */

/*
 * 	Easy Slider 1.7 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
/*
 *	markup example for $("#slider").easySlider();
 *	
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {

	$.fn.easySlider = function(options){
	  
		// default configuration properties
		var defaults = {			
			prevId: 		'prevBtn',
			prevText: 		'Previous',
			nextId: 		'nextBtn',	
			nextText: 		'Next',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',	
			controlsFade:	true,
			animateFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',	
			lastText: 		'Last',
			lastShow:		false,				
			vertical:		false,
			speed: 			800,
			auto:			false,
			pause:			2000,
			continuous:		false, 
			numeric: 		false,
			numericId: 		'controls'
		}; 
		
		var options = $.extend(defaults, options);  
				
		this.each(function() {  
			var obj = $(this); 				
			var s = $("li", obj).length;
			var w = $("li", obj).width(); 
			var h = $("li", obj).height(); 
			var clickable = true;
			obj.width(w); 
			obj.height(h); 
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			$("ul", obj).css('width',s*w);			
			
			if(options.continuous){
				$("ul", obj).prepend($("ul li:last-child", obj).clone().css("margin-left","-"+ w +"px"));
				$("ul", obj).append($("ul li:nth-child(2)", obj).clone());
				$("ul", obj).css('width',(s+1)*w);
			}			
			
			if(!options.vertical) $("li", obj).css('float','left');
								
			if(options.controlsShow){
				var html = options.controlsBefore;				
				if(options.numeric){
					html += '<ol id="'+ options.numericId +'"></ol>';
				} else {
					if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
					html += ' <span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
					html += ' <span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
					if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';				
				}
				
				html += options.controlsAfter;						
				$(obj).after(html);										
			}
			
			if(options.numeric){									
				for(var i=0;i<s;i++){						
					$(document.createElement("li"))
						.attr('id',options.numericId + (i+1))
						.html('<a rel='+ i +' href=\"javascript:void(0);\">'+ (i+1) +'</a>')
						.appendTo($("#"+ options.numericId))
						.click(function(){							
							animate($("a",$(this)).attr('rel'),true);
						}); 												
				}
			} else {
				$("a","#"+options.nextId).click(function(){		
					animate("next",true);
				});
				$("a","#"+options.prevId).click(function(){		
					animate("prev",true);				
				});	
				$("a","#"+options.firstId).click(function(){		
					animate("first",true);
				});				
				$("a","#"+options.lastId).click(function(){		
					animate("last",true);				
				});				
			}
			
			function setCurrent(i){
				i = parseInt(i)+1;
				$("li", "#" + options.numericId).removeClass("current");
				$("li#" + options.numericId + i).addClass("current");
			}
			
			function adjust(){
				if(t>ts) t=0;		
				if(t<0) t=ts;	
				if(!options.vertical) {
					$("ul",obj).css("margin-left",(t*w*-1));
				} else {
					$("ul",obj).css("margin-left",(t*h*-1));
				}
				
				clickable = true;
				if(options.numeric) setCurrent(t);
			}
			
			function animate(dir,clicked){
				if (clickable){
					clickable = false;
					var ot = t;				
					switch(dir){
						case "next":
							t = (ot>=ts) ? (options.continuous ? t+1 : ts) : t+1;						
							break; 
						case "prev":
							t = (t<=0) ? (options.continuous ? t-1 : 0) : t-1;
							break; 
						case "first":
							t = 0;
							break; 
						case "last":
							t = ts;
							break; 
						default:
							t = dir;
							break; 
					}
					var diff = Math.abs(ot-t);
					var speed = diff*options.speed;						
					if(!options.vertical) {
						p = (t*w*-1);
						
						if(options.animateFade==true){
							$("ul",obj).animate(
								{ opacity: 0 }, 
								{ queue:false, duration:speed, complete:function(){
									adjust();
									$("ul",obj).css({marginLeft: p}).animate({ opacity: 1 });
								} }
							);
						}else{
							$("ul",obj).animate(
								{ marginLeft: p }, 
								{ queue:false, duration:speed, complete:adjust }
							);
						}				
					} else {
						p = (t*h*-1);
						if(options.animateFade==true){
							$("ul",obj).animate(
								{ opacity: 0 }, 
								{ queue:false, duration:speed, complete:function(){
									adjust();
									$("ul",obj).css({marginTop: p}).animate({ opacity: 1 });
								} }
							);
						}else{
							$("ul",obj).animate(
								{ marginTop: p }, 
								{ queue:false, duration:speed, complete:adjust }
							);
						}
					}
					
					if(!options.continuous && options.controlsFade){					
						if(t==ts){
							$("a","#"+options.nextId).hide();
							$("a","#"+options.lastId).hide();
						} else {
							$("a","#"+options.nextId).show();
							$("a","#"+options.lastId).show();					
						}
						if(t==0){
							$("a","#"+options.prevId).hide();
							$("a","#"+options.firstId).hide();
						} else {
							$("a","#"+options.prevId).show();
							$("a","#"+options.firstId).show();
						}
					}			
					
					if(clicked) clearTimeout(timeout);
					if(options.auto && dir=="next" && !clicked){
						timeout = setTimeout(function(){
							animate("next",false);
						},diff*options.speed+options.pause);
					}
				}
			}
			// init
			var timeout;
			if(options.auto){
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			}
			
			if(options.numeric) setCurrent(0);
		
			if(!options.continuous && options.controlsFade){					
				$("a","#"+options.prevId).hide();
				$("a","#"+options.firstId).hide();				
			}
		});
	};
})(jQuery);
/*
 * END: Easy Slider 1.7 - jQuery plugin
 */

/*
 * START: jQuery alertr.js
 */
(function($) {
	$.fn.alertr = function(alertDuration) {
		var element = this;
		if ($(element).length) {
			$(element).fadeIn('slow');
			if (alertDuration) {
				var alerttimer = window.setTimeout(function() {
					$(element).trigger('click');
				}, alertDuration);
			}
			$(element).click(function() {
				window.clearTimeout(alerttimer);
				$(element).fadeOut('slow');
			});
		}
	};
})(jQuery);

// Center Function
jQuery.fn.center = function(parent) {
    if (parent) {
        parent = this.parent();
    } else {
        parent = window;
    }
    this.css({
        "position": "absolute",
        "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
        "left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
};

// New Center Function
jQuery.fn.centernew = function() {
    this.css({
        "position": "absolute",
        "left": (((1000 - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
    });
	return this;
};
/*
 * END: jQuery alertr.js
 */

/*
 * START: jquery.alerts.js
 */
// jQuery Alert Dialogs Plugin
//
// Version 1.1
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 14 May 2009
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//		jAlert( message, [title, callback] )
//		jConfirm( message, [title, callback] )
//		jPrompt( message, [value, title, callback] )
// 
// History:
//
//		1.00 - Released (29 December 2008)
//
//		1.01 - Fixed bug where unbinding would destroy all resize events
//
// License:
// 
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC. 
//
(function($) {
	
	$.alerts = {
		
		// These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time
		
		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .01,                // transparency level of overlay
		overlayColor: '#FFF',               // base color of overlay
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		closeButton: '&nbsp;Close&nbsp;',   // text for the Close button (added by RS)
		okButton: '&nbsp;OK&nbsp;',         // text for the OK button
		cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		
		// Public methods
		
		alert: function(message, title, callback) {
			if( title == null ) title = 'Alert';
			$.alerts._show(title, message, null, 'alert', function(result) {
				if( callback ) callback(result);
			});
		},
		
		confirm: function(message, title, callback) {
			if( title == null ) title = 'Confirm';
			$.alerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			});
		},
			
		prompt: function(message, value, title, callback) {
			if( title == null ) title = 'Prompt';
			$.alerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			});
		},
		
		// Private methods
		
		_show: function(title, msg, value, type, callback) {
			
			$.alerts._hide();
			$.alerts._overlay('show');
			
			$("BODY").append(
			  '<div id="popup_container">' +
			    '<h1 id="popup_title"></h1>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.alerts.dialogClass ) $("#popup_container").addClass($.alerts.dialogClass);
			
			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			$("#popup_title").text(title);
			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			// RS 2012/09/03 conversion of \n to <br /> disabled so we won't add any extra line breaks
			// $("#popup_message").html( $("#popup_message").text().replace(/\n/g, '<br />') );
			$("#popup_message").html( $("#popup_message").text() );
			
			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth(),
				maxWidth: $("#popup_container").outerWidth()
			});
			
			$.alerts._reposition();
			$.alerts._maintainPosition(true);
			
			switch( type ) {
				case 'alert':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.closeButton + '" id="popup_ok" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress( function(e) {
						if( e.keyCode == 13 || e.keyCode == 27 ) $("#popup_ok").trigger('click');
					});
				break;
				case 'confirm':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
				break;
				case 'prompt':
					$("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.alerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
			}
			
			// Make draggable
			if( $.alerts.draggable ) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			$("#popup_container").remove();
			$.alerts._overlay('hide');
			$.alerts._maintainPosition(false);
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.alerts._overlay('hide');
					$("BODY").append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: $(document).height(),
						background: $.alerts.overlayColor,
						opacity: $.alerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
			
			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#popup_overlay").height( $(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( $.alerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', $.alerts._reposition);
					break;
					case false:
						$(window).unbind('resize', $.alerts._reposition);
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
	jAlert = function(message, title, callback) {
		$.alerts.alert(message, title, callback);
	}
	
	jConfirm = function(message, title, callback) {
		$.alerts.confirm(message, title, callback);
	};
		
	jPrompt = function(message, value, title, callback) {
		$.alerts.prompt(message, value, title, callback);
	};
	
})(jQuery);
/*
 * END: jquery.alerts.js
 */

/*
 * START: jQuery popup.js
 */
// RS: used in express_interest_table.tpl, platinum_table.tpl
// for showing congratulation popup.
// suggestion is to replace this with jAlert

/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popupContact").fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#popupContact").fadeOut("slow");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#popupContact").height();
	var popupWidth = $("#popupContact").width();
	//centering
	$("#popupContact").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}

//VP initializing popup
function create_popup()
{
	//centering with css
	centerPopup();
	//load popup
	loadPopup();
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	/*
	$("#button").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup();
	});
	*/
	
	//CLOSING POPUP
	//Click the x event!
	$("#popupContactClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});
/*
 * END: jQuery popup.js
 */

/*
 * START: jquery.bxSlider.js
 */
/**
 * jQuery bxSlider v3.0
 * http://bxslider.com
 *
 * Copyright 2011, Steven Wanderski
 * http://bxcreative.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */


(function($){
	
	$.fn.bxSlider = function(options){		
				
		var defaults = {
			mode: 'horizontal',									// 'horizontal', 'vertical', 'fade'
			infiniteLoop: false,									// true, false - display first slide after last
			hideControlOnEnd: false,						// true, false - if true, will hide 'next' control on last slide and 'prev' control on first
			controls: true,											// true, false - previous and next controls
			speed: 500,													// integer - in ms, duration of time slide transitions will occupy
			easing: 'swing',                    // used with jquery.easing.1.3.js - see http://gsgd.co.uk/sandbox/jquery/easing/ for available options
			pager: false,												// true / false - display a pager
			pagerSelector: null,								// jQuery selector - element to contain the pager. ex: '#pager'
			pagerType: 'full',									// 'full', 'short' - if 'full' pager displays 1,2,3... if 'short' pager displays 1 / 4
			pagerLocation: 'bottom',						// 'bottom', 'top' - location of pager
			pagerShortSeparator: '/',						// string - ex: 'of' pager would display 1 of 4
			pagerActiveClass: 'pager-active',		// string - classname attached to the active pager link
			nextText: 'next',										// string - text displayed for 'next' control
			nextImage: '',											// string - filepath of image used for 'next' control. ex: 'images/next.jpg'
			nextSelector: null,									// jQuery selector - element to contain the next control. ex: '#next'
			prevText: 'prev',										// string - text displayed for 'previous' control
			prevImage: '',											// string - filepath of image used for 'previous' control. ex: 'images/prev.jpg'
			prevSelector: null,									// jQuery selector - element to contain the previous control. ex: '#next'
			captions: false,										// true, false - display image captions (reads the image 'title' tag)
			captionsSelector: null,							// jQuery selector - element to contain the captions. ex: '#captions'
			auto: false,												// true, false - make slideshow change automatically
			autoDirection: 'next',							// 'next', 'prev' - direction in which auto show will traverse
			autoControls: false,								// true, false - show 'start' and 'stop' controls for auto show
			autoControlsSelector: null,					// jQuery selector - element to contain the auto controls. ex: '#auto-controls'
			autoStart: true,										// true, false - if false show will wait for 'start' control to activate
			autoHover: false,										// true, false - if true show will pause on mouseover
			autoDelay: 0,                       // integer - in ms, the amount of time before starting the auto show
			pause: 3000,												// integer - in ms, the duration between each slide transition
			startText: 'start',									// string - text displayed for 'start' control
			startImage: '',											// string - filepath of image used for 'start' control. ex: 'images/start.jpg'
			stopText: 'stop',										// string - text displayed for 'stop' control
			stopImage: '',											// string - filepath of image used for 'stop' control. ex: 'images/stop.jpg'
			ticker: false,											// true, false - continuous motion ticker mode (think news ticker)
																					// note: autoControls, autoControlsSelector, and autoHover apply to ticker!
			tickerSpeed: 5000,								  // float - use value between 1 and 5000 to determine ticker speed - the smaller the value the faster the ticker speed
			tickerDirection: 'next',						// 'next', 'prev' - direction in which ticker show will traverse
			tickerHover: false,                 // true, false - if true ticker will pause on mouseover
			wrapperClass: 'bx-wrapper',					// string - classname attached to the slider wraper
			startingSlide: 0, 									// integer - show will start on specified slide. note: slides are zero based!
			displaySlideQty: 5,									// integer - number of slides to display at once
			moveSlideQty: 1,										// integer - number of slides to move at once
			randomStart: false,									// true, false - if true show will start on a random slide
			onBeforeSlide: function(){},				// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onAfterSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onLastSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onFirstSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onNextSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			onPrevSlide: function(){},					// function(currentSlideNumber, totalSlideQty, currentSlideHtmlObject) - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
			buildPager: null										// function(slideIndex, slideHtmlObject){ return string; } - advanced use only! see the tutorial here: http://bxslider.com/custom-pager
		}
		
		var options = $.extend(defaults, options);
		
		// cache the base element
		var base = this;
		// initialize (and localize) all variables
		var $parent = '';
		var $origElement = '';
		var $children = '';
		var $outerWrapper = '';
		var $firstChild = '';
		var childrenWidth = '';
		var childrenOuterWidth = '';
		var wrapperWidth = '';
		var wrapperHeight = '';
		var $pager = '';	
		var interval = '';
		var $autoControls = '';
		var $stopHtml = '';
		var $startContent = '';
		var $stopContent = '';
		var autoPlaying = true;
		var loaded = false;
		var childrenMaxWidth = 0;
		var childrenMaxHeight = 0;
		var currentSlide = 0;	
		var origLeft = 0;
		var origTop = 0;
		var origShowWidth = 0;
		var origShowHeight = 0;
		var tickerLeft = 0;
		var tickerTop = 0;
		var isWorking = false;
    
		var firstSlide = 0;
		var lastSlide = $children.length - 1;
		
						
		// PUBLIC FUNCTIONS
						
		/**
		 * Go to specified slide
		 */		
		this.goToSlide = function(number, stopAuto){
			if(!isWorking){
				isWorking = true;
				// set current slide to argument
				currentSlide = number;
				options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
				// check if stopAuto argument is supplied
				if(typeof(stopAuto) == 'undefined'){
					var stopAuto = true;
				}
				if(stopAuto){
					// if show is auto playing, stop it
					if(options.auto){
						base.stopShow(true);
					}
				}			
				slide = number;
				// check for first slide callback
				if(slide == firstSlide){
					options.onFirstSlide(currentSlide, $children.length, $children.eq(currentSlide));
				}
				// check for last slide callback
				if(slide == lastSlide){
					options.onLastSlide(currentSlide, $children.length, $children.eq(currentSlide));
				}
				// horizontal
				if(options.mode == 'horizontal'){
					$parent.animate({'left': '-'+getSlidePosition(slide, 'left')+'px'}, options.speed, options.easing, function(){
						isWorking = false;
						// perform the callback function
						options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
					});
				// vertical
				}else if(options.mode == 'vertical'){
					$parent.animate({'top': '-'+getSlidePosition(slide, 'top')+'px'}, options.speed, options.easing, function(){
						isWorking = false;
						// perform the callback function
						options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
					});			
				// fade	
				}else if(options.mode == 'fade'){
					setChildrenFade();
				}
				// check to remove controls on last/first slide
				checkEndControls();
				// accomodate multi slides
				if(options.moveSlideQty > 1){
					number = Math.floor(number / options.moveSlideQty);
				}
				// make the current slide active
				makeSlideActive(number);
				// display the caption
				showCaptions();
			}
		}
		
		/**
		 * Go to next slide
		 */		
		this.goToNextSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			if(stopAuto){
				// if show is auto playing, stop it
				if(options.auto){
					base.stopShow(true);
				}
			}			
			// makes slideshow finite
			if(!options.infiniteLoop){
				if(!isWorking){
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide + (options.moveSlideQty));
					// if current slide has looped on itself
					if(currentSlide <= lastSlide){
						checkEndControls();
						// next slide callback
						options.onNextSlide(currentSlide, $children.length, $children.eq(currentSlide));
						// move to appropriate slide
						base.goToSlide(currentSlide);						
					}else{
						currentSlide -= options.moveSlideQty;
					}
				} // end if(!isWorking)		
			}else{ 
				if(!isWorking){
					isWorking = true;					
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide + options.moveSlideQty);
					// if current slide has looped on itself
					if(currentSlide > lastSlide){
						currentSlide = currentSlide % $children.length;
						slideLoop = true;
					}
					// next slide callback
					options.onNextSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// slide before callback
					options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
					if(options.mode == 'horizontal'){						
						// get the new 'left' property for $parent
						var parentLeft = (options.moveSlideQty * childrenOuterWidth);
						// animate to the new 'left'
						$parent.animate({'left': '-='+parentLeft+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('left', '-'+getSlidePosition(currentSlide, 'left')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'vertical'){
						// get the new 'left' property for $parent
						var parentTop = (options.moveSlideQty * childrenMaxHeight);
						// animate to the new 'left'
						$parent.animate({'top': '-='+parentTop+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('top', '-'+getSlidePosition(currentSlide, 'top')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'fade'){
						setChildrenFade();
					}					
					// make the current slide active
					if(options.moveSlideQty > 1){
						makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
					}else{
						makeSlideActive(currentSlide);
					}
					// display the caption
					showCaptions();
				} // end if(!isWorking)
				
			}	
		} // end function
		
		/**
		 * Go to previous slide
		 */		
		this.goToPreviousSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			if(stopAuto){
				// if show is auto playing, stop it
				if(options.auto){
					base.stopShow(true);
				}
			}			
			// makes slideshow finite
			if(!options.infiniteLoop){	
				if(!isWorking){
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = currentSlide - options.moveSlideQty;
					// if current slide has looped on itself
					if(currentSlide < 0){
						currentSlide = 0;
						// if specified, hide the control on the last slide
						if(options.hideControlOnEnd){
							$('.bx-prev', $outerWrapper).hide();
						}
					}
					checkEndControls();
					// next slide callback
					options.onPrevSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// move to appropriate slide
					base.goToSlide(currentSlide);
				}							
			}else{
				if(!isWorking){
					isWorking = true;			
					var slideLoop = false;
					// make current slide the old value plus moveSlideQty
					currentSlide = (currentSlide - (options.moveSlideQty));
					// if current slide has looped on itself
					if(currentSlide < 0){
						negativeOffset = (currentSlide % $children.length);
						if(negativeOffset == 0){
							currentSlide = 0;
						}else{
							currentSlide = ($children.length) + negativeOffset; 
						}
						slideLoop = true;
					}
					// next slide callback
					options.onPrevSlide(currentSlide, $children.length, $children.eq(currentSlide));
					// slide before callback
					options.onBeforeSlide(currentSlide, $children.length, $children.eq(currentSlide));
					if(options.mode == 'horizontal'){
						// get the new 'left' property for $parent
						var parentLeft = (options.moveSlideQty * childrenOuterWidth);
						// animate to the new 'left'
						$parent.animate({'left': '+='+parentLeft+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('left', '-'+getSlidePosition(currentSlide, 'left')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'vertical'){
						// get the new 'left' property for $parent
						var parentTop = (options.moveSlideQty * childrenMaxHeight);
						// animate to the new 'left'
						$parent.animate({'top': '+='+parentTop+'px'}, options.speed, options.easing, function(){
							isWorking = false;
							// if its time to loop, reset the $parent
							if(slideLoop){
								$parent.css('top', '-'+getSlidePosition(currentSlide, 'top')+'px');
							}
							// perform the callback function
							options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
						});
					}else if(options.mode == 'fade'){
						setChildrenFade();
					}					
					// make the current slide active
					if(options.moveSlideQty > 1){
						makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
					}else{
						makeSlideActive(currentSlide);
					}
					// display the caption
					showCaptions();
				} // end if(!isWorking)				
			}
		} // end function
		
		/**
		 * Go to first slide
		 */		
		this.goToFirstSlide = function(stopAuto){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			base.goToSlide(firstSlide, stopAuto);
		}
		
		/**
		 * Go to last slide
		 */		
		this.goToLastSlide = function(){
			// check if stopAuto argument is supplied
			if(typeof(stopAuto) == 'undefined'){
				var stopAuto = true;
			}
			base.goToSlide(lastSlide, stopAuto);
		}
		
		/**
		 * Get the current slide
		 */		
		this.getCurrentSlide = function(){
			return currentSlide;
		}
		
		/**
		 * Get the total slide count
		 */		
		this.getSlideCount = function(){
			return $children.length;
		}
		
		/**
		 * Stop the slideshow
		 */		
		this.stopShow = function(changeText){
			clearInterval(interval);
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			if(changeText && options.autoControls){
				$autoControls.html($startContent).removeClass('stop').addClass('start');
				autoPlaying = false;
			}
		}
		
		/**
		 * Start the slideshow
		 */		
		this.startShow = function(changeText){
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			setAutoInterval();
			if(changeText && options.autoControls){
				$autoControls.html($stopContent).removeClass('start').addClass('stop');
				autoPlaying = true;
			}
		}
		
		/**
		 * Stops the ticker
		 */		
		this.stopTicker = function(changeText){
			$parent.stop();
			// check if changeText argument is supplied
			if(typeof(changeText) == 'undefined'){
				var changeText = true;
			}
			if(changeText && options.ticker){
				$autoControls.html($startContent).removeClass('stop').addClass('start');
				autoPlaying = false;
			}			
		}
		
		/**
		 * Starts the ticker
		 */		
		this.startTicker = function(changeText){
			if(options.mode == 'horizontal'){
				if(options.tickerDirection == 'next'){
					// get the 'left' property where the ticker stopped
					var stoppedLeft = parseInt($parent.css('left'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (origShowWidth + stoppedLeft) + $children.eq(0).width();			
				}else if(options.tickerDirection == 'prev'){
					// get the 'left' property where the ticker stopped
					var stoppedLeft = -parseInt($parent.css('left'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (stoppedLeft) - $children.eq(0).width();
				}
				// calculate the speed ratio to seamlessly finish the loop
				var finishingSpeed = (remainingDistance * options.tickerSpeed) / origShowWidth;
				// call the show
				moveTheShow(tickerLeft, remainingDistance, finishingSpeed);					
			}else if(options.mode == 'vertical'){
				if(options.tickerDirection == 'next'){
					// get the 'top' property where the ticker stopped
					var stoppedTop = parseInt($parent.css('top'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (origShowHeight + stoppedTop) + $children.eq(0).height();			
				}else if(options.tickerDirection == 'prev'){
					// get the 'left' property where the ticker stopped
					var stoppedTop = -parseInt($parent.css('top'));
					// calculate the remaining distance the show must travel until the loop
					var remainingDistance = (stoppedTop) - $children.eq(0).height();
				}
				// calculate the speed ratio to seamlessly finish the loop
				var finishingSpeed = (remainingDistance * options.tickerSpeed) / origShowHeight;
				// call the show
				moveTheShow(tickerTop, remainingDistance, finishingSpeed);
				// check if changeText argument is supplied
				if(typeof(changeText) == 'undefined'){
					var changeText = true;
				}
				if(changeText && options.ticker){
					$autoControls.html($stopContent).removeClass('start').addClass('stop');
					autoPlaying = true;
				}						
			}
		}
				
		/**
		 * Initialize a new slideshow
		 */		
		this.initShow = function(){
			
			// reinitialize all variables
			// base = this;
			$parent = $(this);
			$origElement = $parent.clone();
			$children = $parent.children();
			$outerWrapper = '';
			$firstChild = $parent.children(':first');
			childrenWidth = $firstChild.width();
			childrenMaxWidth = 0;
			childrenOuterWidth = $firstChild.outerWidth();
			childrenMaxHeight = 0;
			wrapperWidth = getWrapperWidth();
			wrapperHeight = getWrapperHeight();
			isWorking = false;
			$pager = '';	
			currentSlide = 0;	
			origLeft = 0;
			origTop = 0;
			interval = '';
			$autoControls = '';
			$stopHtml = '';
			$startContent = '';
			$stopContent = '';
			autoPlaying = true;
			loaded = false;
			origShowWidth = 0;
			origShowHeight = 0;
			tickerLeft = 0;
			tickerTop = 0;
      
			firstSlide = 0;
			lastSlide = $children.length - 1;
						
			// get the largest child's height and width
			$children.each(function(index) {
			  if($(this).outerHeight() > childrenMaxHeight){
					childrenMaxHeight = $(this).outerHeight();
				}
				if($(this).outerWidth() > childrenMaxWidth){
					childrenMaxWidth = $(this).outerWidth();
				}
			});

			// get random slide number
			if(options.randomStart){
				var randomNumber = Math.floor(Math.random() * $children.length);
				currentSlide = randomNumber;
				origLeft = childrenOuterWidth * (options.moveSlideQty + randomNumber);
				origTop = childrenMaxHeight * (options.moveSlideQty + randomNumber);
			// start show at specific slide
			}else{
				currentSlide = options.startingSlide;
				origLeft = childrenOuterWidth * (options.moveSlideQty + options.startingSlide);
				origTop = childrenMaxHeight * (options.moveSlideQty + options.startingSlide);
			}
						
			// set initial css
			initCss();
			
			// check to show pager
			if(options.pager && !options.ticker){
				if(options.pagerType == 'full'){
					showPager('full');
				}else if(options.pagerType == 'short'){
					showPager('short');
				}
			}
						
			// check to show controls
			if(options.controls && !options.ticker){
				setControlsVars();
			}
						
			// check if auto
			if(options.auto || options.ticker){
				// check if auto controls are displayed
				if(options.autoControls){
					setAutoControlsVars();
				}
				// check if show should auto start
				if(options.autoStart){
					// check if autostart should delay
					setTimeout(function(){
						base.startShow(true);
					}, options.autoDelay);
				}else{
					base.stopShow(true);
				}
				// check if show should pause on hover
				if(options.autoHover && !options.ticker){
					setAutoHover();
				}
			}						
			// make the starting slide active
			if(options.moveSlideQty > 1){
				makeSlideActive(Math.ceil(currentSlide / options.moveSlideQty));
			}else{			
				makeSlideActive(currentSlide);			
			}
			// check for finite show and if controls should be hidden
			checkEndControls();
			// show captions
			if(options.captions){
				showCaptions();
			}
			// perform the callback function
			options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
		}
		
		/**
		 * Destroy the current slideshow
		 */		
		this.destroyShow = function(){			
			// stop the auto show
			clearInterval(interval);
			// remove any controls / pagers that have been appended
			$('.bx-next, .bx-prev, .bx-pager, .bx-auto', $outerWrapper).remove();
			// unwrap all bx-wrappers
			$parent.unwrap().unwrap().removeAttr('style');
			// remove any styles that were appended
			$parent.children().removeAttr('style').not('.pager').remove();
			// remove any childrent that were appended
			$children.removeClass('pager');
			
		}
		
		/**
		 * Reload the current slideshow
		 */		
		this.reloadShow = function(){
			base.destroyShow();
			base.initShow();
		}
		
		// PRIVATE FUNCTIONS
		
		/**
		 * Creates all neccessary styling for the slideshow
		 */		
		function initCss(){
			// layout the children
			setChildrenLayout(options.startingSlide);
			// CSS for horizontal mode
			if(options.mode == 'horizontal'){
				// wrap the <ul> in div that acts as a window and make the <ul> uber wide
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+wrapperWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="position:relative; overflow:hidden; width:'+wrapperWidth+'px;"></div>')
				.css({
				  width: '999999px',
				  position: 'relative',
					left: '-'+(origLeft)+'px'
				});
				$parent.children().css({
					width: childrenWidth,
				  'float': 'left',
				  listStyle: 'none'
				});					
				$outerWrapper = $parent.parent().parent();
				$children.addClass('pager');
			// CSS for vertical mode
			}else if(options.mode == 'vertical'){
				// wrap the <ul> in div that acts as a window and make the <ul> uber tall
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="width:'+childrenMaxWidth+'px; height:'+wrapperHeight+'px; position:relative; overflow:hidden;"></div>')
				.css({
				  height: '999999px',
				  position: 'relative',
					top: '-'+(origTop)+'px'
				});
				$parent.children().css({
				  listStyle: 'none',
					height: childrenMaxHeight
				});					
				$outerWrapper = $parent.parent().parent();
				$children.addClass('pager');
			// CSS for fade mode
			}else if(options.mode == 'fade'){
				// wrap the <ul> in div that acts as a window
				$parent
				.wrap('<div class="'+options.wrapperClass+'" style="width:'+childrenMaxWidth+'px; position:relative;"></div>')
				.wrap('<div class="bx-window" style="height:'+childrenMaxHeight+'px; width:'+childrenMaxWidth+'px; position:relative; overflow:hidden;"></div>');
				$parent.children().css({
				  listStyle: 'none',
				  position: 'absolute',
					top: 0,
					left: 0,
					zIndex: 98
				});					
				$outerWrapper = $parent.parent().parent();
				$children.not(':eq('+currentSlide+')').fadeTo(0, 0);
				$children.eq(currentSlide).css('zIndex', 99);
			}
			// if captions = true setup a div placeholder
			if(options.captions && options.captionsSelector == null){
				$outerWrapper.append('<div class="bx-captions"></div>');
			}			
		}
		
		/**
		 * Depending on mode, lays out children in the proper setup
		 */		
		function setChildrenLayout(){			
			// lays out children for horizontal or vertical modes
			if(options.mode == 'horizontal' || options.mode == 'vertical'){
								
				// get the children behind
				var $prependedChildren = getArraySample($children, 0, options.moveSlideQty, 'backward');
				
				// add each prepended child to the back of the original element
				$.each($prependedChildren, function(index) {
					$parent.prepend($(this));
				});			
				
				// total number of slides to be hidden after the window
				var totalNumberAfterWindow = ($children.length + options.moveSlideQty) - 1;
				// number of original slides hidden after the window
				var pagerExcess = $children.length - options.displaySlideQty;
				// number of slides to append to the original hidden slides
				var numberToAppend = totalNumberAfterWindow - pagerExcess;
				// get the sample of extra slides to append
				var $appendedChildren = getArraySample($children, 0, numberToAppend, 'forward');
				
				if(options.infiniteLoop){
					// add each appended child to the front of the original element
					$.each($appendedChildren, function(index) {
						$parent.append($(this));
					});
				}
			}
		}
		
		/**
		 * Sets all variables associated with the controls
		 */		
		function setControlsVars(){
			// check if text or images should be used for controls
			// check "next"
			if(options.nextImage != ''){
				nextContent = options.nextImage;
				nextType = 'image';
			}else{
				nextContent = options.nextText;
				nextType = 'text';
			}
			// check "prev"
			if(options.prevImage != ''){
				prevContent = options.prevImage;
				prevType = 'image';
			}else{
				prevContent = options.prevText;
				prevType = 'text';
			}
			// show the controls
			showControls(nextType, nextContent, prevType, prevContent);
		}			
		
		/**
		 * Puts slideshow into auto mode
		 *
		 * @param int pause number of ms the slideshow will wait between slides 
		 * @param string direction 'forward', 'backward' sets the direction of the slideshow (forward/backward)
		 * @param bool controls determines if start/stop controls will be displayed
		 */		
		function setAutoInterval(){
			if(options.auto){
				// finite loop
				if(!options.infiniteLoop){
					if(options.autoDirection == 'next'){
						interval = setInterval(function(){
							currentSlide += options.moveSlideQty;
							// if currentSlide has exceeded total number
							if(currentSlide > lastSlide){
								currentSlide = currentSlide % $children.length;
							}
							base.goToSlide(currentSlide, false);
						}, options.pause);
					}else if(options.autoDirection == 'prev'){
						interval = setInterval(function(){
							currentSlide -= options.moveSlideQty;
							// if currentSlide is smaller than zero
							if(currentSlide < 0){
								negativeOffset = (currentSlide % $children.length);
								if(negativeOffset == 0){
									currentSlide = 0;
								}else{
									currentSlide = ($children.length) + negativeOffset; 
								}
							}
							base.goToSlide(currentSlide, false);
						}, options.pause);
					}
				// infinite loop
				}else{
					if(options.autoDirection == 'next'){
						interval = setInterval(function(){
							base.goToNextSlide(false);
						}, options.pause);
					}else if(options.autoDirection == 'prev'){
						interval = setInterval(function(){
							base.goToPreviousSlide(false);
						}, options.pause);
					}
				}
			
			}else if(options.ticker){
				
				options.tickerSpeed *= 10;
												
				// get the total width of the original show
				$('.pager', $outerWrapper).each(function(index) {
				  origShowWidth += $(this).width();
					origShowHeight += $(this).height();
				});
				
				// if prev start the show from the last slide
				if(options.tickerDirection == 'prev' && options.mode == 'horizontal'){
					$parent.css('left', '-'+(origShowWidth+origLeft)+'px');
				}else if(options.tickerDirection == 'prev' && options.mode == 'vertical'){
					$parent.css('top', '-'+(origShowHeight+origTop)+'px');
				}
				
				if(options.mode == 'horizontal'){
					// get the starting left position
					tickerLeft = parseInt($parent.css('left'));
					// start the ticker
					moveTheShow(tickerLeft, origShowWidth, options.tickerSpeed);
				}else if(options.mode == 'vertical'){
					// get the starting top position
					tickerTop = parseInt($parent.css('top'));
					// start the ticker
					moveTheShow(tickerTop, origShowHeight, options.tickerSpeed);
				}												
				
				// check it tickerHover applies
				if(options.tickerHover){
					setTickerHover();
				}					
			}			
		}
		
		function moveTheShow(leftCss, distance, speed){
			// if horizontal
			if(options.mode == 'horizontal'){
				// if next
				if(options.tickerDirection == 'next'){
					$parent.animate({'left': '-='+distance+'px'}, speed, 'linear', function(){
						$parent.css('left', leftCss);
						moveTheShow(leftCss, origShowWidth, options.tickerSpeed);
					});
				// if prev
				}else if(options.tickerDirection == 'prev'){
					$parent.animate({'left': '+='+distance+'px'}, speed, 'linear', function(){
						$parent.css('left', leftCss);
						moveTheShow(leftCss, origShowWidth, options.tickerSpeed);
					});
				}
			// if vertical		
			}else if(options.mode == 'vertical'){
				// if next
				if(options.tickerDirection == 'next'){
					$parent.animate({'top': '-='+distance+'px'}, speed, 'linear', function(){
						$parent.css('top', leftCss);
						moveTheShow(leftCss, origShowHeight, options.tickerSpeed);
					});
				// if prev
				}else if(options.tickerDirection == 'prev'){
					$parent.animate({'top': '+='+distance+'px'}, speed, 'linear', function(){
						$parent.css('top', leftCss);
						moveTheShow(leftCss, origShowHeight, options.tickerSpeed);
					});
				}
			}
		}		
		
		/**
		 * Sets all variables associated with the controls
		 */		
		function setAutoControlsVars(){
			// check if text or images should be used for controls
			// check "start"
			if(options.startImage != ''){
				startContent = options.startImage;
				startType = 'image';
			}else{
				startContent = options.startText;
				startType = 'text';
			}
			// check "stop"
			if(options.stopImage != ''){
				stopContent = options.stopImage;
				stopType = 'image';
			}else{
				stopContent = options.stopText;
				stopType = 'text';
			}
			// show the controls
			showAutoControls(startType, startContent, stopType, stopContent);
		}
		
		/**
		 * Handles hover events for auto shows
		 */		
		function setAutoHover(){
			// hover over the slider window
			$outerWrapper.find('.bx-window').hover(function() {
				if(autoPlaying){
					base.stopShow(false);
				}
			}, function() {
				if(autoPlaying){
					base.startShow(false);
				}
			});
		}
		
		/**
		 * Handles hover events for ticker mode
		 */		
		function setTickerHover(){
			// on hover stop the animation
			$parent.hover(function() {
				if(autoPlaying){
					base.stopTicker(false);
				}
			}, function() {
				if(autoPlaying){
					base.startTicker(false);
				}
			});
		}		
		
		/**
		 * Handles fade animation
		 */		
		function setChildrenFade(){
			// fade out any other child besides the current
			$children.not(':eq('+currentSlide+')').fadeTo(options.speed, 0).css('zIndex', 98);
			// fade in the current slide
			$children.eq(currentSlide).css('zIndex', 99).fadeTo(options.speed, 1, function(){
				isWorking = false;
				// ie fade fix
				if(jQuery.browser.msie){
					$children.eq(currentSlide).get(0).style.removeAttribute('filter');
				}
				// perform the callback function
				options.onAfterSlide(currentSlide, $children.length, $children.eq(currentSlide));
			});
		};
				
		/**
		 * Makes slide active
		 */		
		function makeSlideActive(number){
			if(options.pagerType == 'full' && options.pager){
				// remove all active classes
				$('a', $pager).removeClass(options.pagerActiveClass);
				// assign active class to appropriate slide
				$('a', $pager).eq(number).addClass(options.pagerActiveClass);
			}else if(options.pagerType == 'short' && options.pager){
				$('.bx-pager-current', $pager).html(currentSlide+1);
			}
		}
				
		/**
		 * Displays next/prev controls
		 *
		 * @param string nextType 'image', 'text'
		 * @param string nextContent if type='image', specify a filepath to the image. if type='text', specify text.
		 * @param string prevType 'image', 'text'
		 * @param string prevContent if type='image', specify a filepath to the image. if type='text', specify text.
		 */		
		function showControls(nextType, nextContent, prevType, prevContent){
			// create pager html elements
			var $nextHtml = $('<a href="" class="bx-next"></a>');
			var $prevHtml = $('<a href="" class="bx-prev"></a>');
			// check if next is 'text' or 'image'
			if(nextType == 'text'){
				$nextHtml.html(nextContent);
			}else{
				$nextHtml.html('<img src="'+nextContent+'" />');
			}
			// check if prev is 'text' or 'image'
			if(prevType == 'text'){
				$prevHtml.html(prevContent);
			}else{
				$prevHtml.html('<img src="'+prevContent+'" />');
			}
			// check if user supplied a selector to populate next control
			if(options.prevSelector){
				$(options.prevSelector).append($prevHtml);
			}else{
				$outerWrapper.append($prevHtml);
			}
			// check if user supplied a selector to populate next control
			if(options.nextSelector){
				$(options.nextSelector).append($nextHtml);
			}else{
				$outerWrapper.append($nextHtml);
			}
			// click next control
			$nextHtml.click(function() {
				base.goToNextSlide();
				return false;
			});
			// click prev control
			$prevHtml.click(function() {
				base.goToPreviousSlide();
				return false;
			});
		}
		
		/**
		 * Displays the pager
		 *
		 * @param string type 'full', 'short'
		 */		
		function showPager(type){
			// sets up logic for finite multi slide shows
			var pagerQty = $children.length;
			// if we are moving more than one at a time and we have a finite loop
			if(options.moveSlideQty > 1){
				// if slides create an odd number of pages
				if($children.length % options.moveSlideQty != 0){
					// pagerQty = $children.length / options.moveSlideQty + 1;
					pagerQty = Math.ceil($children.length / options.moveSlideQty);
				// if slides create an even number of pages
				}else{
					pagerQty = $children.length / options.moveSlideQty;
				}
			}
			var pagerString = '';
			// check if custom build function was supplied
			if(options.buildPager){
				for(var i=0; i<pagerQty; i++){
					pagerString += options.buildPager(i, $children.eq(i * options.moveSlideQty));
				}
				
			// if not, use default pager
			}else if(type == 'full'){
				// build the full pager
				for(var i=1; i<=pagerQty; i++){
					pagerString += '<a href="" class="pager-link pager-'+i+'">'+i+'</a>';
				}
			}else if(type == 'short') {
				// build the short pager
				pagerString = '<span class="bx-pager-current">'+(options.startingSlide+1)+'</span> '+options.pagerShortSeparator+' <span class="bx-pager-total">'+$children.length+'</span>';
			}	
			// check if user supplied a pager selector
			if(options.pagerSelector){
				$(options.pagerSelector).append(pagerString);
				$pager = $(options.pagerSelector);
			}else{
				var $pagerContainer = $('<div class="bx-pager"></div>');
				$pagerContainer.append(pagerString);
				// attach the pager to the DOM
				if(options.pagerLocation == 'top'){
					$outerWrapper.prepend($pagerContainer);
				}else if(options.pagerLocation == 'bottom'){
					$outerWrapper.append($pagerContainer);
				}
				// cache the pager element
				$pager = $('.bx-pager', $outerWrapper);
			}
			$pager.children().click(function() {
				// only if pager is full mode
				if(options.pagerType == 'full'){
					// get the index from the link
					var slideIndex = $pager.children().index(this);
					// accomodate moving more than one slide
					if(options.moveSlideQty > 1){
						slideIndex *= options.moveSlideQty;
					}
					base.goToSlide(slideIndex);
				}
				return false;
			});
		}
				
		/**
		 * Displays captions
		 */		
		function showCaptions(){
			// get the title from each image
		  var caption = $('img', $children.eq(currentSlide)).attr('title');
			// if the caption exists
			if(caption != ''){
				// if user supplied a selector
				if(options.captionsSelector){
					$(options.captionsSelector).html(caption);
				}else{
					$('.bx-captions', $outerWrapper).html(caption);
				}
			}else{
				// if user supplied a selector
				if(options.captionsSelector){
					$(options.captionsSelector).html('&nbsp;');
				}else{
					$('.bx-captions', $outerWrapper).html('&nbsp;');
				}				
			}
		}
		
		/**
		 * Displays start/stop controls for auto and ticker mode
		 *
		 * @param string type 'image', 'text'
		 * @param string next [optional] if type='image', specify a filepath to the image. if type='text', specify text.
		 * @param string prev [optional] if type='image', specify a filepath to the image. if type='text', specify text.
		 */
		function showAutoControls(startType, startContent, stopType, stopContent){
			// create pager html elements
			$autoControls = $('<a href="" class="bx-start"></a>');
			// check if start is 'text' or 'image'
			if(startType == 'text'){
				$startContent = startContent;
			}else{
				$startContent = '<img src="'+startContent+'" />';
			}
			// check if stop is 'text' or 'image'
			if(stopType == 'text'){
				$stopContent = stopContent;
			}else{
				$stopContent = '<img src="'+stopContent+'" />';
			}
			// check if user supplied a selector to populate next control
			if(options.autoControlsSelector){
				$(options.autoControlsSelector).append($autoControls);
			}else{
				$outerWrapper.append('<div class="bx-auto"></div>');
				$('.bx-auto', $outerWrapper).html($autoControls);
			}
						
			// click start control
			$autoControls.click(function() {
				if(options.ticker){
					if($(this).hasClass('stop')){
						base.stopTicker();
					}else if($(this).hasClass('start')){
						base.startTicker();
					}
				}else{
					if($(this).hasClass('stop')){
						base.stopShow(true);
					}else if($(this).hasClass('start')){
						base.startShow(true);
					}
				}
				return false;
			});
			
		}
		
		/**
		 * Checks if show is in finite mode, and if slide is either first or last, then hides the respective control
		 */		
		function checkEndControls(){
			if(!options.infiniteLoop && options.hideControlOnEnd){
				// check previous
				if(currentSlide == firstSlide){
					$('.bx-prev', $outerWrapper).hide();				
				}else{
					$('.bx-prev', $outerWrapper).show();
				}
				// check next
				if(currentSlide == lastSlide){
					$('.bx-next', $outerWrapper).hide();
				}else{
					$('.bx-next', $outerWrapper).show();
				}
			}
		}
		
		/**
		 * Returns the left offset of the slide from the parent container
		 */		
		function getSlidePosition(number, side){			
			if(side == 'left'){
				var position = $('.pager', $outerWrapper).eq(number).position().left;
			}else if(side == 'top'){
				var position = $('.pager', $outerWrapper).eq(number).position().top;
			}
			return position;
		}
		
		/**
		 * Returns the width of the wrapper
		 */		
		function getWrapperWidth(){
			var wrapperWidth = $firstChild.outerWidth() * options.displaySlideQty;
			return wrapperWidth;
		}
		
		/**
		 * Returns the height of the wrapper
		 */		
		function getWrapperHeight(){
			// if displaying multiple slides, multiple wrapper width by number of slides to display
			var wrapperHeight = $firstChild.outerHeight() * options.displaySlideQty;
			return wrapperHeight;
		}
		
		/**
		 * Returns a sample of an arry and loops back on itself if the end of the array is reached
		 *
		 * @param array array original array the sample is derived from
		 * @param int start array index sample will start
		 * @param int length number of items in the sample
		 * @param string direction 'forward', 'backward' direction the loop should travel in the array
		 */		
		function getArraySample(array, start, length, direction){
			// initialize empty array
			var sample = [];
			// clone the length argument
			var loopLength = length;
			// determines when the empty array should start being populated
			var startPopulatingArray = false;
			// reverse the array if direction = 'backward'
			if(direction == 'backward'){
				array = $.makeArray(array);
				array.reverse();
			}
			// loop through original array until the length argument is met
			while(loopLength > 0){				
				// loop through original array
				$.each(array, function(index, val) {
					// check if length has been met
					if(loopLength > 0){
						// don't do anything unless first index has been reached
					  if(!startPopulatingArray){
							// start populating empty array
							if(index == start){
								startPopulatingArray = true;
								// add element to array
								sample.push($(this).clone());
								// decrease the length clone variable
								loopLength--;
							}
						}else{
							// add element to array
							sample.push($(this).clone());
							// decrease the length clone variable
							loopLength--;
						}
					// if length has been met, break loose
					}else{
						return false;
					}			
				});				
			}
			return sample;
		}
												
		this.each(function(){
			// make sure the element has children
			if($(this).children().length > 0){
				base.initShow();
			}
		});
				
		return this;						
	}
	
	jQuery.fx.prototype.cur = function(){
		if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
			return this.elem[ this.prop ];
		}

		var r = parseFloat( jQuery.css( this.elem, this.prop ) );
		// return r && r > -10000 ? r : 0;
		return r;
	}
})(jQuery);
/*
 * END: jquery.bxSlider.js
 */

/*
 * START: jquery.msAccordion.js
 */
//menu Accordion
//author: Marghoob Suleman
//Date: 05th Aug, 2009
//Version: 1.0
//web: www.giftlelo.com | www.marghoobsuleman.com
;(function($){
	$.fn.msAccordion = function(options) {
		options = $.extend({
					currentDiv:'1',
					previousDiv:'',
					vertical: false,
					defaultid:0,
					currentcounter:0,
					intervalid:0,
					autodelay:0,
					event:"click",
					alldivs_array:new Array()
			}, options);
		$(this).addClass("accordionWrapper");
		$(this).css({overflow:"hidden"});
		//alert(this);
		var elementid = $(this).attr("id");
		var allDivs = this.children();
		if(options.autodelay>0)  {
			$("#"+ elementid +" > div").bind("mouseenter", function(){
														   pause();
														   });
			$("#"+ elementid +" > div").bind("mouseleave", function(){
																  startPlay();
																  });
		}
		//set ids
		allDivs.each(function(current) {
								 var iCurrent = current;
								 var sTitleID = elementid+"_msTitle_"+(iCurrent);
								 var sContentID = sTitleID+"_msContent_"+(iCurrent);
								 var currentDiv = allDivs[iCurrent];
								 var totalChild = currentDiv.childNodes.length;
								 var titleDiv = $(currentDiv).find("div.title");
								 titleDiv.attr("id", sTitleID);
								 var contentDiv = $(currentDiv).find("div.content");
								 contentDiv.attr("id", sContentID);
								 options.alldivs_array.push(sTitleID);
								 //$("#"+sTitleID).click(function(){openMe(sTitleID);});
								 $("#"+sTitleID).bind(options.event, function(){pause();openMe(sTitleID);});
								 });
		
		//make vertical
		if(options.vertical) {makeVertical();};
		//open default
		openMe(elementid+"_msTitle_"+options.defaultid);
		if(options.autodelay>0) {startPlay();};
		//alert(allDivs.length);
		function openMe(id) {
			var sTitleID = id;
			var iCurrent = sTitleID.split("_")[sTitleID.split("_").length-1];
			options.currentcounter = iCurrent;
			var sContentID = id+"_msContent_"+iCurrent;
			if($("#"+sContentID).css("display")=="none") {
				if(options.previousDiv!="") {
					closeMe(options.previousDiv);
				};
				if(options.vertical) {
					$("#"+sContentID).slideDown("slow");
				} else {
					$("#"+sContentID).show("slow");
				}
				options.currentDiv = sContentID;
				options.previousDiv = options.currentDiv;
			};
		};
		function closeMe(div) {
			if(options.vertical) {
				$("#"+div).slideUp("slow");
			} else {
				$("#"+div).hide("slow");
			};
		};	
		function makeVertical() {
			$("#"+elementid +" > div").css({display:"block", float:"none", clear:"both"});
			$("#"+elementid +" > div > div.title").css({display:"block", float:"none", clear:"both"});
			$("#"+elementid +" > div > div.content").css({clear:"both"});
		};
		function startPlay() {
			options.intervalid = window.setInterval(play, options.autodelay*1000);
		};
		function play() {
			var sTitleId = options.alldivs_array[options.currentcounter];
			openMe(sTitleId);
			options.currentcounter++;
			if(options.currentcounter==options.alldivs_array.length) options.currentcounter = 0;
		};
		function pause() {
			window.clearInterval(options.intervalid);
		};
		}
})(jQuery);
/*
 * END: jquery.msAccordion.js
 */

/*
 * START: jquery.nailthumb.1.1.js
 */
/*	jQuery NailThumb Plugin - any image to any thumbnail
 *  Examples and documentation at: http://www.garralab.com/nailthumb.php
 *  Copyright (C) 2012  garralab@gmail.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function($) {
    var DEBUG = false;
    var version = '1.1';
    $.fn.nailthumb = function(options) {
        var opts = $.extend({}, $.fn.nailthumb.defaults, options);
        return this.each(function() {
            var $this = $(this);
            var o = $.metadata ? $.extend({}, opts, $this.metadata()) : opts;
            thumbize($this,o)
        });
    };
    function thumbize(element,options) {
        var image = setImage(element,options);
        var container = setContainer(element,options);
        if (options.serverSideParams) {
            $.fn.nailthumb.setServerSideParams(image,container,options);
        }
        debugObject('image',image);
        debugObject('container',container);
        if (options.onStart) options.onStart(container,options);
        if (options.loadingClass) container.addClass(options.loadingClass);

        if (options.preload || image.data('nailthumb.replaceto')) {
            debug('wait on load');
            image.one('load',function() {
                debugObject('before check',image);
                if(!image.data('nailthumb.working') && !image.data('nailthumb.replacing')) {
                    image.data('nailthumb.working',true);
                    debugObject('inside check',image);
                    doThumb(image,container,options);
                }
            });
            var src = image.attr('src');
            image.attr('src',null).attr('src',src);
        } else {
            debug('nail thumb directly');
            image.data('nailthumb.working',true);
            doThumb(image,container,options);
        }
    };
    function doThumb(image,container,options) {
        resetImage(image,options);
        resetContainer(container,options);
        var imageDims = getImageDims(image,options);
        debugObject('image',image);
        debugObject('imageDims',imageDims);
        if (imageDims.width==0 || imageDims.height==0) {
            imageDims = getHiddenCloneDims(image);
            debugObject('imageCloneDims',imageDims);
        }
        var containerDims = getContainerDims(container,options);
        debugObject('container',container);
        debugObject('containerDims',containerDims);
        var prop = getProportion(containerDims,imageDims,options);
        debug('proportions',prop);
        resize(image, imageDims, container, containerDims, prop, options);
        
    };
    function setImage(element,options) {
        var image = element.find('img').first();
        var finder = options.imageCustomFinder;
        if (!finder && options.imageUrl) {
            finder = imageUrlFinder;
        } else if(!finder && options.imageFromWrappingLink) {
            finder = imageFromWrappingLinkFinder;
        }
        if (finder) {
            var img = finder(element,options);
            debugObject('finder',img);
            if (!img) img = [];
            if (img.length>0) {
                image = img;
                image.css('display','none');
                if(!image.data('nailthumb.replaceto')) image.data('nailthumb.replaceto',element);
                image.data('nailthumb.originalImageDims',null);
            }
        }
        if (image.length==0) {
            if (element.is('img')) image = element;
        }
        
        return image;
    };
    function imageUrlFinder(element,options) {
        var image = $('<img />').attr('src',options.imageUrl).css('display','none').data('nailthumb.replaceto',element);
        element.append(image);
        return image;
    };
    function imageFromWrappingLinkFinder(element,options) {
        var image;
        var link = element.find('a').first();
        if (link.length==0 && element.is('a')) {
            link = element;
        }
        if (link.attr('href')) {
            image = $('<img />').attr('src',link.attr('href')).css('display','none').data('nailthumb.replaceto',link);
            if (link.attr('title')) image.attr('title',link.attr('title'));
            link.append(image);
        }
        return image;
    };
    function resetImage(image,options) {
        if (!options.nostyle) {
            image.css({
                'position':'relative'
            });
        }
        if (!image.data('nailthumb.originalImageDims')) {
            image.css({
                'width':'auto',
                'height':'auto',
                'top':0,
                'left':0
            }).removeAttr('width')
            .removeAttr('height');
        }
    };
    function setContainer(element,options) {
        var container = element;
        if (element.is('img')) {
            if (options.ifImageAddContainer) {
                var c = $('<div></div>');
                element.wrap(c);
            } 
            container = element.parent();
        }
        return container;
    };
    function resetContainer(container,options) {
        if (options.containerClass) container.addClass(options.containerClass);
        if (!options.nostyle) {
            container.css({
                'overflow':'hidden',
                'padding':'0px'
            });
        }
        
        if (options.replaceAnimation == 'animate') {
            if (options.width || options.height) {
                container.animate({
                    'width':options.width,
                    'height':options.height
                },options.animationTime,options.animation);
            }
        } else {
            if (options.width) container.width(options.width);
            if (options.height) container.height(options.height);
        }
        
        container.find('span.'+options.titleClass).remove();
    };
    function resize(image, imageDims, container, containerDims, prop, options) {
        var iw = imageDims.width * prop;
        var ih = imageDims.height * prop;
        var top = 0, left = 0, diff;
        var direction = getDirections(options.fitDirection);
        if (ih<containerDims.innerHeight) {
            switch (direction.v) {
                case 'center':
                    top=-(ih-containerDims.innerHeight)/2;
                    break;
                case 'bottom':
                    top=-(ih-containerDims.innerHeight);
                    diff='bottom';
                    break;
                case 'top':
                    top=0;
                    diff='top';
                    break;
                default:
                    break;
            }
        } else if (ih>containerDims.innerHeight) {
            switch (direction.v) {
                case 'center':
                    top=-(ih-containerDims.innerHeight)/2;
                    break;
                case 'bottom':
                    top=-(ih-containerDims.innerHeight);
                    break;
                default:
                    break;
            }
        }
        if (iw<containerDims.innerWidth) {
            switch (direction.h) {
                case 'center':
                    left=-(iw-containerDims.innerWidth)/2;
                    break;
                case 'right':
                    left=-(iw-containerDims.innerWidth);
                    break;
                default:
                    break;
            }
        } else if (iw>containerDims.innerWidth) {
            switch (direction.h) {
                case 'center':
                    left=-(iw-containerDims.innerWidth)/2;
                    break;
                case 'right':
                    left=-(iw-containerDims.innerWidth);
                    break;
                default:
                    break;
            }
        }
        image.addClass(options.imageClass);
        if (image.data('nailthumb.replaceto')) {
            replaceImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
        } else {
            showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
        }
    };
    function replaceImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        var element = image.data('nailthumb.replaceto');
        var replaceto = findReplaceTo(element,options);
        image.data('nailthumb.replacing',true);
        image.load(function() {
            image.data('nailthumb.replacing',null);
        });
        if (replaceto) {
            replaceto.replaceWith(image);
        } else {
            element.append(image);
        }
        if (options.afterReplace) options.afterReplace(container, image, options);
        showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
    };
    function showImage(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        
        if (options.replaceAnimation == 'animate') {
            image.css('display','inline');
            container.animate({
                'width':containerDims.innerWidth,
                'height':containerDims.innerHeight
            },options.animationTime,options.animation);
            image.animate({
                'width':iw,
                'height':ih,
                'top':top,
                'left':left
            },options.animationTime,options.animation,function(){
                afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
            });
        } else {
            container.css({
                'width':containerDims.innerWidth,
                'height':containerDims.innerHeight
            });
            if (options.replaceAnimation) image.css('display','none');
            image.css({
                'width':iw,
                'height':ih,
                'top':top,
                'left':left
            });
            if (options.replaceAnimation == 'fade') {
                image.fadeIn(options.animationTime,options.animation,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                });
            } else if (options.replaceAnimation == 'slide') {
                image.slideDown(options.animationTime,options.animation,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                });
            } else if (options.replaceAnimation && options.replaceAnimation instanceof Function) {
                options.replaceAnimation(image,function(){
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                },options);
                if (!options.selfStartAfterAppear) {
                    afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
                }
            } else {
                image.css('display','inline');
                afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
            }
        }
    };
    function afterAppear(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        if (options.afterAppear) options.afterAppear(container, image, options);
        image.data('nailthumb.replaceto',null);
        decorate(image, imageDims, container, containerDims, ih, iw, left, top, diff, options);
    };
    function findReplaceTo(element,options) {
        var rep = null; 
        element.find('img').each(function() {
            if (!rep && !$(this).data('nailthumb.replaceto')) {
                rep = $(this);
            }
        });
        return rep;
    };
    function decorate(image, imageDims, container, containerDims, ih, iw, left, top, diff, options) {
        if (options.title || (options.titleAttr && image.attr(options.titleAttr)) ) {
            var title = options.title?options.title:image.attr(options.titleAttr);
            if (title) {
                var span = $('<span class="'+options.titleClass+'">'+title+'</span>');
                if (containerDims.innerHeight>ih) span.css('top',containerDims.innerHeight-ih);
                else span.css('top','0px');
                container.append(span);
                var tit = getHiddenDims(span);
                var im = getHiddenDims(image);
                debugObject('decorate containerDims',containerDims);
                debugObject('decorate imageDims',imageDims);
                debugObject('decorate imageDims',im);
                debugObject('decorate tit',tit);
                var outbound = containerDims.offsetTop+containerDims.innerHeight-tit.offsetTop;
                if (containerDims.height>containerDims.innerHeight) {
                    outbound+=(containerDims.height-containerDims.innerHeight)/2
                }
                span.css('top','+='+outbound);
                
                if (iw < tit.width) span.css('width',iw);
                if (left > 0) span.css('left',left);
                
                var delta = tit.height;
                if (containerDims.innerHeight>ih && diff!='bottom') {
                    delta += (containerDims.innerHeight-ih)/((diff=='top')?1:2);
                }
                
                var clone = span.clone();
                clone.css('width','auto').css('display','none').css('position','absolute');
                container.append(clone);
                var cloneDims = getHiddenDims(clone);
                clone.remove();
                debugObject('decorate cloneDims',cloneDims);
                
                if (options.titleWhen=='hover') {
                    container.unbind('mouseenter mouseleave').hover(function(){
                        span.find('span.'+options.titleScrollerClass).css('left',0);
                        containerDims = getHiddenDims(container);
                        tit = getHiddenDims(span);
                        outbound = containerDims.offsetTop+containerDims.innerHeight-tit.offsetTop;
                        if (containerDims.height>containerDims.innerHeight) {
                            outbound+=(containerDims.height-containerDims.innerHeight)/2
                        }
                        debugObject('decorate hover tit',tit);
                        debug('decorate hover outbound',tit);
                        var doubleDelta = 0;
                        if (outbound<0) {
                            span.css('top','+='+outbound);
                            doubleDelta = delta;
                        } else {
                            doubleDelta = delta-outbound;
                        }
                        if(options.animateTitle) {
                            resetScrollTitle(span,options);
                            span.stop(true).animate({
                                top:'-='+doubleDelta
                            },options.titleAnimationTime,options.titleAnimation,function(){
                                scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                            });
                        } else {
                            span.css({
                                top:'-='+doubleDelta
                            });
                            scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                        }
                    },function(){
                        if(options.animateTitle) {
                            resetScrollTitle(span,options);
                            span.animate({
                                top:'+='+delta
                            },options.titleAnimationTime,options.titleAnimation,function(){
                                resetScrollTitle(span,options);
                            });
                        } else {
                            resetScrollTitle(span,options);
                            span.css({
                                top:'+='+delta
                            });
                        }
                    });
                } else {
                    if(options.animateTitle) {
                        span.animate({
                            top:'-='+delta
                        },options.titleAnimationTime,options.titleAnimation,function(){
                            scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                        });
                    } else {
                        span.css({
                            top:'-='+delta
                        });
                        scrollTitle(span, cloneDims.width, containerDims.innerWidth, options);
                    }
                }
            }
        }
        if (options.onFinish) options.onFinish(container,options);
        if (options.loadingClass) container.removeClass(options.loadingClass);
        image.data('nailthumb.working',null);
    };
    function resetScrollTitle(span,options) {
        span.find('span.'+options.titleScrollerClass).stop();
    };
    function scrollTitle(span, width, visibleWidth, options) {
        if (width > visibleWidth && options.titleScrolling) {
            if (span.find('span.'+options.titleScrollerClass).length==0) {
                span.wrapInner('<span class="'+options.titleScrollerClass+'" />');
                span.find('span.'+options.titleScrollerClass).width(width).css('position','relative').css('white-space','nowrap');
            }
            span.find('span.'+options.titleScrollerClass).css('left',0);
            setTimeout(scrollFunction(span, width, visibleWidth, options),1000);
        }
    };
    function scrollFunction(span, width, visibleWidth, options) {
        return function() {
            var indent = Number(span.find('span.'+options.titleScrollerClass).css('left').replace(/[^-\d]/g,''));
            debug('indent',indent);
            debug('width',width);
            debug('visibleWidth',visibleWidth);
            debug('width <= -indent',(width <= -indent));
            var delta = width + indent;
            if (delta <= 0) {
                span.find('span.'+options.titleScrollerClass).css('left',visibleWidth);
                delta = width + visibleWidth;
            }
            delta += 10;
            span.find('span.'+options.titleScrollerClass).animate({
                'left':'-='+delta
            },width*1000/30,'linear',scrollFunction(span, width, visibleWidth, options));
        };
    };
    function getProportion(containerDims, imageDims, options) {
        if (options.proportions != null && options.proportions > 0) {
            return options.proportions;
        } else {
            var prop = containerDims.innerWidth/imageDims.width;
            if (options.method && options.method=='resize') {
                if (containerDims.innerHeight/imageDims.height < prop) {
                    prop = containerDims.innerHeight/imageDims.height;
                }
            } else {
                if (containerDims.innerHeight/imageDims.height > prop) {
                    prop = containerDims.innerHeight/imageDims.height;
                }
            }
            if (options.maxEnlargement && options.maxEnlargement < prop) prop = options.maxEnlargement;
            if (options.maxShrink && options.maxShrink > prop) prop = options.maxShrink;
            return prop;
        }
    };
    function getDirections(option) {
        var dir = {
            h:'center',
            v:'center'
        };
        if (option) {
            var opts = option.split(' ');
            if (opts.length > 0) {
                dir = getDirection(opts[0],dir);
            }
            if (opts.length > 1) {
                dir = getDirection(opts[1],dir);
            }
        }
        return dir;
    };
    function getDirection(str,d) {
        switch (str) {
            case 'top':
                d.v = 'top';
                break;
            case 'bottom':
                d.v = 'bottom';
                break;
            case 'left':
                d.h = 'left';
                break;
            case 'right':
                d.h = 'right';
                break;
            default:
                break;
        }
        return d;
    };
    function getImageDims(image,options) {
        var imageDims;
        if (!image.data('nailthumb.originalImageDims') ) {
            imageDims = getHiddenDims(image);
            image.data('nailthumb.originalImageDims',imageDims);
            if (!options.keepImageDimensions) {
                image.one('load',function(){
                    image.data('nailthumb.originalImageDims',null);
                });
            }
        } else {
            imageDims = image.data('nailthumb.originalImageDims');
        }
        return imageDims;
    };
    function getContainerDims(container,options) {
        var containerDims = getHiddenDims(container)
        if (options.width) containerDims.innerWidth = options.width;
        if (options.height) containerDims.innerHeight = options.height;
        return containerDims;
    };
    function getDims(elem) {
        var offset = $(elem).offset();
        return {
            offsetTop: offset.top,
            offsetLeft: offset.left,
            width: $(elem).outerWidth(),
            height: $(elem).outerHeight(),
            innerWidth: $(elem).innerWidth(),
            innerHeight: $(elem).innerHeight()
        };
    };
    function getHiddenDims(elems) {
        var dims = null, i = 0, offset, elem;

        while ((elem = elems[i++])) {
            var hiddenElems = $(elem).parents().andSelf().filter(':hidden');
            if ( ! hiddenElems.length ) {
                dims = getDims(elem);
            } else {
                var backupStyle = [];
                hiddenElems.each( function() {
                    var style = $(this).attr('style');
                    style = typeof style == 'undefined'? '': style;
                    backupStyle.push( style );
                    $(this).attr( 'style', style + ' display: block !important;' );
                });

                hiddenElems.eq(0).css( 'left', -10000 );

                dims = getDims(elem);

                hiddenElems.each( function() {
                    $(this).attr( 'style', backupStyle.shift() );
                });
            }
            
        }

        return dims;
    };
    function getHiddenCloneDims(elems) {
        var dims = null, i = 0, offset, elem;

        while ((elem = elems[i++])) {
            var hiddenElems = $(elem).parents().andSelf().filter(':hidden');
            if ( ! hiddenElems.length ) {
                dims = getDims(elem);
            } else {
                var backupStyle = [];
                hiddenElems.each( function() {
                    var style = $(this).attr('style');
                    style = typeof style == 'undefined'? '': style;
                    backupStyle.push( style );
                    $(this).attr( 'style', style + ' display: block !important;' );
                });

                hiddenElems.eq(0).css( 'left', -10000 );
                
                var clone = hiddenElems.eq(0).clone();
                $('body').append(clone);

                dims = getDims(clone);

                hiddenElems.each( function() {
                    $(this).attr( 'style', backupStyle.shift() );
                });
                clone.remove();
            }
            
        }

        return dims;
    };
    $.fn.nailthumb.evalServerSideParams = function(image,container,options) {
        if (options.serverSideParams) {
            var params = {};
            if (!options.serverSideParams.noServerResize) {
                var w = null, h = null;
                if (options.serverSideParams.width) w = options.serverSideParams.width;
                else if (options.width) w = options.width;
                if (options.serverSideParams.height) h = options.serverSideParams.height;
                else if (options.height) h = options.height;
                if (!(w && h)) {
                    resetContainer(container,options);
                    var containerDims = getContainerDims(container,options);
                    w = containerDims.innerWidth;
                    h = containerDims.innerHeight;
                }
                if (w && h) {
                    params.w = w;
                    params.h = h;
                    if (options.serverSideParams.mode!='resize') {
                        if (options.method=='crop') params.mode = 'crop';
                        if (options.serverSideParams.mode) params.mode = options.serverSideParams.mode;
                    }
                }
            }
            
            $.each(options.serverSideParams, function(key,val) {
                if (key!='width' && key!='height' && key!='mode' && key!='noServerResize' && val) {
                    params[key]=val;
                }
            });
            var pars = "";
            $.each(params, function(key,val) {
                pars+=";"+key+"="+val;
            });
            debug(pars,params);
            return pars;
        } else {
            return "";
        }
    };
    $.fn.nailthumb.setServerSideParams = function(image,container,options) {
        if (options.serverSideParams) {
            var url = image.attr("src");
            if (image.data('nailthumb.originalImageUrl')) {
                url = image.data('nailthumb.originalImageUrl');
            }
            image.data('nailthumb.originalImageUrl',url);
            var pars = $.fn.nailthumb.evalServerSideParams(image,container,options);
            url += pars;
            image.attr("src",url);
        }
    };
    $.fn.nailthumb.toggleDebug = function() {
        DEBUG = !DEBUG;
    };
    $.fn.nailthumb.doThumb = function(image,container,options) {
        doThumb(image,container,options);
    };
    $.fn.nailthumb.defaults = {
        onStart: null,
        onFinish: null,
        loadingClass: 'nailthumb-loading',
        imageUrl: null,
        imageFromWrappingLink: false,
        imageCustomFinder: null/*function(element,options){
            return null;
        }*/,
        imageClass:'nailthumb-image',
        afterReplace: null,
        afterAppear: null,
        replaceAnimation: 'fade',
        selfStartAfterAppear: false,
        animationTime: 1000,
        animation: 'swing',
        keepImageDimensions: false,
        method: 'crop',
        fitDirection: null,
        proportions: null,
        ifImageAddContainer: true,
        containerClass: 'nailthumb-container',
        maxEnlargement: null,
        maxShrink: null,
        preload: true,
        nostyle: false,
        width: null,
        height: null,
        title: null,
        titleClass: 'nailthumb-title',
        titleAttr: 'title',
        titleWhen: 'hover',
        titleScrolling: true,
        titleScrollerClass: 'nailthumb-title-scroller',
        animateTitle: true,
        titleAnimationTime: 500,
        titleAnimation: 'swing',
        serverSideParams: null
    };
    function log(log, jQueryobj) {
        try {
            debug(log, jQueryobj, true);
        } catch(ex) {}
    };
    function debug(log, jQueryobj, force) {
        try {
            if ((DEBUG && window.console && window.console.log) || force)
                window.console.log(log + ': ' + jQueryobj);
        } catch(ex) {}
    };
    function debugObject(log, jQueryobj, force) {
        try {
            if (!jQueryobj) jQueryobj=log;
            debug(log, jQueryobj);
            if ((DEBUG && window.console && window.console.log) || force)
                window.console.debug(jQueryobj);
        } catch(ex) {}
    };
})(jQuery);
/*
 * END: jquery.nailthumb.1.1.js
 */