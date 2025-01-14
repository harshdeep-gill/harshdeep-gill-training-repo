@php
	$url       = quark_get_template_data( 'site_url', '' );
	$site_name = quark_get_template_data( 'site_name', '' );
@endphp

<a href="{{ $url }}" class="header__logo" aria-label="{{ $site_name }}">
	<x-svg name="logo-v2" />
</a>
