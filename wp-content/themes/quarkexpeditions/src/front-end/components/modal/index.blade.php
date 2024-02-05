@props( [
	'id'       => '',
	'class'    => '',
	'title'    => '',
	'subtitle' => '',
	'form_id'  => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'modal', $class ];

@endphp

<tp-modal
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
>
	<tp-modal-content class="modal__content">
		@if ( ! empty( $title ) || ! empty( $subtitle ) )
			<header class="modal__header">
				@if ( ! empty( $title ) )
					<h3>{{ $title }}</h3>
				@endif
				@if ( ! empty( $subtitle ) )
					<p>{{ $subtitle }}</p>
				@endif
			</header>
		@endif
		<x-modal.close />
		<div class="modal__body">
			{!! $slot !!}
		</div>
		@if ( ! empty( $form_id ) )
			<footer class="modal__footer">
				<x-form.buttons>
					<x-form.submit form="{{ $form_id }}">Request a Quote</x-form.submit>
				</x-form.buttons>
			</footer>
		@endif
	</tp-modal-content>
</tp-modal>
