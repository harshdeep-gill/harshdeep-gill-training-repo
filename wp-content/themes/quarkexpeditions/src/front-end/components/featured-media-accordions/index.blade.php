@props( [
	'class' => '',
] )

@php
	if (
		empty( $slot )
		|| ! class_exists( 'DOMDocument' )
		|| ! class_exists( 'DOMElement' )
		|| ! class_exists( 'DOMXPath' )
		|| ! class_exists( 'DOMNodeList' )
	) {
		return;
	}

	$classes = [ 'featured-media-accordions' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$accordion_items_count = 0;

	// Initialize DOMDocument.
	$dom                   = new DOMDocument();
	$libxml_previous_state = libxml_use_internal_errors( true );
	$is_dom_loaded         = $dom->loadHTML( '<?xml encoding="utf-8"?>' . strval( $slot ), LIBXML_COMPACT );


	if ( $is_dom_loaded ) {
		// Clear errors and restore previous state.
		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );

		// Get xpath object to query the DOMDocument.
		$xpath    = new DOMXPath( $dom );

		$accordion_items = $xpath->query( '//tp-accordion-item' );

		if ( $accordion_items instanceof DOMNodeList ) {
			foreach ( $accordion_items as $accordion_item ) {
				++$accordion_items_count;
			}
		}
	}

	if ( $accordion_items_count > 5 ) {
		return;
	}

@endphp

<quark-featured-media-accordions @class( $classes )>
	{!! $slot !!}
</quark-featured-media-accordions>
