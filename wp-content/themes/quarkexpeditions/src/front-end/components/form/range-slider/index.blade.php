@props ( [
	'label'          => '',
	'range_prefix'   => '',
	'range_suffix'   => '',
	'id'             => '',
	'min'            => 0,
	'max'            => 0,
	'selected_value' => [],
	'class'          => '',
	'name'           => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );

	if ( empty( $slot ) || ! isset( $min ) || ! isset( $max ) || absint( $min ) > absint( $max ) ) {
		return;
	}

	// Set the range prefix to an empty string if it is not set.
	$range_prefix = $range_prefix ?? '';

	$range_start = absint( $min );
	$range_end   = absint( $max );

	// Reassign the values back to the range variable with the prefix, if it exists.
	$range = sprintf( '%s - %s %s', $range_prefix . $range_start, $range_prefix . $range_end, $range_suffix );
@endphp

<quark-range-slider
	@class( [ 'form__range-slider', $class ] )
	prefix="{{ $range_prefix }}"
	suffix="{{ $range_suffix }}"
	min="{{ $min }}"
	max="{{ $max }}"
	selected-value="{{ wp_json_encode( $selected_value ?? [] ) }}"
>
	<div class="form__range-slider-wrapper">
		<span class="form__range-slider-track"></span>
		<x-form.range-slider.input :name="$name" :value="$min" :id="$id" />
		<x-form.range-slider.input :name="$name" :value="$max" :id="$id" />
	</div>
	<div class="form__range-slider-description">
		@if ( ! empty( $label ) )
			<label for="{{ $id }}">
				<x-escape :content="$label" />
			</label>
		@endif

		@if ( ! empty( $range ) )
			<span class="form__range-slider-range">
				<x-escape :content="$range" />
			</span>
		@endif
	</div>
</quark-range-slider>
