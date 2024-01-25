@props( [
	'type' => '',
	'url'  => '#',
] )

@php
	if ( empty( $type ) ) {
		return;
	}
@endphp


<a href="{{ $url }}" class="lp-footer__social-link" title="{{ $type }}" target="_blank" rel="nofollow noopener noreferrer">
	<x-svg name="social/{{ $type }}" />
</a>
