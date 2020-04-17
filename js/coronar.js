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

        /** Make Tables Great Again! */
        let $summary = $( '.mdp-coronar-summary-tbl' );

        /** Foreach Summary table on page. */
        $summary.each( function( index ) {

            /** Options for DataTable. */
            let options = {

                /** Disable length menu. */
                bLengthChange: false,

                /** Disable pagination. */
                paging: false,

                /** Disable auto-width */
                autoWidth: false,

                /** Disable padding */
                padding: false,

            };

            /** If flag column exist. */
            if ( $( this ).find( '.mdp-coronar-flag' ).length ) {

                /** Disable sorting by first (flag) column. */
                options['aoColumnDefs'] = [ {"bSortable": false, "aTargets": [$( this ).find( '.mdp-coronar-flag' ).index()]} ];

            }

            /** If table was filtered, order by filter column */
            if ( $( this ).find( '.mdp-coronar-filter-order' ).length ) {

                /** Default order by country column alphabetically. */
                options['order'] = [[ $( this ).find( '.mdp-coronar-filter-order' ).index(), "desc" ]];

            /** If country column exist sort by it. */
            } else if ( $( this ).find( '.mdp-coronar-country' ).length ) {

                /** Default order by country column alphabetically. */
                options['order'] = [[ $( this ).find( '.mdp-coronar-country' ).index(), "asc" ]];

            }

            /** Show/Hide Search box */
            options['bFilter'] = ( mdpCoronar.showSearch === 'true');

            /** Enable Responsive */
            if ( ( 'true' === mdpCoronar.responsiveTable ) ) {

                options['rowReorder'] = {
                    selector: 'td:nth-child(2)'
                };

                options['responsive'] = true;

            }

            /** Init DataTable. */
            let table = $( this ).DataTable( options );

            /** Disable row reorder on touch devices. */
            if ( ( 'true' === mdpCoronar.responsiveTable ) ) {

                table.rowReorder.disable();

            }

            /** Listen for details display event. */
            table.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {

                /** Work only on show data. */
                if ( ! showHide ) { return; }

                /** Foreach chart in row copy chart in child tr. */
                $( datatable.row( row.index() ).node() ).find( '.ct-chart svg' ).each( function ( index ) {

                    let classes_str = $( this ).parent().attr( 'class' );
                    let chart_class_id = classes_str.replace( 'ct-chart ct-golden-section', '' ).trim();

                    /** Copy Chart */
                    let $chart = $( '.' + chart_class_id + ' svg' ).clone();
                    let $child_cell = $( this ).parent().parent().parent().next().find( '.' + chart_class_id );
                    $child_cell.html( $chart );

                    /** Fix charts colors. */
                    let color_class = $( this ).parent().parent().attr( 'class' );
                    $child_cell.parent().addClass( color_class );

                } );

            } );

        } );

        /** Mark last visible td */
        $( 'tr' ).find( 'td:visible:last' ).addClass( 'mdp-coronar-last-visible' );

        let rTime;
        let timeout = false;
        let delta = 50;
        $( window ).on( 'resize', function() {

            rTime = new Date();
            if ( timeout === false ) {

                timeout = true;
                setTimeout( resizeEnd, delta );

            }

        } );

        function resizeEnd() {

            if ( new Date() - rTime < delta ) {

                setTimeout( resizeEnd, delta );

            } else {

                timeout = false;

                $( 'td.red' ).removeClass( 'mdp-coronar-last-visible' );
                $( 'tr' ).find( 'td:visible:last' ).addClass( 'mdp-coronar-last-visible' );

            }
        }

    } );

} ( jQuery ) );
