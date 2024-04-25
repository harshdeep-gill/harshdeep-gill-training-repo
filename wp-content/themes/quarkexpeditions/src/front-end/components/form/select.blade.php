@props( [
	'id'       => '',
	'label'    => '',
	'multiple' => false,
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
	multiple="{{ $multiple ? 'yes' : 'no' }}"
	close-on-select="{{ $multiple ? 'no' : 'yes' }}"
	{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
>
	<tp-multi-select-field>
		@if ( $multiple )
			<tp-multi-select-pills></tp-multi-select-pills>
			<tp-multi-select-search>
				<input placeholder="Select...">
			</tp-multi-select-search>
		@else
			<tp-multi-select-placeholder>Select...</tp-multi-select-placeholder>
			<tp-multi-select-status format="$value"></tp-multi-select-status>
		@endif
	</tp-multi-select-field>
	<tp-multi-select-options>
		@if ( $multiple )
			<tp-multi-select-select-all select-text="Select All" unselect-text="Un-Select All">Select All</tp-multi-select-select-all>
		@endif
		{!! $slot !!}
	</tp-multi-select-options>
</tp-multi-select>
