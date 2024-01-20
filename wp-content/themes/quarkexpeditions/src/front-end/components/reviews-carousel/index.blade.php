@props( [
	'title'         => '',
	'heading_level' => '',
	'title_align'   => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="reviews-carousel" title="{{ $title }}" heading_level="{{ $heading_level }}" title_align="{{ $title_align }}" >
	{!! $slot !!}
</x-section>
