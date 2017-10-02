var Preloader = {
	init: function () {
		$('#preloader').show();
		$(window).load(function() {
			$('#preloader').fadeOut('800', function() {
				
			});
		});
	}
}
jQuery(document).ready(function($) {

	var global = {
		window: $(window),
		body: $('body'),
		car: $('a.car'),
		mainmenu: $('#mainmenu'),
		wrapper: $('#wrapper')
	}

	var cookie = {
		init: function () {
			
		},

		set: function (cname,cvalue,exdays){
			var d = new Date();
			d.setTime(d.getTime()+(exdays*24*60*60*1000));
			var expires = "expires="+d.toGMTString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		},

		get: function (cname){
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) 
			  {
			  var c = ca[i].trim();
			  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
			  }
			return "";
		}
	}

	var car = {

		car_container:  $('#turnaround-container'),

		init: function () {
			this.navHover();
			this.getCurrent();
			this.navUpdate();
			this.resetTimer();
			this.autoPlay();
			global.car.on('click', function(event) {
				event.preventDefault();
				content.scrollTo('oferta');
				offer.loadOffer($(this).attr('href'));
			});
			$('#turnaround').css('min-height', global.window.height() - (global.mainmenu.height() + $('#logo').height()));
		},

		getNext: function () {
			car.car_next = car.car_current.next(global.car);
			if (car.car_next.length == 0) {
				car.car_next = global.car.first();
			};
		},

		getPrev: function () {
			car.car_next = car.car_current.prev(global.car);
			if (car.car_next.length == 0) {
				car.car_next = global.car.last();
			};
		},

		arrows: function () {
			$('.arrow').bind('click', function(event) {
				car.getCurrent();
				if ($(this).attr('id') == 'r-arrow') {
					car.getNext();
				} else if ($(this).attr('id') == 'l-arrow') {
					car.getPrev();
				};
				car.carAnimate();
			});
		},

		autoPlay: function () {
			setInterval (function(){
				if (car.time === 0){
					car.getCurrent();
					car.getNext();
					car.carAnimate();
				};
				car.time--;
			}, 1000);
		},

		resetTimer: function () {
			this.time = 6;
		},

		getCurrent: function () {
			this.car_current = $(global.car, car.car_container).filter(function(index) {
				if (!$(this).hasClass('car-hidden')) {
					return this;
				};
			});
		},

		navHover: function (car_current) {
			$('#navigation').delegate('a', 'mouseenter', function(event) {
				var ref = $(this).data('ref');
				car.car_next = $(car.car_container).find('#'+ref);
				car.carAnimate();
				// car.navUpdate();
			});
		},

		carAnimate: function () {
			$(car.car_next).removeClass('car-hidden').siblings().addClass('car-hidden');
			this.getCurrent();
			this.navUpdate();
			this.resetTimer();
		},
		
		navUpdate: function () {
			var car_id = car.car_current.attr('id');
			var current_nav = $('.navbutton', $('#navigation')).filter(function() {
				if ($(this).data('ref') === car_id) {
					return this;
				};
			});
			current_nav.addClass('selected-nav').parent().siblings().find('a').removeClass('selected-nav');
		}
	}

	var menuBar = {

		init: function () {
			this.showMenuBar();
			global.body.on('click', '#go-back', function(event) {
				event.preventDefault();
				$('html, body').animate({
				scrollTop: 0
			}, 500);
			history.pushState(null, 'home', '#home');
			});
		},

		showMenuBar: function () {
			var mainmenu = global.mainmenu;
			var menuOffset = mainmenu.offset().top + mainmenu.height();
			global.window.on('scroll', function(event) {
				if (global.window.width() > 1024) {
						content.scrollBg();
					};
				if (global.window.scrollTop() > menuOffset) {
					if (!menuBar.menuExists('#menubar')) {
						global.body.append('<nav id="menubar">').find('#menubar').html(mainmenu.html()).find('ul').prepend('<li><a id="go-back">^</a></li>').parent().slideDown(200);
					};

				} else {
					if (menuBar.menuExists('#menubar')) {
						$('#menubar').slideUp(300, function(){
							$(this).remove();
						});
					};
				}
			});
		},

		menuExists: function (elem) {
			return $(elem).length
		}
	}

	var offer = {
		init: function () {
			this.buttonWidth = $('#grid').find('li').width();
			this.buttonHeight = $('#grid').find('li').width();
			$('#grid').on('click', 'a', function(event) {
				event.preventDefault();
				offer.showLoading(this);
				offer.loadOffer($(this).attr('href'));
			});
		},

		loadOffer: function (page) {
			$.post(page, function(data, textStatus, xhr) {
				var dataArray = data;
				var filtered = $(data).find('section.content');
				offer.offerChange($(data));

			});
		},

		appendOffer: function (data) {
			this.offerChange(data);
			
		},

		appendButton: function () {
			if ($('#mini-grid').length === 0) {
				$('#offer-content').parent().prepend('<ul id="mini-grid">');
			};
			$('#mini-grid').html($('#grid').html());
			$('#mini-grid').slideDown(400);
			$('#oferta').find('.section-title').append('<span id="backtext">powrót</span>').css('cursor', 'pointer').bind('click', function(event) {
				event.preventDefault();
				offer.revert();
			});
			$('#mini-grid').on('click', 'a', function(event) {
				event.preventDefault();
				offer.showLoading(this);
				offer.loadOffer($(this).attr('href'));
			});
		},

		showLoading: function (elem) {
			$('div.loading', $('#oferta')).remove();
			$(elem).append('<div class="loading">').fadeIn(200);
		},

		hideLoading: function (elem) {
			$('div.loading', $('#oferta')).fadeOut('400', function() {
				this.remove();
			});;
		},

		revert: function () {
			$('#mini-grid').slideUp(400);
			$('#offer-content').fadeOut(400, function () {
				$('#grid').fadeIn(500);
			});
			offer.minified = false;
			$('#backtext').fadeOut('300', function() {
				$(this).remove();
			});
		},

		offerChange: function (data) {
			if (offer.minified) {
				$('#offer-content').fadeOut(400, function(){
						offer.hideLoading();
						$(this).html(data.html()).fadeIn(400, function() {
						});
					});
			} else {
				if ($('#offer-content').length === 0) {
					$('#oferta').find('.content').append('<div id="offer-content" class="slide-content">');
				}
				$('#grid').fadeOut(500, function() {
					$('#offer-content').html(data.html()).fadeIn(400, function(){
						offer.appendButton();
						offer.hideLoading();
					});
					
					offer.minified = true;
				});
			}
			
		},
	}

	var content = {

		init: function () {
			
			this.appendEach();
			this.scrollPos = 0;
			this.currentElem = 'home';
			content.checkCookies();
			this.cookiesInfo();
			$('div.details').hide();
			$('button.more').show().click(function(event) {
				$('div.details').slideToggle(500);
			});
			
			global.body.on('click', 'a.menulink', function(event) {
				event.preventDefault();
				content.scrollTo($(this).attr('href'));
			});
			global.window.resize(function(event) {
				content.resize();
			});
			global.window.bind("popstate", function(event) {
				content.getURL();
				if ((content.segment != undefined) && (content.segment != null) && (content.segment != '')) {
					content.scrollTo(content.segment);
				};
			});
			global.window.load(function() {
				content.loaded = true;
				content.resize();
			});
			global.window.bind('scrollstop', function(event) {
				if (content.loaded) {
					content.updateUrl();
				};
			});
		},

		getURL: function () {
			var path = window.location.hash;
			this.segment = path.replace('#', '');
			// this.segment = path.substr(path.lastIndexOf('#') + 1);
		},

		cookiesInfo: function () {
			$('#confirm-cookie').click(function(event) {
				cookie.set('agreement', true, 1);
				$('#cookies').fadeOut('500', function() {
					$(this).remove();
				});
			});
		},

		checkCookies: function () {
			var agreement = cookie.get('agreement');
			if (agreement == "" || agreement == null) {
				$('#cookies').show();
			};
		},

		appendPage: function (page) {
			$.post(page, function(data, textStatus, xhr) {
				global.wrapper.append(data);
				return true;
			});
		},

		appendEach: function () {
			if (global.wrapper.hasClass('home-wrapper')) {

				$.ajaxSetup({ async: false });
				var pages = ['about', 'oferta', 'systemy-gps', 'kontakt'];
				$.each(pages, function(index, val) {
						content.appendPage(val);
				});
			};
		},

		scrollBg: function () {
			var scrollHeight = (global.wrapper.height() - global.window.height());
			var scrollPercent = (global.window.scrollTop() / scrollHeight) * 200;
			global.body.css('background-position', '50% '+(-scrollPercent)+'px');

		},

		scrollTo: function (elemId) {
			if ((elemId != undefined) && (elemId != 'undefined')) {
				var targetOffset = global.wrapper.find('#' + elemId).offset().top;
				if ($('body, html').is(':not(:animated)')) {

					$('html, body').stop().animate({
						scrollTop: targetOffset
					}, 500, function(){

					});
				};
			};
		},

		updateUrl: function () {
			$('.container').each(function(index, el) {
				var windowScroll = global.window.scrollTop();
				var elemHeight = $(el).height();
				var elemPos = $(el).offset().top - (elemHeight/3);
				var elemId = $(el).attr('id');
				if (content.segment != elemId) {	
					if ((windowScroll >= elemPos) && (windowScroll < elemPos + elemHeight)) {
						history.pushState(null, elemId, '#' + elemId);
						content.currentElem = elemId;
						content.getURL();
					};
				};
			});
		},
		resize: function () {
			$('.container, #turnaround').css('min-height', global.window.height());
		}
	}

var mapstyle = [
  {
	"featureType": "road.local",
	"elementType": "geometry.stroke",
	"stylers": [
	  { "color": "#373737" },
	  { "weight": 1.2 }
	]
  },{
	"featureType": "road.arterial",
	"elementType": "geometry.stroke",
	"stylers": [
	  { "color": "#373737" },
	  { "weight": 1.2 }
	]
  },{
	"featureType": "road",
	"elementType": "geometry.fill",
	"stylers": [
	  { "color": "#000000" }
	]
  },{
	"featureType": "landscape.man_made",
	"stylers": [
	  { "visibility": "off" },
	  { "color": "#838080" }
	]
  },{
	"featureType": "landscape.natural",
	"elementType": "geometry",
	"stylers": [
	  { "color": "#141414" }
	]
  },{
	"featureType": "road",
	"elementType": "labels.text.stroke",
	"stylers": [
	  { "color": "#000000" },
	  { "weight": 3 }
	]
  },{
	"featureType": "road",
	"elementType": "labels.text.fill",
	"stylers": [
	  { "color": "#ffffff" }
	]
  },{
	"featureType": "poi",
	"elementType": "labels.text.fill",
	"stylers": [
	  { "color": "#aaaaaa" }
	]
  },{
	"featureType": "poi",
	"elementType": "labels.text.stroke",
	"stylers": [
	  { "color": "#000000" }
	]
  },{
	"featureType": "poi",
	"elementType": "geometry.fill",
	"stylers": [
	  { "color": "#343434" }
	]
  },{
	"featureType": "road.arterial",
	"elementType": "labels.icon",
	"stylers": [
	  { "saturation": -100 },
	  { "gamma": 4.06 }
	]
  },{
	"featureType": "water",
	"elementType": "geometry.fill",
	"stylers": [
	  { "color": "#6f6f6f" }
	]
  },{
	"featureType": "administrative.locality",
	"elementType": "labels.text.fill",
	"stylers": [
	  { "color": "#59595a" }
	]
  },{
	"featureType": "administrative.locality",
	"elementType": "labels.text.stroke",
	"stylers": [
	  { "color": "#000000" }
	]
  },{
	"featureType": "administrative.neighbourhood",
	"elementType": "labels.text.fill",
	"stylers": [
	  { "color": "#59595a" }
	]
  },{
	"featureType": "administrative.neighbourhood",
	"elementType": "labels.text.stroke",
	"stylers": [
	  { "color": "#000000" }
	]
  }

];

var icon = "assets/images/google_maps_marker.png";

var map1 = {

	latlng: [53.785895, 20.452492],

	init: function (elem) {
		$(elem).gmap3({
			 map:{
				options:{
				disableDefaultUI: true,
				center: map1.latlng,
				zoom: 15,
				styles: mapstyle
				}
			 },
			 marker:{
				latLng: map1.latlng,
				options: {
					icon: icon
				}
			 }
		});
	}

}

var map2 = {

	latlng: [53.750839, 20.483293],

	init: function (elem) {
		$(elem).gmap3({
			 map:{
				options:{
				disableDefaultUI: true,
				center: map2.latlng,
				zoom: 15,
				styles: mapstyle
				}
			 },
			 marker:{
				latLng: map2.latlng,
				options: {
					icon: icon
				}
			 }
		});
	}
}

	cookie.init();
	content.init();
	menuBar.init();
	car.init();
	offer.init();
	map1.init('#map1');
	map2.init('#map2');

});