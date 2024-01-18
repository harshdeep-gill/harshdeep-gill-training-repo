@props( [
	'logo_url' => '',
] )

<x-section seamless="true">
	<header class="lp-header">
		<x-lp-header.logo :url="$logo_url" />
		<x-lp-header.cta />
	</header>
</x-section>
