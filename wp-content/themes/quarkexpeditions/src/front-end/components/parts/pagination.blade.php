@props( [
	'pagination'      => '',
	'current_page'    => 0,
	'total_pages'     => 0,
	'first_page_link' => '',
	'last_page_link'  => '',
] )

@php
	if ( empty( $pagination ) ) {
		return;
	}
@endphp

<x-pagination>
	<x-pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" />
	<x-pagination.links>
		@if ( ! empty( $first_page_link ) )
			<x-pagination.first-page :href="$first_page_link" >{!! __( 'First', 'qrk' ) !!}</x-pagination.first-page>
		@endif
		{!! $pagination !!}
		@if ( ! empty( $last_page_link ) )
			<x-pagination.last-page :href="$last_page_link" >{!! __( 'Last', 'qrk' ) !!}</x-pagination.last-page>
		@endif
	</x-pagination.links>
</x-pagination>
