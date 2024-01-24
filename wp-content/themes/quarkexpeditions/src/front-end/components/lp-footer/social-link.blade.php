@props( [
	'type' => '',
	'url'  => '#',
] )


<a href="{{ $url }}" class="lp-footer__social-link" title="{{ $type }}">
	<x-svg name="social/{{ $type }}" />
</a>