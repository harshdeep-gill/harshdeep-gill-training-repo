@props( [
	'count' => 0,
] )

@php
	if ( empty( $slot ) || empty( $count ) ) {
		return;
	}
@endphp

<li class="departure-cards__option departure-cards__options-count-wrap">
	<span>&hellip; +<span class="departure-cards__options-count"><x-escape :content="$count" /></span> {{ __( 'more', 'qrk' ) }}</span>
	<x-departure-cards.tooltip icon="info">
		{!! $slot !!}
	</x-departure-cards.tooltip>
</li>
