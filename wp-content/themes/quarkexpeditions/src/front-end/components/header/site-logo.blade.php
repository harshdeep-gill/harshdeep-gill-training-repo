@props( [
	'url'       => '',
	'site_name' => '',
] )

@php
	$logo_name = 'logo';
@endphp

<a href="{{ $url }}" class="header__logo" aria-label="{{ $site_name }}">
	<x-svg name="{{ $logo_name }}" />
</a>
