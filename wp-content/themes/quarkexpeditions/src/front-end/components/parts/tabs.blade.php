@props( [
	'tabs'       => [],
	'update_url' => false,
] )

@php
	if ( empty( $tabs ) || ! is_array( $tabs ) ) {
		return;
	}

	// Process update URL.
	$update_url = $update_url ? 'yes' : 'no';
@endphp

<x-tabs :update_url="$update_url">
	<x-tabs.header>
		@foreach ( $tabs as $tab )
			<x-tabs.nav
				:id="$tab['id'] ?? ''"
				:title="$tab['title'] ?? ''"
				:active="$tab['active'] ?? false"
			/>
		@endforeach
	</x-tabs.header>

	<x-tabs.content>
		@foreach ( $tabs as $tab )
			<x-tabs.tab
				:id="$tab['id'] ?? ''"
				:open="$tab['active'] ?? false"
			>
				{!! $tab['content'] ?? '' !!}
			</x-tabs.tab>
		@endforeach
	</x-tabs.content>
</x-tabs>
