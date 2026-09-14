<?php

/*
| Sizes the img/{size}/{path} route may generate. Each one appears as a literal
| in the code (entity image dimensions, getImage() calls, route('image') calls).
| tests/Unit/ImageSizesConfigTest.php fails when code uses a size missing here.
*/

return [
    'allowed' => [
        '1000x1000', '1000x750', '100x100', '100x70', '110x110', '1150x598', '1150x700', '1200x848',
        '120x120', '130x85', '150x100', '150x150', '165x130', '180x180', '1920x1079', '1920x1280',
        '1920x540', '1920x600', '1920x960', '192x128', '200x150', '200x200', '250x187', '250x250',
        '250x320', '25x25', '270x270', '300x300', '30x30', '341x218', '349x250', '350x350',
        '360x180', '400x200', '400x400', '40x40', '420x700', '425x250', '450x300', '500x375',
        '500x500', '500x640', '590x330', '600x300', '600x600', '60x30', '640x1066', '640x180',
        '698x500', '730x350', '735x403', '75x75', '795x259', '800x400', '850x500', '85x85',
        '861x825', '90x80', 'autox400',
    ],
];
