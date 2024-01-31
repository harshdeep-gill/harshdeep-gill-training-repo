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

	$icon = $valid_toast_type;
@endphp

<quark-toast-message @class( $classes )
	@if ( $visible )
		visible="true"
	@endif
>
	<x-svg name="{{ $icon }}"/>
	<p><x-escape :content="$message"/></p>
	<button class="toast-message__close">
		<x-svg name="cross-white"/>
	</button>
</quark-toast-message>
