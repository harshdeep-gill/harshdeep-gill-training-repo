@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="lp-footer__column">
	@if ( ! empty( $url ) )
		<a href="{{ $url }}">
			{!! $slot !!}
		</a>
	@else
		{!! $slot !!}
	@endif
</div>
