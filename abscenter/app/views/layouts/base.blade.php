<!DOCTYPE html>
	<html lang="pl">
	<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
			<meta name="description" content="Firma ABS CENTER oferuje szeroką gamę usług w dziedzinach takich jak car audio, systemy zabezpieczeń, cb-radia, systemy odzyskiwania skradzionych pojazdów, zestawy głośnomówiące. Oferujemy usługi najwyższej jakości.">
			<meta name="keywords" content="autoalarmy, car audio, zabezpieczenia, odzyskiwanie pojazdów">
			@section('title')
				<title>ABS CENTER</title>
			@show

			{{ HTML::style('assets/css/reset.css') }}
			{{ HTML::style('assets/css/style.css') }}

			{{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') }}
			{{ HTML::script('assets/js/modernizr.custom.js') }}
			{{ HTML::script('http://maps.google.com/maps/api/js?sensor=false') }}
			{{ HTML::script('assets/js/gmap3.min.js') }}
			{{ HTML::script('assets/js/jquery.scrollstop.js') }}
			{{ HTML::script('assets/js/scripts.js') }}
	</head>
	<body>
	<!--[if lte IE 9]>
		<div id="notice">
				<p class="browsehappy"><b>UWAGA!</b> Używasz przestarzałej przeglądarki. Z tego powodu elementy strony mogą nie funkcjonować prawidłowo. <br><a href="http://browsehappy.com/">Zaktualizuj swoją przeglądarkę</a> aby cieszyć się prawidłowym wyglądem i funkcjonowaniem stron.</p>
		</div>
	<![endif]-->
	<div id="wrapper">
		
		<header id="logo"><a href="/" id="home-link"><img src="assets/images/logo.png" alt="ABS CENTER - logo"></a></header>
	
		<nav id="mainmenu">
			<ul>
				<li><a href="about">O firmie</a></li>
				<li><a href="oferta">Oferta</a></li>
				<li><a href="systemy-gps">System GPS</a></li>
				<li><a href="kontakt">Kontakt</a></li>
			</ul>
		</nav>

			
		@yield('content')
			

	</div>
		
	</body>
</html>