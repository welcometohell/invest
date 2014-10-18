

// jQuery.noConflict();


/* accordion add active class */

function initAccordion() {
	jQuery(".accordion").on("show",function (e) {
     jQuery(e.target).prev(".accordion-heading").find(".accordion-toggle").addClass("active");
 }).on("hide",function (e) {
         jQuery(this).find(".accordion-toggle").not(jQuery(e.target)).removeClass("active");
     }).each(function () {
         var $a = jQuery(this);
         $a.find("a.accordion-toggle").attr("data-parent", "#" + $a.attr("id"));
     });
}


/* clickable 1 level bootstrap menu */

function initClickableMenu() {
	jQuery("#nav li.dropdown > .dropdown-toggle").removeAttr("data-toggle data-target");
}





/* init prettyphoto */

function initPrettyphoto() {

	jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({
       deeplinking: false,
       social_tools: " ",
       hook: 'data-rel'
    });
}




function initBars() {

	/* in-viewport plugin */
	(function($){$.belowthefold=function(element,settings){var fold=$(window).height()+$(window).scrollTop();return fold<=$(element).offset().top-settings.threshold;};$.abovethetop=function(element,settings){var top=$(window).scrollTop();return top>=$(element).offset().top+$(element).height()-settings.threshold;};$.rightofscreen=function(element,settings){var fold=$(window).width()+$(window).scrollLeft();return fold<=$(element).offset().left-settings.threshold;};$.leftofscreen=function(element,settings){var left=$(window).scrollLeft();return left>=$(element).offset().left+$(element).width()-settings.threshold;};$.inviewport=function(element,settings){return!$.rightofscreen(element,settings)&&!$.leftofscreen(element,settings)&&!$.belowthefold(element,settings)&&!$.abovethetop(element,settings);};$.extend($.expr[':'],{"below-the-fold":function(a,i,m){return $.belowthefold(a,{threshold:0});},"above-the-top":function(a,i,m){return $.abovethetop(a,{threshold:0});},"left-of-screen":function(a,i,m){return $.leftofscreen(a,{threshold:0});},"right-of-screen":function(a,i,m){return $.rightofscreen(a,{threshold:0});},"in-viewport":function(a,i,m){return $.inviewport(a,{threshold:0});}});})(jQuery);


	jQuery('.progress:in-viewport').each(function() {
		var $barEl = jQuery(this).find('.bar');

		if($barEl.width()==5) {
			$barEl.delay(700).stop().animate({'width':jQuery(this).attr('data-percentage')+'%'}, 1000);
		}
  });

}




/* tooltip for socials init */

function initTooltip() {
    jQuery("[data-toggle='tooltip']").tooltip();
    jQuery("[data-toggle='tooltip-pretty']").tooltip();
}



/* flexcarousel slider */

function initflexCarousel() {

	jQuery('.flexslider.flexCarousel').flexslider({
     animation: "slide",
     useCSS: true,
     slideshow: false,
     slideshowSpeed: 7000,
     animationSpeed: 500,
     animationLoop: true,
     itemWidth: 960,
     itemMargin: 0,
     minItems: 1,
     maxItems: 10,
     move: 1,
     controlNav: true,
	 controlsContainer: ".my-controls",
	  start: function(slider) {
	   jQuery(".flexslider.flexCarousel").removeClass("loading-slider");
	   slider.flexAnimate(1);
	   jQuery('.total-slides').text(slider.count);
	     },
	   after: function(slider) {

	    var zm = slider.currentSlide;
	    var $current = jQuery('.flexCarousel li:nth-child(' + (zm+1) + ')');

	 jQuery('.flexCarousel li').removeClass('active');
	 $current.addClass('active');

	   }
	 });
}


/* init easy flex slider - testimonials */

