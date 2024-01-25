@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="lp-footer__featured-on">
	@if ( ! empty( $title ) )
		<h5>
			<x-escape :content="$title" />
		</h5>
	@endif

	{!! $slot !!}
</div>
