@props( [
	'options' => [],
] )

@php
	if ( empty( $options ) ) {
		return;
	}

	$options = implode( ', ', $options ?? '' )
@endphp

<div class="departure-cards__options">
	<p class="departure-cards__options-label">{{ __( 'Adventure Options', 'qrk' ) }}</p>
	<div class="departure-cards__options-content">
		{{ $options }}
	</div>
</div>
