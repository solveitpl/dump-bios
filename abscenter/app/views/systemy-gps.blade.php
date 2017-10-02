@extends((($nolayout == true) ? 'layouts.plain' : 'layouts.base'))

@section('title')
	<title>Sterownik - ABS CENTER</title>
@stop

@section('content')



<style>
    .opis1
    {
        overflow: hidden;
        text-align: justify;
        height: 18rem;
        cursor: pointer;
    }

</style>
<section id="sterownik" class="container clearfix" data-panel="sterownik">

	<h2 class="section-title">Wysyłka<br>sterownika</h2>
	<div class="content">

		<div class="column-r gps-info clearfix">
            <div><p class="wysylka">Nie masz czasu przyjść do serwisu?  <br/>
                Skorzystaj z naszej oferty i wyślij swój sterownik my go naprawimy! 
                </p>
            </div>
            
            <div id="krok1">
                <img src="/public/assets/images/krok1.png" alt="krok1">
                <p class="title">KROK 1</p>
                <p class="address-title">KONTAKT <BR>TELEFONICZNY</p>
                <p class="opis1">Prosimy w celu szybkiej weryfikacji, aby każdy klient na wstępie rozmowy podał dane samochodu -
                 marka, model, rok, pojemność,następnie typ sterownika. Wskazane są też kody usterek i ich opisy, oraz charakterystyka usterki (objawy) która ma na celu określenie sensu naprawy.Warto znać historie ponieważ są to naprawy które się powielają. Usuwamy i znamy wady fabryczne oraz środowisko sterowników samochodowych .</p>
            </div>
            
            <div id="krok2">
               <img src="/public/assets/images/krok2.png" alt="krok2">
                <p class="title">KROK 2</p>
                <p class="address-title">WYPEŁNIJ<BR> FORMULARZ</p>
                <p class="opis">Wypełnij tradycyjny fomularz, podając dane, jak nr telefonu, odres zwrotny, itd.</p>
                <img class="plus" src="/public/assets/images/plus.png" alt="plus">
            </div>
            
            <div id="krok3">                
                <img src="/public/assets/images/krok3.png" alt="krok3">
                <p class="title">KROK 3</p>
                <p class="address-title">WYŚLIJ<BR> STEROWNIK</p>
                <p class="opis">Odpowiednio zabezpiecz część przed ewentualnym uszkodzeniem w trakcie transportu oraz wyślij na adres podany na dole strony,</p>
                
            </div>
		</div>	
	</div>
</section>

@stop