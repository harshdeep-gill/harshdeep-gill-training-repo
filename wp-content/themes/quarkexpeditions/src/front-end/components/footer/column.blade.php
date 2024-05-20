@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="footer__column">
	@if ( ! empty( $title ) )
		<h4 class="footer__column-title">
			<x-escape :content="$title" />
		</h4>
	@endif

	{!! $slot !!}
</div>
