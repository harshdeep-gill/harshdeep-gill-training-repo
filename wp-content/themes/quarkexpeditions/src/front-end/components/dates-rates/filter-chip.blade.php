@props( [
	'title'        => '',
	'drawer_id'    => '',
	'accordion_id' => '',
	'type'         => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	if ( ! in_array( $type, [ '', 'currency', 'sticky-filter' ], true ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-chip
	@if ( ! empty( $accordion_id ) )
		accordion_id="{{ $accordion_id }}"
	@endif

	@if ( ! empty( $type ) )
		type="{{ $type }}"
	@endif
>
	<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="dates-rates__filter-chip">
		<x-button type="button" appearance="outline" class="dates-rates__filter-chip-button">
			<x-escape :content="$title" />
		</x-button>
	</x-drawer.drawer-open>
</quark-dates-rates-filter-chip>
