@props( [
	'count'     => 0,
	'drawer_id' => '',
] )

@php
	if ( empty( $slot ) || empty( $count ) || $count < 0 || empty( $drawer_id ) ) {
		return;
	}
@endphp

<div class="expedition-cards__options-count-wrap">
	<span>+<span class="expedition-cards__options-count"><x-escape :content="$count" /></span> {{ __( 'more', 'qrk' ) }}</span>

	<x-expedition-cards.tooltip>
		<x-content :content="$slot" />
	</x-expedition-cards.tooltip>
</div>

<x-drawer id="{{ $drawer_id }}" animation_direction="up" class="expedition-cards__options-drawer">
	<x-drawer.header>
		<h3>{{ __( 'Adventure Options', 'qrk' ) }}</h3>
	</x-drawer.header>
	<x-drawer.body>
		<x-content :content="$slot" />
	</x-drawer.body>
</x-drawer>
