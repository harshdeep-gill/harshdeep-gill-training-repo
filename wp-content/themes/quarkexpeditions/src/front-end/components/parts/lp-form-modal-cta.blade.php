@props( [
	'text'          => '',
	'class'         => '',
	'form_id'       => '',
	'color'         => '',
	'hidden_fields' => [],
] )

<x-lp-form-modal-cta :form_id="$form_id" :class="$class" :hidden_fields="$hidden_fields">
	<x-button type="button" size="big" :color="$color">
		<x-escape :content="$text" />
	</x-button>
</x-lp-form-modal-cta>
