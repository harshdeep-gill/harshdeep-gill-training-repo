@props( [
	'dialog_id' => '',
	'class'    => '',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $slot ) || empty( $dialog_id ) ) {
		return;
	}
@endphp

<quark-dialog-open @class( [ $class, 'dialog__dialog-open' ] ) dialog-id="{{ $dialog_id }}">
	{!! $slot !!}
</quark-dialog-open>
