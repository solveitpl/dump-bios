<!DOCTYPE html>

	<html lang="pl">
	<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
			<meta name="description" content="Firma ABS CENTER oferuje szeroką gamę usług w dziedzinach takich jak car audio, systemy zabezpieczeń, cb-radia, systemy odzyskiwania skradzionych pojazdów, zestawy głośnomówiące. Oferujemy usługi najwyższej jakości.">
			<meta name="keywords" content="autoalarmy, car audio, zabezpieczenia, odzyskiwanie pojazdów">
			<title>ABS CENTER</title>
			{{ HTML::style('assets/css/reset.css') }}
			{{ HTML::style('assets/css/style.css') }}

			{{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') }}
			{{ HTML::script('assets/js/modernizr.custom.js') }}
			{{ HTML::script('http://maps.google.com/maps/api/js?sensor=false') }}
			{{ HTML::script('assets/js/gmap3.min.js') }}
			{{ HTML::script('assets/js/jquery.scrollstop.js') }}
			{{ HTML::script('assets/js/scripts.js') }}
			<link rel='shortcut icon' type='image/png' href="/public/assets/images/favicon.png" />
        
        <script>
                if(href=="http://localhost/public/about")
                    {
                        document.getElementById("aboutmenu").style.color = "blue";
                    }
                    
        </script>
        

	</head>
	<body>
	<!--[if lte IE 9]>
		<div id="notice">
				<p class="browsehappy"><b>UWAGA!</b> Używasz przestarzałej przeglądarki. Z tego powodu elementy strony mogą nie funkcjonować prawidłowo. <br><a href="http://browsehappy.com/">Zaktualizuj swoją przeglądarkę</a> aby cieszyć się prawidłowym wyglądem i funkcjonowaniem stron.</p>
		</div>
	<![endif]-->

        
        <style>
        #mainmenu a { font-family: "Nilland-Black"; font-size: 1.5rem; text-transform: uppercase; -webkit-transition: all 0.5s; -moz-transition: all 0.5s; -ms-transition: all 0.5s; -o-
        </style>
        
        
        
        
	<div id="preloader"></div>
	<div id="bgholder"></div>
	<div id="wrapper" class="home-wrapper">
		<section id="home" class="container clearfix" data-panel="home">
			<header id="logo"><img src="assets/images/logo.png" alt="ABS CENTER - logo"></header>
		
			<nav id="mainmenu">
				<ul>
					<li><a class="menulink" data-panel="about" id="aboutmenu" href="about">O firmie</a></li>
					<li><a class="menulink" data-panel="oferta" href="oferta">Oferta</a></li>
					<li><a class="menulink" data-panel="sterownik" href="sterownik">Wysyłka sterownika</a></li>
					<li><a class="menulink" data-panel="kontakt" href="kontakt">Kontakt</a></li>
				</ul>
			</nav>
			
			<section id="turnaround">
				<!-- <nav id="arrows">
					<a class="arrow" id="l-arrow"></a>
					<a class="arrow" id="r-arrow"></a>
				</nav> -->
				<div id="turnaround-container">
					<a href="systemy-odzyskiwania-pojazdow" class="car car-hidden" id="gps"><img src="{{ asset('assets/images/car/lutowanie.png') }}" alt="Systemy GPS"></a>
					<a href="autoalarmy" class="car car-hidden" id="alarmy"><img src="{{ asset('assets/images/car/wysyłka.png') }}" alt="Alarmy"></a>
					<a href="caraudio" class="car car-hidden" id="caraudio"><img src="{{ asset('assets/images/car/naprawa.png') }}" alt="Naprawa"></a>
					<a href="systemy-glosnomowiace" class="car car-hidden" id="glosnomowiace"><img src="{{ asset('assets/images/car/wymiana.png') }}" alt="Wymiana"></a>
					<a href="cb-radio" class="car" id="cb-radio"><img src="{{ asset('assets/images/car/diagnoza.png') }}" alt="Diagnoza"></a>
				</div>

				<nav id="navigation">
					<ul>
						<li><a class="navbutton" data-ref="gps">Systemy GPS</a></li>
						<li><a class="navbutton" data-ref="alarmy">Alarmy</a></li>
						<li><a class="navbutton" data-ref="caraudio">Car Audio</a></li>
						<li><a class="navbutton" data-ref="glosnomowiace">Systemy Głośnomówiące</a></li>
						<li><a class="navbutton" data-ref="cb-radio">CB Radia</a></li>
					</ul>
				</nav>
			</section>
		</section>
	</div>
		<script type="text/javascript">
			Preloader.init();
		</script>
	<footer id="footer">
		<span id="copy">&copy; ABS CENTER | tel:507 704 537</span>
	</footer>
	<div id="cookies"><p>Nasza strona wykorzystuje pliki cookies do właściwego funkcjonowania. Kliknij OK aby potwierdzić i zamknąć to okno dialogowe lub <a href="http://wszystkoociasteczkach.pl/">dowiedz się więcej.</a></p><button id="confirm-cookie">OK</button></div>
	</body>
</html>