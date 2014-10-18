/**
 * waSlideMenu - jQuery plugin for menu organization like Facebook Help Page.
 * @version v1.0.4
 * @link https://github.com/webasyst/waslidemenu
 * @license MIT
 */
/*global $, jQuery*/
(function ($, window, document) {
    "use strict";

    $.waSlideMenu = function (el, options) {
        var pluginName = 'SlideMenu',
            base = this,
            element = el;

        // Apply namespace to css classes
        base.addCssNamespace = function (attrNames, namespace) {
            var obj = {};
            $.each(attrNames, function (k, v) {
                if (typeof v === 'string') {
                    obj[k] = namespace + pluginName + v;
                }
            });

            return obj;
        };
        // setup DOM elements, variables
        base.setup = function () {
            base.o = $.extend({}, $.waSlideMenu.defaults, options);

            base.o.classNames = base.addCssNamespace(base.o.classNames, base.o.namespace);

            // Access to jQuery and DOM versions of element
            base.$waSlideMenu = $(el);

            // Add reverse reference object
            base.$waSlideMenu.data(pluginName, base);

            base.$backs = []; // backlink menu items
            base.$items = base.$waSlideMenu.find(base.o.itemSelector);
            base.$waSlideMenu.addClass(base.o.classNames.navigationClass);

            // wrap all navigation
            base.$movable = $('<div/>', {
                'class' : base.o.classNames.wrapperClass
            });
            // detach?
            // root and all other inherited menus
            base.$inheritedMenus = base.$waSlideMenu.find(base.o.menuSelector);
            base.$rootMenu = $(base.$inheritedMenus.splice(0, 1));

            base.$rootMenu
                .addClass(base.o.classNames.allMenusClass)
                .appendTo(base.$movable.prependTo(base.$waSlideMenu));
            // add css to menus for floating left to right
            base.$inheritedMenus.addClass(base.o.classNames.allMenusClass + ' ' + base.o.classNames.inheritedMenuClass);

            var menu_item_tagname = base.$items.prop("tagName");

            // add 'back' buttons
            base.$inheritedMenus.each(function (i, val) {
                var url = $(val)
                        .closest(base.o.itemSelector)
                        .children('a')
                        .attr('href'),
                    $li = $('<' + menu_item_tagname + '/>', {
                        'class': base.o.classNames.menuItemBackClass
                    });

                if (base.o.backOnTop) {
                    $li.prependTo($(val));
                } else {
                    $li.appendTo($(val));
                }

                base.$backs.push(
                    $li.append($('<a>', {'href' : url})
                        .html(base.o.backLinkContent))
                );
            });
            // minimum height fix
            if (base.o.minimumHeight > 0) {
                base.o.minimumHeight = base.o.minimumHeight > base.$waSlideMenu.height() ? base.o.minimumHeight : base.$waSlideMenu.height();
                base.$waSlideMenu.css('min-height', base.o.minimumHeight);
            }
            base.o.previousHeight = 0;
        };
        // go to selected item
        base.gotoSelected = function (depth) {
            // we need to go deeper
            base.hideOtherMenus(depth, base.scrollToTop);
            // height fix
            base.heightFix(depth);
            // sliiiiide to other level
            base.animateSlide(depth);
        };
        // animate function
        base.animateSlide = function (depth) {
            var amount = Math.abs(depth * 100),
                callback_slide = depth > 0 ? base.o.onSlideForward : base.o.onSlideBack;

            // onSlide or onSlideBack callback
            if (callback_slide && typeof (callback_slide) === 'function') {
                callback_slide(base);
            }

            base.$movable.animate(
                {
                    'left': depth > 0 ? '-=' + amount + '%' : '+=' + amount + '%'
                },
                base.o.slideSpeed,
                base.o.slideEasing,
                function () {
                    base.scrollToTop(depth);
                    // afterSlide callback
                    if (base.o.afterSlide && typeof (base.o.afterSlide) === 'function') {
                        base.o.afterSlide(base);
                    }
                }
            );
        };

        base.scrollToTop = function (depth) {
            var $menu_children = base.$currentMenuElement.children(base.o.menuSelector),
                back_link = base.$currentMenuElement.hasClass(base.o.classNames.menuItemBackClass) ? true : false;

            // we need to understand, are backlink or previous link visible in viewport
            if (base.o.scrollToTopSpeed && (back_link || $menu_children.length > 0)) {
                var $back = depth > 0 ? $menu_children.children(base.o.itemSelector).last() : base.$currentMenuElement,
                    $offset_elem = depth > 0 ? $back.closest(base.o.menuSelector) : base.$currentMenuElement,
                    back_pos_top = $back.offset().top,
                    win_pos_top = $(window).scrollTop();
                if (back_pos_top - win_pos_top < 0) {
                    $('html, body').animate({
                        scrollTop: $offset_elem.offset().top
                    }, base.o.scrollToTopSpeed);
                }
            }
        };

        base.heightFix = function (depth) {
            if (base.o.autoHeight) {
                var h = 0,
                    to_currentmenuelement_h = 0;
                if (depth > 0) { // forward - height of inherited menu
                    to_currentmenuelement_h = (base.$currentMenuElement.offset().top - base.$waSlideMenu.offset().top) + base.$currentMenuElement.outerHeight();
                    h = base.$currentMenuElement
                        .children(base.o.menuSelector)
                        .height();
                    // if children menu height is lower then currentMenuElement offset
                    h = h < to_currentmenuelement_h ? h = to_currentmenuelement_h : h;
                } else { // height of current menu
                    h = base.$currentMenuElement
                        .closest(base.o.menuSelector)
                        .height();
                }
                if (depth < 0) { // if backwards - prevent previous height
                    h = h > base.o.previousHeight ? h : base.o.previousHeight;
                }

                h = h > base.o.minimumHeight ? h : base.o.minimumHeight;
                base.o.previousHeight = h;
                base.$waSlideMenu.css('height', h);
            }
        };

        base.hideOtherMenus = function (depth) {
            var $branchParent = base.$currentMenuElement.parentsUntil('.' + base.o.classNames.navigationClass).filter(base.o.itemSelector);

            $branchParent.push(base.$currentMenuElement[0]);

            if (depth > 0) {
                // hide all menus branches
                base.$inheritedMenus.css('visibility', 'hidden');
                // show only our inherited menus
                $branchParent.children(base.o.menuSelector).css('visibility', 'visible');
            }
        };

        base.loadContent = function (loadContainer, url) {
            var loading = $('<span/>', {
                    'class' : base.o.classNames.loadingClass
                }).html('&nbsp;'),
                clear = '<div style="clear:both"></div>';

            // TODO: (?) save content if we will fail load next page
            // base.$currentContent = $load_container.html();
            loadContainer.html(loading).append(clear);
            $.ajax(
                {
                    url: url,
                    type: 'get'
                }
            )
                .done(function (data) {
                    loadContainer.html(data + clear);
                    // set page title
                    base.changeTitle();
                    // set new url
                    base.changeUri(url);
                    // afterLoadDone callback
                    if (base.o.afterLoadDone && typeof (base.o.afterLoadDone) === 'function') {
                        base.o.afterLoadDone(base);
                    }
                    base.$waSlideMenu.trigger('afterLoadDone.' + base.o.namespace + pluginName);
                })
                .fail(function () {
                    // do not remove content and slide menu
                    base.$currentMenuElement
                        .siblings()
                        .children('.' + base.o.classNames.menuItemBackClass)
                        .trigger('click');

                    // afterLoadFail callback
                    if (base.o.afterLoadFail && typeof (base.o.afterLoadFail) === 'function') {
                        base.o.afterLoadFail(base);
                    }
                    base.$waSlideMenu.trigger('afterLoadFail.' + base.o.namespace + pluginName);
                })
                .always(function () {
                    // afterLoadAlways callback
                    if (base.o.afterLoadAlways && typeof (base.o.afterLoadAlways) === 'function') {
                        base.o.afterLoadAlways(base);
                    }
                    base.$waSlideMenu.trigger('afterLoadAlways.' + base.o.namespace + pluginName);
                });
        };

        base.changeTitle = function () {
            if (base.o.setTitle) {
                $('title').text(base.$currentMenuElement.children('a').text());
            }
        };

        base.changeUri = function (url) {
            if (!!(window.history && history.pushState)) {
                window.history.pushState({}, '', url);
            }
        };

        base.init = function () {
            base.setup();

            $.each(base.$backs, function (index, back) {
                back.on('click', 'a', function (e) {
                    base.menuItemClicked(this, e, -1);
                });
            });
            base.$items.on('click', 'a', function (e) {
                base.menuItemClicked(this, e, 1);
            });

            if (!!(window.history && history.pushState)) {
                window.onpopstate = function (event) {
                    if (event && event.state) {
                        location.reload();
                    }
                };
            }

            var $selectedItem = base.$waSlideMenu
                .find('.' + base.o.selectedClass)
                .first();

            if ($selectedItem.length) {
                base.$currentMenuElement = $selectedItem;
                var $url = base.$currentMenuElement.children('a').first(),
                    depth = $selectedItem.parentsUntil('.' + base.o.classNames.navigationClass).filter(base.o.itemSelector).length,
                    $selectedItemChildrenLen = $selectedItem.children(base.o.menuSelector).length;
                if ($.inArray($url.attr('href'), base.o.excludeUri) < 0) {
                    // if we have menus inside current element
                    if ($selectedItemChildrenLen > 0) {
                        // slide to them
                        depth += 1;
                    }
                    base.gotoSelected(depth);
                    // heightFix hack if we show last menu
                    base.heightFix($selectedItemChildrenLen);
                }
            }

            // onInit callback
            if (base.o.onInit && typeof (base.o.onInit) === 'function') {
                base.o.onInit(base);
            }
            base.$waSlideMenu.trigger('onInit.' + base.o.namespace + pluginName);
        };

        base.menuItemClicked = function (element, event, depth) {
            event.preventDefault();
            event.stopPropagation();

            base.$currentMenuElement = $(element).parent(base.o.itemSelector);

            var url = $(element).attr('href'),
                $menu_children = base.$currentMenuElement.children(base.o.menuSelector),
                back_link = base.$currentMenuElement.hasClass(base.o.classNames.menuItemBackClass) ? true : false,
                $load_container = $(base.o.loadSelector);

            // change selected class
            base.$waSlideMenu
                .find(base.o.itemSelector)
                .filter('.' + base.o.selectedClass)
                .removeClass(base.o.selectedClass);

            // if this is backlink find by url
            if (back_link) {
                base.$currentMenuElement = base.$waSlideMenu
                    .find("[href='" + url + "']:first")
                    .parent(base.o.itemSelector);
            }
            // add 'selected' class
            base.$currentMenuElement.addClass(base.o.selectedClass);

            if (base.o.loadSelector.length > 0 && $load_container.length > 0 && $.inArray(url, base.o.exludeUri) < 0 && (location.origin + url) !== window.location.href) {
                if (base.o.loadOnlyLatest === false) {
                    base.loadContent($load_container, url);
                } else if (base.o.loadOnlyLatest && $menu_children.length === 0 && depth > 0) {
                    base.loadContent($load_container, url);
                }
            }

            // if we can go deeper or up
            if ($menu_children.length > 0 || depth < 0) {
                base.gotoSelected(depth);
            }
        };

        var oldBase = $(el).data(pluginName);
        if (typeof oldBase === 'object') {
            $.each(options, function (k, v) {
                if (typeof oldBase[k] === 'function') {
                    oldBase[k](v);
                } else {
                    oldBase[k] = v;
                }
            });
        } else {
            base.init();
        }
    };

    $.waSlideMenu.defaults = {
        namespace           : 'wa',

        slideSpeed          : 400,
        slideEasing         : 'linear',
        backLinkContent     : 'Back',
        backOnTop           : false,
        selectedClass       : 'selected',
        loadSelector        : '',
        minimumHeight       : 0,
        autoHeight          : true,
        excludeUri          : ['/', '#'],
        loadOnlyLatest      : false,
        menuSelector        : 'ul',
        itemSelector        : 'li',
        setTitle            : false,
        scrollToTopSpeed    : 0,

        /* Callbacks */
        onInit              : null,
        onSlideForward      : null,
        onSlideBack         : null,
        afterSlide          : null,
        afterLoadAlways     : null,
        afterLoadDone       : null,
        afterLoadFail       : null,

        /* class names */
        classNames: {
            navigationClass         : '-nav',
            wrapperClass            : '-wrapper',
            allMenusClass           : '-menu',
            inheritedMenuClass      : '-inheritedmenu',
            menuItemBackClass       : '-back',
            loadingClass            : '-loading'
        }
    }; // $.waSlideMenu.defaults

    $.fn.waSlideMenu = function (key, val) {

        return this.each(function () {
            var options;

            // Allow for passing in an object or a single key and value
            if (typeof key !== 'object' && typeof val !== 'undefined') {
                options = {};
                options[key] = val;
            } else {
                options = key;
            }

            var t = new $.waSlideMenu(this, options);
        }); // this.each

    }; // $.fn.waSlideMenu

}(jQuery, window, document));
