@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<li class="expedition-cards__option expedition-cards__options-count-wrap">
	<span>&hellip; +<span class="expedition-cards__options-count"></span> {{ __( 'more', 'qrk' ) }}</span>
	<x-tooltip icon="info">
		{!! $slot !!}
	</x-tooltip>
</li>
