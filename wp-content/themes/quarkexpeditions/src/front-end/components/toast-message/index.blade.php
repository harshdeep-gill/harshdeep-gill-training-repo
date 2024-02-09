@props( [
	'type'    => '',
	'message' => '',
	'visible' => false,
] )

@php
	$classes = [ 'toast-message' ];
	$icon = '';


	$valid_toast_type =  match( $type ) {
		'error' => 'error',
		default => 'success'
	};

	$classes[] = 'toast-message--' . $valid_toast_type;

	$icon = match( $type ) {
		'error' => 'alert',
		default => 'check'
	};
@endphp

<quark-toast-message @class( $classes )
	@if ( $visible )
		visible="true"
	@endif
>
	<span class="toast-message__icon"><x-svg name="{{ $icon }}"/></span>
	<p><x-escape :content="$message"/></p>
	{{--
		This needs to be type="button" so that it does not intefere with submit
		functionality if it is ever placed inside a form.
	--}}
	<button type="button" class="toast-message__close">
		<x-svg name="cross"/>
	</button>
</quark-toast-message>
