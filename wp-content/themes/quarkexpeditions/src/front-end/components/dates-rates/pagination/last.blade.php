@props( [
	'last_page' => 1,
] )

@php
	if ( empty( $last_page ) || $last_page < 1 ) {
		return;
	}
@endphp

<quark-dates-rates-pagination-page-number number="{!! esc_attr( $last_page ) !!}" data-hidden>
	<x-pagination.last-page>{!! esc_html__( 'Last', 'qrk' ) !!}</x-pagination.last-page>
</quark-dates-rates-pagination-page-number>
