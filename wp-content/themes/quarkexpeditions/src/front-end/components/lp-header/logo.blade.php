@props( [
	'url'       => '',
	'dark_mode' => false,
] )

@php
	$logo_name = 'logo';

	if ( true === $dark_mode ) {
		$logo_name = 'dark/' . $logo_name;
	}
@endphp

<a href="{{ $url }}" class="lp-header__logo">
	<x-svg name="{{ $logo_name }}" />
</a>

{{--Logo displayed on compact version of lp-header.--}}
<a href="{{ $url }}" class="lp-header__logo lp-header__logo--compact">
	<x-svg name="{{ $logo_name . '-compact' }}" />
</a>
