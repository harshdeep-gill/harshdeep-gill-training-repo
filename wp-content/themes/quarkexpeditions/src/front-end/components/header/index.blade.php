@props( [
	'data' => [],
] )


<header class="header">
	<div class="header__wrap">
		<x-header.logo :url="$data['logo_url'] ?? ''"/>
	</div>
</header>