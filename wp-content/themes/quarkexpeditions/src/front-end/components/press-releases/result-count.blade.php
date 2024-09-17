@props( [
	'current' => '',
	'total' => '',
] )

@php
if ( empty( $current ) || empty( $total ) ) {
	return;
}
@endphp

<p class="result-count">
	Showing {{ $current }} of {{ $total }} results
</p>
