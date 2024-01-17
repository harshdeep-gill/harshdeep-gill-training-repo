@aware( [
	'id'    => '',
	'name'  => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$for = $name ?? '';
	if ( ! empty( $id ) ) {
		$for = $id;
	}
@endphp

<label
	@if ( ! empty( $for ) )
		for="{{ $for }}"
	@endif
	@class( [ 'label', $class ] )
>
	<x-content :content="$slot"/>
</label>
