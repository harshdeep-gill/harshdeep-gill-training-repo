@props( [
	'current_page' => 1,
	'total_pages'  => 1,
] )

<quark-dates-rates-pagination-links-controller total-pages="{!! esc_attr( $total_pages ) !!}" current-page="{!! esc_attr( $current_page ) !!}">
	<x-dates-rates.pagination.prev />
	<x-dates-rates.pagination.page-number />
	<x-dates-rates.pagination.dots />
	<x-dates-rates.pagination.next />
</quark-dates-rates-pagination-links-controller>
