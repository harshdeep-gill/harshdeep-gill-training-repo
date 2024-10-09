@props( [
	'class' => '',
	'min'   => '1',
	'max'   => '10',
	'step'  => '1',
	'label' => '',
] )

@php
	// Class.
	$classes = [ 'number-spinner' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Number spinner.
	wp_enqueue_script( 'tp-number-spinner' );
	wp_enqueue_style( 'tp-number-spinner' );
@endphp

@if ( ! empty( $label ) )
	<x-form.label><x-content :content="$label" /></x-form.label>
@endif

<tp-number-spinner @class( $classes ) min="{{ $min }}" max="{{ $max }}" step="{{ $step }}">
	<tp-number-spinner-decrement>
		<button class="btn" type="button">-</button>
	</tp-number-spinner-decrement>
	<tp-number-spinner-input>
		<input type="text" value="1" readonly />
	</tp-number-spinner-input>
	<tp-number-spinner-increment>
		<button class="btn" type="button">+</button>
	</tp-number-spinner-increment>
</tp-number-spinner>
