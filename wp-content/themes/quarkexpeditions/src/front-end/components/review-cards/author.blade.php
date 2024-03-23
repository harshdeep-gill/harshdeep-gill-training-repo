@props( [
	'name' => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}
@endphp

<div class="review-cards__author">
	<strong><x-escape :content="$name" /></strong>
</div>
