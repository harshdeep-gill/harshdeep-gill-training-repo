@props( [
	'title'         => '',
	'heading_level' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="reviews-carousel" >
	<x-section.heading>
		<x-section.title title="{{ $title }}" heading_level="{{ $heading_level }}"/>
	</x-section.heading>
	{!! $slot !!}
</x-section>
