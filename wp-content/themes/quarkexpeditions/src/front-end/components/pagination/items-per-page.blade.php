@props( [
	'title' => __( 'Items per page', 'qrk' ),
] )

@php
	if ( empty( $slot ) ) {
	    return;
	}
@endphp

<div class="pagination__items-per-page">
	<span class="pagination__items-per-page-text"><x-escape :content="$title" /></span>
	<x-form.select class="pagination__items-per-page-select">
		<x-form.option value="10" label="10" selected="yes">10</x-form.option>
		<x-form.option value="20" label="20">20</x-form.option>
		<x-form.option value="30" label="30">30</x-form.option>
	</x-form.select>
</div>
