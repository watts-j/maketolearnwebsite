/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	var container, button, menu, links, subMenus, i, len;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links    = menu.getElementsByTagName( 'a' );
	subMenus = menu.getElementsByTagName( 'ul' );

	// Set menu items with submenus to aria-haspopup="true".
	for ( i = 0, len = subMenus.length; i < len; i++ ) {
		subMenus[i].parentNode.setAttribute( 'aria-haspopup', 'true' );
	}

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ( 'ontouchstart' in window ) {
			touchStartFn = function( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
						if ( menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			for ( i = 0; i < parentLink.length; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );
} )();

/****** highlight Invention Kits when in an activity *****/
(function() {
  var urlparts = (window.location.pathname).split('/'); 
    if("inventions-kits" === urlparts[1]) {
        var navitems = document.getElementById("menu-primary-navigation").childNodes;
        for(var i=0; i< navitems.length; i++) {
            if(navitems[i].tagName === "LI") {
                var thelitag = navitems[i].childNodes;
                for(var j=0; j< thelitag.length; j++) {
                    if(thelitag[j].tagName === "A") {
                        var theAtag = thelitag[j].childNodes;
                        for(var k=0; k<theAtag.length; k++) {
                            if("Invention Kits" === theAtag[k].textContent) {
                                navitems[i].className = "current-menu-item";
                                return;
                            }
                        }
                    }
                }
            }
        }
    }
} )();


/******* Handles showing/hiding/moving of kit groups *********/

function showbackbtn( element ) {
    if("forward" === element.className) {
        element.previousElementSibling.previousElementSibling.style.display = "block";
    } else {
        element.style.display = "block";
    }
}
function hidebackbtn( element ) {
    if("forward" === element.className) {
        element.previousElementSibling.previousElementSibling.style.display = "none";
    } else {
        element.style.display = "none";
    }
}
function showforwbtn( element ) {
    if("back" === element.className) {
        element.nextElementSibling.nextElementSibling.style.display = "block";
    } else {
        element.style.display = "none";
    }
}
function hideforwbtn( element ) {
    if("back" === element.className) {
        element.nextElementSibling.nextElementSibling.style.display = "none";
    } else {
        element.style.display = "none";
    }
}

(function() {
    var lists = document.getElementsByClassName("kitlist");
    var i, kitlist_width_goal, forw, currentwidth, back;
    for(i = 0; i<lists.length; i++) {
        var kits = lists[i].getElementsByClassName("kit");
        kitlist_width_goal = 164 * kits.length;
        forw = lists[i].parentElement.getElementsByClassName("forward");
        back = lists[i].parentElement.getElementsByClassName("back");
        lists[i].style.width=kitlist_width_goal + "px";
        currentwidth = parseInt(window.getComputedStyle(forw[0].parentElement).width);
        
        if(currentwidth < kitlist_width_goal) {
            if(currentwidth < kitlist_width_goal) {
            forw[0].style.display = "block";
            forw[0].addEventListener('click', (function(){ 
                var currpos = parseInt(this.previousElementSibling.style.left);
                if(isNaN(currpos)) {
                    currpos = 0;
                }
                if(currpos > currentwidth - kitlist_width_goal) {
                    currpos = currpos - 50;
                    showbackbtn( this );
                    if(currpos < currentwidth - kitlist_width_goal) {
                        hideforwbtn( this );
                    }
                } else {
                    hideforwbtn( this );
                }
                this.previousElementSibling.style.left = currpos + 'px';
            }));
            back[0].addEventListener('click', (function(){ 
                var currpos = parseInt(this.nextElementSibling.style.left);
                if(isNaN(currpos)) {
                    currpos = 0;
                }
                if(currpos < 0) {
                    currpos = currpos + 50;
                    showforwbtn( this );
                    if(currpos >= 0) {
                        hidebackbtn( this );
                    }
                } else {
                    hidebackbtn( this );
                }
                this.nextElementSibling.style.left = currpos + 'px';
            }));
            }
        } else {
            
            continue;
        }
        
    }
    
} )();

