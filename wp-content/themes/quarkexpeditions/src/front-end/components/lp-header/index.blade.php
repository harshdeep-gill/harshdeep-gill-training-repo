@props( [
	'logo_url' => '',
] )

<header class="lp-header full-width">
	<div class="lp-header__wrap">
		<x-lp-header.logo :url="$logo_url" />
		<x-lp-header.cta />
	</div>
</header>
