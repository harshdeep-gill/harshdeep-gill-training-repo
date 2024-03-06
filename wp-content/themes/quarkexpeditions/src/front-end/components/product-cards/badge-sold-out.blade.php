@props( [
	'text' => __( 'Sold Out', 'qrk' ),
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="product-cards__badge-sold-out h5">
	<x-escape :content="$text" />
</div>
