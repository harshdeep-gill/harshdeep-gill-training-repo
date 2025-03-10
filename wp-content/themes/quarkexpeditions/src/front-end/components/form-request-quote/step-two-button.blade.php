@props( [
	'back_button_text'        => __( 'Back to Travel Details', 'qrk' ),
	'back_button_text_mobile' => __( 'Back', 'qrk' ),
	'button_text'             => __( 'Submit', 'qrk' ),
] )

@php
	if ( empty( $button_text ) ) {
		return;
	}
@endphp

<div class="form-request-quote__buttons-wrap">
	<x-button type="button" appearance="outline" size="big" class="form-request-quote__previous-step-button">
		<span class="form-request-quote__previous-step-button-text">
			<x-escape :content="$back_button_text" />
		</span>
		<span class="form-request-quote__previous-step-button-text-mobile">
			<x-escape :content="$back_button_text_mobile" />
		</span>
	</x-button>

	<x-form.submit size="big">
		<x-escape :content="$button_text" />
	</x-form.submit>
</div>
