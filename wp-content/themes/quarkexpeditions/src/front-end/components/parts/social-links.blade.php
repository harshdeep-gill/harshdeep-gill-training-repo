@props( [
	'links' => [],
] )


@php

	if ( empty( $links ) ) {
		return;
	}

@endphp

<x-lp-footer.social-links>
    @foreach( $links as $link )
        <x-lp-footer.social-link :type="$link['type'] ?? ''" :url="$link['url'] ?? '#'"/>
    @endforeach
</x-lp-footer.social-links>
