@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-lp-form-modal-cta
	class="hero__form-modal-cta color-context--dark"
	form_id="inquiry-form"
	:countries="[
		'IN' => 'India',
		'AU' => 'Australia',
		'US' => 'United States',
		'CA' => 'Canada',
	]"
	:states="[
		'AU' => [
			'ACT' => 'Australian Capital Territory',
			'JBT' => 'Jervis Bay Territory',
		],
		'US' => [
			'AA' => 'Armed Forces Americas',
			'AE' => 'Armed Forces Europe',
		],
		'CA' => [
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
		],
	]"
>
	<x-button type="button" size="big">
		<x-content :content="$slot" />
	</x-button>
</x-lp-form-modal-cta>
