@props( [
	'current' => '0',
	'total'   => '0',
] )

@php
	if ( empty( $current ) || empty( $total ) ) {
		return;
	}
@endphp

<p class="press-releases__result-count">
	<x-escape :content="sprintf( __( 'Showing %s of %s results', 'qrk' ), $current, $total )" />
</p>
