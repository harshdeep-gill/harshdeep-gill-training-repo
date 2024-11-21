@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="media-text-cta__secondary-text">
	{!! $text !!}
</div>
