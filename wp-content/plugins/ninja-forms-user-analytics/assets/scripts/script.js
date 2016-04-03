//namespace
var NfUserAnalytics = {};

/*
 * Detect Browser
 */
NfUserAnalytics.DetectBrowser = {
    init: function () {
        this.browser = this.searchString( this.dataBrowser ) || "Unknown browser";
        this.version = this.searchVersion( navigator.userAgent ) || this.searchVersion( navigator.appVersion ) || "Unknown version";
    },
    searchString: function ( data ) {
        for ( var i = 0; i < data.length; i++ ) {
            var dataString = data[ i ].string;
            var dataProp = data[ i ].prop;
            this.versionSearchString = data[ i ].versionSearch || data[ i ].identity;
            if ( dataString ) {
                if ( dataString.indexOf( data[ i ].subString ) != -1 ) {
                    return data[ i ].identity;
                }
            }
            else if ( dataProp ) {
                return data[ i ].identity;
            }
        }
    },
    searchVersion: function ( dataString ) {
        var index = dataString.indexOf( this.versionSearchString );
        if ( index == -1 ) return;
        return parseFloat( dataString.substring( index + this.versionSearchString.length + 1 ) );
    },
    dataBrowser: [
        {
            string: navigator.userAgent,
            subString: "Chrome",
            identity: "Chrome"
        }, {
            string: navigator.userAgent,
            subString: "OmniWeb",
            versionSearch: "OmniWeb/",
            identity: "OmniWeb"
        }, {
            string: navigator.vendor,
            subString: "Apple",
            identity: "Safari",
            versionSearch: "Version"
        }, {
            prop: window.opera,
            identity: "Opera"
        }, {
            string: navigator.vendor,
            subString: "iCab",
            identity: "iCab"
        }, {
            string: navigator.vendor,
            subString: "KDE",
            identity: "Konqueror"
        }, {
            string: navigator.userAgent,
            subString: "Firefox",
            identity: "Firefox"
        }, {
            string: navigator.vendor,
            subString: "Camino",
            identity: "Camino"
        }, { // for newer Netscapes (6+)
            string: navigator.userAgent,
            subString: "Netscape",
            identity: "Netscape"
        }, {
            string: navigator.userAgent,
            subString: "MSIE",
            identity: "Internet Explorer",
            versionSearch: "MSIE"
        }, {
            string: navigator.userAgent,
            subString: "Gecko",
            identity: "Mozilla",
            versionSearch: "rv"
        }, { // for older Netscapes (4-)
            string: navigator.userAgent,
            subString: "Mozilla",
            identity: "Netscape",
            versionSearch: "Mozilla"
        } ]
};

// Detect the browser - no need to wait for page load
NfUserAnalytics.DetectBrowser.init();


/*
 * Add Data to Form
 */
NfUserAnalytics.updateForm = function ( form, geoData ) {
    // cache the jQuery selector
    form = jQuery( form );

    // update user technology fields
    jQuery( '.nfua-browser', form ).val( NfUserAnalytics.DetectBrowser.browser );
    jQuery( '.nfua-browser-version', form ).val( NfUserAnalytics.DetectBrowser.version );
    jQuery( '.nfua-os', form ).val( window.navigator.platform );

    // split the city string by a comma to find the region.
    // ex. "Denver, CO"

    // find the position of the comma if it exists
    comma = geoData.city.search( "," );
    city = geoData.city;
    if ( 1 < comma ) {
        // if there's a comma there's a region

        // add two to the comma position to skip past the comma and the white space after it
        region = city.substring( comma + 2 );

        // remove the region from the city string
        city = city.substring( 0, comma );

        // update the region field
        jQuery( '.nfua-region', form ).val( region );
    }

    // update the rest of the geolocation fields
    jQuery( '.nfua-country', form ).val( geoData.country_name );
    jQuery( '.nfua-city', form ).val( city );
    jQuery( '.nfua-latitude', form ).val( geoData.lat );
    jQuery( '.nfua-longitude', form ).val( geoData.lng );
}


/*
 * Initialize
 */
jQuery( document ).ready( function ( $ ) {

    // load geo-location script
    $.getJSON( nfua.ajax_url,
        {
            action: 'nfua_data'
        }
    ).done( function ( json ) {

            console.log( json );

            // update each form on the page
            $( '.ninja-forms-form' ).each( function () {

                // populate the data.
                NfUserAnalytics.updateForm( this, json );
            } );
        } );

} );