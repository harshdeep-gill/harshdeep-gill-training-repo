@props( [
	'title'        => '',
	'drawer_id'    => '',
	'accordion_id' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-chip
	class="dates-rates__filter-chip-container"
	@if ( ! empty( $accordion_id ) )
		accordion_id="{{ $accordion_id }}"
	@endif
>
	<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="dates-rates__filter-chip">
		<x-button type="button" appearance="outline" class="dates-rates__filter-chip-button">
			<x-escape :content="$title" />
		</x-button>
	</x-drawer.drawer-open>
</quark-dates-rates-filter-chip>
