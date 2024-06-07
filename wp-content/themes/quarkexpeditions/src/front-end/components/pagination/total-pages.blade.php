@props( [
	'current_page' => '',
	'total_pages'  => '',
] )

@php
	if ( empty( $current_page ) || empty( $total_pages ) ) {
		return;
	}
@endphp

<div class="pagination__total-pages">
	{{ __( 'Page', 'qrk' ) }} {{ $current_page }} {{ __( 'of', 'qrk' ) }} {{ $total_pages }}
</div>
