@props( [
	'text'    => '',
	'class'   => '',
	'form_id' => '',
	'color'   => '',
] )

<x-form-modal-cta :form_id="$form_id" :class="$class">
	<x-button type="button" size="big" :color="$color">
		<x-escape :content="$text" />
	</x-button>
</x-form-modal-cta>
