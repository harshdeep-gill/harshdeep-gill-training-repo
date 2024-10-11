@props( [
	'class'    => '',
] )

<quark-dialog-close @class( [ $class, 'dialog__close-button' ] )>
	<button><x-svg name="cross" /></button>
</quark-dialog-close>
