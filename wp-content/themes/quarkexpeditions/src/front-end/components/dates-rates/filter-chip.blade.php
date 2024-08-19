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

<quark-dates-rates-filter>
	<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="dates-rates__filter-chip">
		<x-button accordion_id="{{ $accordion_id }}" type="button" appearance="outline" class="dates-rates__filter-chip-button">
			<x-escape :content="$title" />
		</x-button>
	</x-drawer.drawer-open>
</quark-dates-rates-filter>
