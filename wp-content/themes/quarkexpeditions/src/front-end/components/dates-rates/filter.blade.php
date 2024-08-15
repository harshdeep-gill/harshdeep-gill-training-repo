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

<quark-filter>
	<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="dates-rates__filter">
		<x-button accordion_id="{{ $accordion_id }}" type="button" appearance="outline" class="dates-rates__filter-button">
			<x-escape :content="$title" />
		</x-button>
	</x-drawer.drawer-open>
</quark-filter>
