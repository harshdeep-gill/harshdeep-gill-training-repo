@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<li class="departure-cards__option departure-cards__options-count-wrap">
	<span>&hellip; +<span class="departure-cards__options-count"></span> {{ __( 'more', 'qrk' ) }}</span>
	<x-tooltip icon="info">
		{!! $slot !!}
	</x-tooltip>
</li>
