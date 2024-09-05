@props( [
	'current_page' => 1,
	'total_pages'  => 1,
] )

<x-pagination>
	<x-pagination.items-per-page />
	<x-pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" />
	<x-pagination.links>
		<x-dates-rates.pagination.page-number :prev="true">Prev</x-dates-rates.pagination.page-number>
		<x-dates-rates.pagination.page-number number="1" :current="true">1</x-dates-rates.pagination.page-number>
		<x-dates-rates.pagination.page-number number="2">2</x-dates-rates.pagination.page-number>
		<x-dates-rates.pagination.page-number number="3">3</x-dates-rates.pagination.page-number>
		<x-dates-rates.pagination.dots />
		<x-dates-rates.pagination.page-number :next="true">Next</x-dates-rates.pagination.page-number>
	</x-pagination.links>
</x-pagination>
