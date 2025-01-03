@props( [
	'count' => 0,
] )

@php
	if ( empty( $slot ) || empty( $count ) ) {
		return;
	}
@endphp

<li class="expedition-cards__option expedition-cards__options-count-wrap">
	<span>&hellip; +<span class="expedition-cards__options-count"><x-escape :content="$count" /></span> {{ __( 'more', 'qrk' ) }}</span>

	<x-expedition-cards.tooltip>
		<x-content :content="$slot" />
	</x-expedition-cards.tooltip>
</li>
