@props( [
	'id'    => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-dialog
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	class="{{ $class }}"
>
	<dialog class="dialog">
		<div class="dialog__content">
			<x-dialog.dialog-close />
			{!! $slot !!}
		</div>
	</dialog>
</quark-dialog>