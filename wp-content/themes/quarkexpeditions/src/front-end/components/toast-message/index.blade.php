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
	<span class="icon"><x-svg name="{{ $icon }}"/></span>
	<p><x-escape :content="$message"/></p>
	<button class="toast-message__close">
		<x-svg name="cross"/>
	</button>
</quark-toast-message>
