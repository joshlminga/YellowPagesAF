document.addEventListener( 'DOMContentLoaded', function () {
    var elementExists = document.getElementById("secondary-slider");
    if (elementExists) {
        var secondarySlider = new Splide( '#secondary-slider', {
            fixedWidth  : 100,
            height      : 60,
            gap         : 10,
            cover       : true,
            isNavigation: true,
            pagination : false,
            arrows     : false,
            focus       : 'center',
        } ).mount();
        
        var primarySlider = new Splide( '#primary-slider', {
            type       : 'loop',
    		autoplay    : true,
            heightRatio: 0.5,
            pagination : false,
            arrows     : true,
            cover      : true,
        } ); // do not call mount() here.
        
        primarySlider.sync( secondarySlider ).mount();
    }else{
        var primarySlider = new Splide( '#primary-slider', {
            type       : 'loop',
            autoplay    : true,
            heightRatio: 0.5,
            pagination : false,
            arrows     : true,
            cover      : true,
        } ); // do not call mount() here.
        
        primarySlider.mount();
    }
} );


jQuery(document).ready(function($){
    $('.popup-video').magnificPopup({
      type:'inline',
      midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });
});