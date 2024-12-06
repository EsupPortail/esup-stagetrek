jQuery(document).ready(function($) {

    // Boutons switchs on/off pour l'accessibilité
	$('.bt-access').on('click', function() {
        accessDo();
	});
    // Gestion de l'affichage accessibilité
    function accessDo() {
        var a_contrast = 0,
            a_dys_font = 0,
            a_dys_line = 0,
            a_justify = 0,
            body_class = '';
        // récupération des switchs on/off
        if( $('input.access-contrast').is(':checked') ) {
            a_contrast = 1;
            body_class = body_class + ' unistrap-aria-contrast';
        }
        if( $('input.access-dys-font').is(':checked') ) {
            a_dys_font = 1;
            body_class = body_class + ' unistrap-aria-dys-font';
        }
        if( $('input.access-dys-line').is(':checked') ) {
            a_dys_line = 1;
            body_class = body_class + ' unistrap-aria-dys-line';
        }
        if( $('input.access-justify').is(':checked') ) {
            a_justify = 1;
            body_class = body_class + ' unistrap-aria-justification';
        }
        // écriture dans le local storage
        localStorage.setItem('unistrap-access-contrast', a_contrast);
        localStorage.setItem('unistrap-access-dys-font', a_dys_font);
        localStorage.setItem('unistrap-access-dys-line', a_dys_line);
        localStorage.setItem('unistrap-access-justify', a_justify);
        // écriture des class sur le body
        $('body').removeClass('unistrap-aria-contrast unistrap-aria-dys-line unistrap-aria-dys-font unistrap-aria-justification');
		$('body').addClass(body_class);
    }
    // Affichage on/off des switchs pour l'accessibilité
    function accessLoad() {
        if( localStorage.getItem('unistrap-access-contrast') && localStorage.getItem('unistrap-access-contrast') == 1 ) {
            $('input.access-contrast').prop('checked',true);
        } else {
            $('input.access-contrast').prop('checked',false);
        }
        if( localStorage.getItem('unistrap-access-dys-font') && localStorage.getItem('unistrap-access-dys-font') == 1 ) {
            $('input.access-dys-font').prop('checked',true);
        } else {
            $('input.access-dys-font').prop('checked',false);
        }
        if( localStorage.getItem('unistrap-access-dys-line') && localStorage.getItem('unistrap-access-dys-line') == 1 ) {
            $('input.access-dys-line').prop('checked',true);
        } else {
            $('input.access-dys-line').prop('checked',false);
        }
        if( localStorage.getItem('unistrap-access-justify') && localStorage.getItem('unistrap-access-justify') == 1 ) {
            $('input.access-justify').prop('checked',true);
        } else {
            $('input.access-justify').prop('checked',false);
        }
        accessDo();
    }
    accessLoad(); // first load

    // amélioration de la nav-ghost
    if( $('.unistrap .navbar.navbar-main').length && $('.unistrap .navbar.navbar-ghost').length ) {
        var nH = $('.unistrap .navbar.navbar-main').outerHeight(true);
        $('.unistrap .navbar.navbar-ghost').css('height',nH+'px');
    }
	

});



$(window).scroll( function() {

    // amélioration de la nav-ghost
    if( $('.unistrap .navbar.navbar-main').length && $('.unistrap .navbar.navbar-ghost').length ) {
        var nH = $('.unistrap .navbar.navbar-main').outerHeight(true);
        $('.unistrap .navbar.navbar-ghost').css('height',nH+'px');
    }
	
	if( $(window).scrollTop() < 200 ){

		$('.unistrap').removeClass('unistrap-scroll'); // Animation sticky header
		$('.hautdepage').removeClass('show'); // Flèche retour en haut de page : hide

	} else {

		$('.unistrap').addClass('unistrap-scroll'); // Animation sticky header
		$('.hautdepage').addClass('show'); // Flèche retour en haut de page : show

	}

});