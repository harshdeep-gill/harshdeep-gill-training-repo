@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="hero__sub-title">
	<h5 class="h5"><x-escape :content="$title" /></h5>
</div>
