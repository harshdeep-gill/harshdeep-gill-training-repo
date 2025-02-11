@props( [
	'count'     => 0,
	'drawer_id' => '',
] )

@php
	if ( empty( $slot ) || empty( $count ) || $count < 0 || empty( $drawer_id ) ) {
		return;
	}
@endphp

<div class="departure-cards__options-count-wrap">
	<span>+<span class="departure-cards__options-count"><x-escape :content="$count" /></span> {{ __( 'more', 'qrk' ) }}</span>
	<x-departure-cards.tooltip icon="info">
		{!! $slot !!}
	</x-departure-cards.tooltip>
</div>

<x-drawer id="{{ $drawer_id }}" animation_direction="up" class="departure-cards__options-drawer">
	<x-drawer.header>
		<h3>{{ __( 'Adventure Options', 'qrk' ) }}</h3>
	</x-drawer.header>
	<x-drawer.body>
		<x-content :content="$slot" />
	</x-drawer.body>
</x-drawer>
