@props( [
	'id'                    => '',
	'class'                 => '',
	'full_width_mobile'     => false,
	'explicit_close_button' => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'modal', $class ];
	$classes[] = $full_width_mobile ? 'modal--full-width-mobile' : '';

@endphp

<tp-modal
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
>
	<tp-modal-content class="modal__content">
		@if ( ! empty( $explicit_close_button ) )
			<tp-modal-close class="modal__close-button">
				<button><x-svg name="cross" /></button>
			</tp-modal-close>
		@endif
		<div class="modal__content-wrap">
			{!! $slot !!}
		</div>
	</tp-modal-content>
</tp-modal>
