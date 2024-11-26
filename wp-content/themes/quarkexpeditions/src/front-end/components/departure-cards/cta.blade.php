@props( [
	'text'                => '',
	'availability_status' => '',
] )

@php
	if ( empty( $text ) ) {
		$text = __( 'View Cabin Pricing & Options', 'qrk' );
	}

	$appearance = '';
	$disabled   = false;

	if ( ! empty( $availability_status ) && 'S' === $availability_status ) {
		$text       = __( 'Sold Out', 'qrk' );
		$appearance = 'outline';
		$disabled   = true;
	}
@endphp

<x-button type="button" size="big" class="departure-cards__cta" :appearance="$appearance" :disabled="$disabled">
	<x-escape :content="$text" />
</x-button>
