@props( [
	'current_page' => 1,
	'total_pages'  => 1,
] )

<quark-dates-rates-pagination-total-pages><x-pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" /></quark-dates-rates-pagination-total-pages>
