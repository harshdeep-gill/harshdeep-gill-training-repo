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
	<select class="pagination__items-per-page-select">
		<option value="10" label="10">10</option>
		<option value="20" label="20">20</option>
		<option value="30" label="30">30</option>
	</select>
</div>