function initEasyFlex() {

    jQuery('.flexslider.easyFlex').flexslider({
        animation: "slide",              //String: Select your animation type, "fade" or "slide"
        easing: "easeInOutExpo",               //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
        // easing types :
        // swing, easeInQuad, easeOutQuad, easeInOutQuad, easeInCubic, easeOutCubic,
        // easeInOutCubic, easeInQuart, easeOutQuart, easeInOutQuart, easeInQuint,
        // easeOutQuint, easeInOutQuint, easeInSine, easeOutSine, easeInOutSine,
        // easeInExpo, easeOutExpo, easeInOutExpo, easeInCirc, easeOutCirc,
        // easeInOutCirc, easeInElastic, easeOutElastic, easeInOutElastic, easeInBack,
        // easeOutBack, easeInOutBack, easeInBounce, easeOutBounce, easeInOutBounce
        direction: "horizontal",        //String: Select the sliding direction, "horizontal" or "vertical"
        reverse: false,                 //{NEW} Boolean: Reverse the animation direction
        animationLoop: true,             //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
        smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode
        startAt: 0,                     //Integer: The slide that the slider should start on. Array notation (0 = first slide)
        slideshow: true,                //Boolean: Animate slider automatically
        slideshowSpeed: 5000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
        animationSpeed: 1000,            //Integer: Set the speed of animations, in milliseconds
        initDelay: 0,                   //{NEW} Integer: Set an initialization delay, in milliseconds
        randomize: false,               //Boolean: Randomize slide order

        // Primary Controls
        controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        directionNav: false,             //Boolean: Create navigation for previous/next navigation? (true/false)

        // Usability features
        pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
        pauseOnHover: true,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
        touch: true,                    //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
        video: true,                   //{NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
        useCSS: false,                   //{NEW} Boolean: Slider will use CSS3 transitions if available


        // Secondary Navigation
        keyboard: true,                 //Boolean: Allow slider navigating via keyboard left/right keys
        multipleKeyboard: false,        //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
        mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel

        // Callback API
        start: function () {
        }
    });

}

/* logos slider */

  function carouselFlexsliderInit() {
      jQuery('.logoSlider .flexslider').flexslider({
          animation: "slide",
          animationLoop: true,
          slideshow: false,
          itemWidth: 190,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
          minItems: 1,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
          move: 1,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
          controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
          directionNav: true
      });
  }


/* work slider */
function genericFlexsliderInit() {
    jQuery('.genericSlider .flexslider').flexslider({
        animation: "slide",
        animationLoop: true,
        slideshow: false,
        itemWidth: 333,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
        minItems: 1,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
        move: 0,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
        controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        directionNav: true
    });
}



/* link smooth scroll to top */
function scrollToTop(i) {
    if (i == 'show') {
        if (jQuery(this).scrollTop() != 0) {
            jQuery('#toTop').fadeIn();
        } else {
            jQuery('#toTop').fadeOut();
        }
    }
    if (i == 'click') {
        jQuery('#toTop').click(function () {
	        if (navigator.userAgent.match(/(Android)/)) {
	                    window.scrollTo(0,0) // first value for left offset, second value for top offset
	        			return false;
	        }else{
	        	jQuery('body,html').animate({scrollTop: 0}, 600);
	            return false;
	        }
        });
    }
}




/* parallax effect */

function initParallax() {
  //.parallax(xPosition, speedFactor, outerHeight) options:
	//xPosition - Horizontal position of the element
	//inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
	//outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport

  jQuery('#section1').parallax("50%", 0.3);
	jQuery('#section2').parallax("50%", 0.3);
	jQuery('#section3').parallax("50%", 0.3);
	jQuery('#section4').parallax("50%", 0.3);
	jQuery('#section5').parallax("50%", 0.3);
}

/* delay function */

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();



jQuery(document).ready(function () {
    //load curve to box
    jQuery('.lineBtm').load('images/svg/curve-bottom.svg');
    jQuery('.lineLeft').load('images/svg/curve-left.svg');
	  jQuery('.lineIcon').load('images/svg/curve-icon.svg');
	  jQuery('.lineOverlay').load('images/svg/curve-overlay.svg');
});


jQuery(document).ready(function () {
	initAccordion();
	initTooltip();
	scrollToTop('click');
	initPrettyphoto();

    //IE placeholder support
    jQuery('input[placeholder],textarea[placeholder]').placeholder();
});

jQuery(window).load(function () {

	initParallax();
	initClickableMenu();
	initflexCarousel();
	initEasyFlex();
	carouselFlexsliderInit();
  genericFlexsliderInit();

});
jQuery(window).resize(function() {
    delay(function(){
        initParallax();
    }, 500);
});
jQuery(window).scroll(function () {
  scrollToTop('show');
	initBars();
});



/* isotope init */

