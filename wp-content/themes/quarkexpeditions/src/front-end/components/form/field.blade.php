@props( [
	'class'      => '',
	'validation' => [],
	'min_length' => 4,
] )

<tp-form-field @class( [ 'form-field', $class ] )
@foreach( $validation as $validation_name )
	{{ $validation_name  }}="yes"
@endforeach >
{{ $slot }}
</tp-form-field>
