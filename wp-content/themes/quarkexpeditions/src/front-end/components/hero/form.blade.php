@props( [
	'modal_id' => '',
	'form_id'  => '',
	'title'    => '',
	'subtitle' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-inquiry-form
	:classes="[ 'hero__form', 'color-context--dark' ]"
>
	<x-inquiry-form.main modal_id="{{ $modal_id }}">
		{!! $slot !!}
	</x-inquiry-form.main>
	<x-inquiry-form.modal
		title="{{ $title }}"
		subtitle="{{ $subtitle }}"
		modal_id="{{ $modal_id }}"
		form_id="{{ $form_id }}"
	/>
</x-inquiry-form>
