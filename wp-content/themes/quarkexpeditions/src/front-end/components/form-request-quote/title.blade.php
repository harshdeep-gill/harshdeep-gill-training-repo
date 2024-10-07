@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h3 class="form-request-quote__title">
	<x-escape :content="$title" />
</h3>
