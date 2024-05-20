@props( [
	'url'       => '',
] )

@php
	$logo_name = 'logo';
@endphp

<a href="{{ $url }}" class="footer__logo">
	<x-svg name="{{ $logo_name }}" />
</a>

{{--Logo displayed on compact version of footer.--}}
<a href="{{ $url }}" class="footer__logo footer__logo--compact">
	<x-svg name="{{ $logo_name . '-compact' }}" />
</a>
