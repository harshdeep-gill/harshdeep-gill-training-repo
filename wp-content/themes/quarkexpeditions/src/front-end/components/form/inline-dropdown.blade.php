@props( [
	'id'    => '',
	'label' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );

	wp_enqueue_script( 'tp-multi-select' );
	wp_enqueue_style( 'tp-multi-select' );
@endphp

@if ( ! empty( $label ) )
	<x-form.label :id="$id">
		<x-escape :content="$label"/>
	</x-form.label>
@endif

<tp-multi-select
	class="form__inline-dropdown"
	multiple="no"
	close-on-select="yes"
	{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
>
	<tp-multi-select-field>
		<tp-multi-select-placeholder>{{ __( 'Select', 'qrk' ) }}</tp-multi-select-placeholder>
		<tp-multi-select-status format="$value"></tp-multi-select-status>
	</tp-multi-select-field>
	<tp-multi-select-options>
		<div class="tp-multi-select-options-container">
			{!! $slot !!}
		</div>
	</tp-multi-select-options>
</tp-multi-select>
