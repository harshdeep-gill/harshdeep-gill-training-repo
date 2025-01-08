@props( [
	'is_compact' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-expedition-search-sidebar-filters-inputs-container
	@if ( ! empty( $is_compact ) )
		compact="yes"
	@endif
>
	{!! $slot !!}
</quark-expedition-search-sidebar-filters-inputs-container>
