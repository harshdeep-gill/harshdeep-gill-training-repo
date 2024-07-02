@props( [
	'regions' => [],
] )

@php
	if ( empty( $regions ) ) {
		return;
	}
@endphp

<ul class="expedition-details__regions h4">
	@foreach ( $regions as $region )
		<li class="expedition-details__region">
			<x-escape :content="$region" />
		</li>
	@endforeach
</ul>
