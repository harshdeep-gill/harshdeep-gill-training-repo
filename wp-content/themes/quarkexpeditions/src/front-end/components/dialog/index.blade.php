@props( [
	'id'    => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'dialog', $class ];
@endphp

<dialog
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
>
	<div class="dialog__content">
		<x-dialog.dialog-close :dialog_id="$id" />
		{!! $slot !!}
	</div>
</dialog>
