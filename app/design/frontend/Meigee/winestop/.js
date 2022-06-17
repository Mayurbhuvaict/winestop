var config = {
    shim: {
        jquery: {
            exports: '$'
        },
        'Smartwave_Megamenu/js/sw_megamenu':
            {
                deps: ['jquery']
            }, 
        'owl.carousel/owl.carousel.min':
            {
                deps: ['jquery']
            },
        'js/jquery.stellar.min': 
            {
            deps: ['jquery']
            },
        'js/jquery.parallax.min':
            {
            deps: ['jquery']
            }
    }
};