jQuery.Isotope.prototype._getCenteredMasonryColumns = function () {
    this.width = this.element.width();

    var parentWidth = this.element.parent().width();

    // i.e. options.masonry && options.masonry.columnWidth
    var colW = this.options.masonry && this.options.masonry.columnWidth || // or use the size of the first item
            this.$filteredAtoms.outerWidth(true) || // if there's no items, use size of container
            parentWidth;

    var cols = Math.floor(parentWidth / colW);
    cols = Math.max(cols, 1);

    // i.e. this.masonry.cols = ....
    this.masonry.cols = cols;
    // i.e. this.masonry.columnWidth = ...
    this.masonry.columnWidth = colW;
};

jQuery.Isotope.prototype._masonryReset = function () {
    // layout-specific props
    this.masonry = {};
    // FIXME shouldn't have to call this again
    this._getCenteredMasonryColumns();
    var i = this.masonry.cols;
    this.masonry.colYs = [];
    while (i--) {
        this.masonry.colYs.push(0);
    }
};

jQuery.Isotope.prototype._masonryResizeChanged = function () {
    var prevColCount = this.masonry.cols;
    // get updated colCount
    this._getCenteredMasonryColumns();
    return ( this.masonry.cols !== prevColCount );
};

jQuery.Isotope.prototype._masonryGetContainerSize = function () {
    var unusedCols = 0, i = this.masonry.cols;
    // count unused columns
    while (--i) {
        if (this.masonry.colYs[i] !== 0) {
            break;
        }
        unusedCols++;
    }

    return {
        height: Math.max.apply(Math, this.masonry.colYs),
        // fit container to columns that have been used;
        width: (this.masonry.cols - unusedCols) * this.masonry.columnWidth
    };
};


jQuery(window).load(function () {

    var $container = jQuery('#iContainer'), // object that will keep track of options
            isotopeOptions = {}, // defaults, used if not explicitly set in hash
            defaultOptions = {
                filter: '*',
                sortBy: 'original-order',
                sortAscending: true,
                layoutMode: 'masonry'
            };


    var setupOptions = jQuery.extend({}, defaultOptions, {
        itemSelector: '.galleryItem',
        masonry: {
            // columnWidth: $container.width() / 4

        }
    });

    // set up Isotope
    $container.isotope(setupOptions);

    var $optionSets = jQuery('#galleryOptions').find('.option-set'), isOptionLinkClicked = false;

    // switches selected class on buttons
    function changeSelectedLink($elem) {
        // remove selected class on previous item
        $elem.parents('.option-set').find('.selected').removeClass('selected');
        // set selected class on new item
        $elem.addClass('selected');
    }


    $optionSets.find('a').click(function () {
        var $this = jQuery(this);
        // don't proceed if already selected
        if ($this.hasClass('selected')) {
            return;
        }
        changeSelectedLink($this);
        // get href attr, remove leading #
        var href = $this.attr('href').replace(/^#/, ''), // convert href into object
        // i.e. 'filter=.inner-transition' -> { filter: '.inner-transition' }
                option = jQuery.deparam(href, true);
        // apply new option to previous
        jQuery.extend(isotopeOptions, option);
        // set hash, triggers hashchange on window
        jQuery.bbq.pushState(isotopeOptions);
        isOptionLinkClicked = true;
        return false;
    });


    var hashChanged = false;

    jQuery(window).bind('hashchange', function (event) {
        // get options object from hash
        var hashOptions = window.location.hash ? jQuery.deparam.fragment(window.location.hash, true) : {}, // do not animate first call
                aniEngine = hashChanged ? 'best-available' : 'none', // apply defaults where no option was specified
                options = jQuery.extend({}, defaultOptions, hashOptions, { animationEngine: aniEngine });
        // apply options from hash
        $container.isotope(options);
        // save options
        isotopeOptions = hashOptions;

        // if option link was not clicked
        // then we'll need to update selected links
        if (!isOptionLinkClicked) {
            // iterate over options
            var hrefObj, hrefValue, $selectedLink;
            for (var key in options) {
                hrefObj = {};
                hrefObj[ key ] = options[ key ];
                // convert object into parameter string
                // i.e. { filter: '.inner-transition' } -> 'filter=.inner-transition'
                hrefValue = jQuery.param(hrefObj);
                // get matching link
                $selectedLink = $optionSets.find('a[href="#' + hrefValue + '"]');
                changeSelectedLink($selectedLink);
            }
        }

        isOptionLinkClicked = false;
        hashChanged = true;
    })// trigger hashchange to capture any hash data on init
            .trigger('hashchange');

});


