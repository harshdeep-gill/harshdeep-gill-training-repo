@props( [
	'url' => '',
] )

<a href="{{ $url }}" class="lp-header__logo">
	<x-svg name="logo" />
</a>

{{--Logo displayed on compact version of lp-header.--}}
<a href="{{ $url }}" class="lp-header__logo lp-header__logo-compact">
	<x-svg name="logo-compact" />
</a>
