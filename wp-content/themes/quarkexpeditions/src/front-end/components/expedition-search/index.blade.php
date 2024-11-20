@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_script( 'querystring' );
	quark_component_enqueue_assets( 'departure-cards' );
	quark_component_enqueue_assets( 'media-carousel' );
	quark_component_enqueue_assets( 'options-button' );
	quark_component_enqueue_assets( 'product-options-cards' );
	quark_component_enqueue_assets( 'dialog' );
	quark_component_enqueue_assets( 'tooltip' );
	quark_enqueue_script( 'popover-polyfill' );
@endphp

<quark-expedition-search class="expedition-search" loading="false">
	{!! $slot !!}
</quark-expedition-search>
