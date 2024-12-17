@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-lp-form-modal-cta
	class="hero__form-modal-cta color-context--dark"
	form_id="inquiry-form"
	:countries="[
		'IN' => __( 'India', 'qrk' ),
		'AU' => __( 'Australia', 'qrk' ),
		'US' => __( 'United States', 'qrk' ),
		'CA' => __( 'Canada', 'qrk' ),
	]"
	:states="[
		'AU' => [
			'ACT' => __( 'Australian Capital Territory', 'qrk' ),
			'JBT' => __( 'Jervis Bay Territory', 'qrk' ),
		],
		'US' => [
			'AA' => __( 'Armed Forces Americas', 'qrk' ),
			'AE' => __( 'Armed Forces Europe', 'qrk' ),
		],
		'CA' => [
			'AB' => __( 'Alberta', 'qrk' ),
			'BC' => __( 'British Columbia', 'qrk' ),
		],
	]"
>
	<x-button type="button" size="big">
		<x-content :content="$slot" />
	</x-button>
</x-lp-form-modal-cta>
