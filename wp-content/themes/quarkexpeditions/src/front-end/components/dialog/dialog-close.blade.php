@props( [
	'dialog_id' => '',
	'class'    => '',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $dialog_id ) ) {
		return;
	}
@endphp

<quark-dialog-close @class( [ $class, 'dialog__close-button' ] ) dialog-id="{{ $dialog_id }}">
	<button><x-svg name="cross" /></button>
</quark-dialog-close>
