@props( [
	'date' => '',
] )

@php
	if ( empty( $date ) ) {
		return;
	}
@endphp

<h3 class="expedition-cards__departing-on h4">
	{{ __( 'Departing on', 'qrk' ) }}
	<x-escape :content="$date" />
</h3>
