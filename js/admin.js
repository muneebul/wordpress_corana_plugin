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

        /** Clear Cache button. */
        let $clearCacheBtn = $( '#mdp-coronar-clear-cache' );

        /** Click on Clear Cache button. */
        $clearCacheBtn.on( 'click', function ( e ) {

            /** Disable button and show process. */
            $clearCacheBtn.attr( 'disabled', true ).addClass( 'mdp-spin' ).find( '.material-icons' ).text( 'refresh' );

            /** Prepare data for AJAX request. */
            let data = {
                action: 'clear_cache',
                nonce: window.mdpCoronar.nonce,
                doClear: 1
            };

            /** Make POST AJAX request. */
            $.post( window.mdpCoronar.ajaxURL, data, function( response ) {

                /** Show Error message if returned false. */
                if ( response ) {
                    alert( 'Cache Cleared' );
                } else {
                    console.error( 'Looks like an error has occurred. Please try again later.' );
                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.log( response );
                console.error( 'Looks like an error has occurred. Please try again later.' );

            } ).always( function() {

                /** Enable button again. */
                $clearCacheBtn.attr( 'disabled', false ).removeClass( 'mdp-spin' ).find( '.material-icons' ).text( 'close' );

            } );

        } );

        /** Initialize CSS Code Editor. */
        let css_editor;
        if ( $( '#mdp_custom_css_fld' ).length ) {

            let editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css'
                }
            );

            css_editor = wp.codeEditor.initialize( 'mdp_custom_css_fld', editorSettings );

            css_editor.codemirror.on( 'change', function( cMirror ) {
                css_editor.codemirror.save(); // Save data from CodeEditor to textarea.
                $( '#mdp_custom_css_fld' ).change();
            } );
        }

        mdp_coronar_unsaved = false;
    } );

} ( jQuery ) );

