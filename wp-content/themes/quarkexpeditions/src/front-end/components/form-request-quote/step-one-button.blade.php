@props( [
	'button_text' => __( 'Continue to Contact Details', 'qrk' ),
] )

@php
	if ( empty( $button_text ) ) {
		return;
	}
@endphp

<div class="form-request-quote__buttons-wrap">
	<x-button type="button" size="big" class="form-request-quote__next-step-btn">
		<x-escape :content="$button_text" />
	</x-button>
</div>
