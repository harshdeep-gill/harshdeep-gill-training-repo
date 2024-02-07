@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section class="review-cards" :full_width="true">
	<div class="review-cards__wrap">
		{!! $slot !!}
	</div>
</x-section>
