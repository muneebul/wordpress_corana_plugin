/**
 * COVID19 Coronavirus Visual Dashboard
 * Exclusively on Envato Market: https://1.envato.market/coronar
 *
 * @encoding        UTF-8
 * @version         1.0.7
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

"use strict";

/** Run jQuery scripts */
( function ( $ ) {

    "use strict";

    /** Document Ready. */
    $( document ).ready( function () {

        google.charts.load('current', {
            'packages':['geochart'],
            'mapsApiKey': mdpCoronarMap.mapsApiKey
        } );
        google.charts.setOnLoadCallback( drawRegionsMap );

        function drawRegionsMap() {

            $( '.mdp-coronar-map-box' ).each( function ( index ) {

                let summary = $( this ).data( 'summary' );
                let color_1 = $( this ).data( 'color-1' );
                let color_2 = $( this ).data( 'color-2' );
                let region = $( this ).data( 'region' );
                let landColor = $( this ).data( 'land-color' );
                let waterColor = $( this ).data( 'water-color' );

                let data = google.visualization.arrayToDataTable( summary );

                let options = {
                    colors: [color_1, color_2],
                    backgroundColor: waterColor,
                    datalessRegionColor: landColor,
                    tooltip: { isHtml: true },
                    width: '100%'
                };

                /** Set region if we have one. */
                if ( region ) {
                    options['region'] = region;
                }

                let chart = new google.visualization.GeoChart( $( this )[0] );

                chart.draw( data, options );

                /** Create trigger to resizeEnd event. */
                $( window ).resize( function() {

                    if ( this.resizeTO ) {

                        clearTimeout( this.resizeTO );

                    }

                    this.resizeTO = setTimeout(function() {

                        $( this ).trigger( 'resizeEnd' );

                    }, 500 );

                } );

                /** Redraw graph when window resize is completed. */
                $( window ).on( 'resizeEnd', function() {

                    chart.draw( data, options );

                } );

            } );

        }

    } );

} ( jQuery ) );
