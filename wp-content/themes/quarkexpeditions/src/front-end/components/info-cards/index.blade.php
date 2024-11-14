@props( [
	'layout'              => 'grid',
	'mobile_carousel'     => true,
	'carousel_overflow'   => false,
	'title'               => '',
	'title_align'         => '',
	'title_heading_level' => '2',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards' ];

	if ( true === $carousel_overflow ) {
		$classes[] = 'info-cards--has-overflow';
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:full_width="true"
	:wrap="true"
	:seamless="true"
	data-layout="{{ $layout }}"
	data-mobile-carousel="{{ $mobile_carousel }}"
>
	@if ( ! empty( $title ) )
		<x-section.heading>
			<x-section.title
				:title="$title"
				:align="$title_align ?? ''"
				:heading_level="$title_heading_level ?? ''"
			/>
		</x-section.heading>
	@endif

  <x-info-cards.carousel :layout="$layout">
		{!! $slot !!}
	</x-info-cards.carousel>
</x-section>
