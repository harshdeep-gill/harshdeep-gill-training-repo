@props( [
	'logo_url' => '',
] )

<x-section seamless="true">
	<header class="header full-width">
		<x-header.logo :url="$logo_url" />
		<x-header.cta />
	</header>
</x-section>
