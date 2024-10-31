@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) ) {
		return;
	}
@endphp

<x-tabs update_url='yes' current_tab="{{ $ships[0]['id'] ?? '' }}">
	<x-tabs.header>
		@foreach ( $ships as $ship )
			@if ( ! empty( $ship['id'] ) )
				<x-tabs.nav id="{{ $ship['id'] ?? '' }}" title="{{ $ship['title'] ?? '' }}" />
			@endif
		@endforeach
	</x-tabs.header>

	<x-tabs.content>
		@foreach ( $ships as $ship )
			@if ( ! empty( $ship['id'] ) )
				<x-tabs.tab id="{{ $ship['id'] ?? '' }}">
					<x-parts.ship :ship="$ship" />
				</x-tabs.tab>
			@endif
		@endforeach
	</x-tabs.content>
</x-tabs>
