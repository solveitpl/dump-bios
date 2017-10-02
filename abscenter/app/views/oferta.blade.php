@extends((($nolayout == true) ? 'layouts.plain' : 'layouts.base'))

@section('title')
	<title>Oferta - ABS CENTER</title>
@stop

@section('content')

<style>

#mini-grid
    {
        text-align: inherit;
    }
    #mini-grid { display: none; width: 98%;  background: rgba(0, 0, 0, 0.3); box-shadow: 0 0 1em rgba(0, 0, 0, 0.6); margin-bottom: 1em; margin-left: 0.5em;padding-left: 0.9em;}
#mini-grid li { display: inline-block; vertical-align: top; position: relative;     margin: 0.6rem 3.7%; padding: 0; width: 173px; height: 127px;  no-repeat; box-shadow: 0 0 2em #000; }
</style>
<section id="oferta" class="container clearfix">

	<h2 class="section-title">Oferta</h2>
	<div class="content">
		<ul id="grid">
			<li><a href="car-audio">Naprawa sterowników ABS</a></li>
			<li><a href="cb-radio">Wymiana mikro-włączników</a></li>
			<li><a href="inne-zabezpieczenia">Precyzyjne Lutowanie</a></li>

		</ul>

	</div>
</section>

@stop