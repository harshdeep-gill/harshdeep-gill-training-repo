@props( [
	'url' => '',
] )

@php
	$logo_name = 'logo-v2';
@endphp

<a href="{{ $url }}" class="lp-header__logo">
	<x-svg name="{{ $logo_name }}" />
</a>

{{--Logo displayed on compact version of lp-header.--}}
<a href="{{ $url }}" class="lp-header__logo lp-header__logo--compact">
	<x-svg name="{{ $logo_name . '-compact' }}" />
</a>
