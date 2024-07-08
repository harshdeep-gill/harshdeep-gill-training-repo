@props( [
	'title' => ''
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="menu-list" no_border="true">
	@if ( ! empty( $title ) )
		<p class="menu-list__title overline">
			<x-escape :content="$title" />
		</p>
	@endif

	<ul class="menu-list__list">
		{!! $slot !!}
	</ul>
</x-section>
