@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="contact-cover-card__title h4">
	<x-content :content="$title" />
</h2>
