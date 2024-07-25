@props( [
	'class'        => '',
	'title'        => '',
	'no_of_guests' => 1,
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$no_of_guests = intval( $no_of_guests );

	$classes = [ 'product-options-cards__room-title' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<span><x-escape :content="$title" /></span>
	@for ( $i = 0; $i < $no_of_guests; $i++ )
		<span class="product-options-cards__room-title-icon">
			<x-svg name="person" />
		</span>
	@endfor
</div>
