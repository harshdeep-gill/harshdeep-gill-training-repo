@props( [
	'name' => '',
] )

@php
	$file = get_template_directory() . '/src/assets/svg/' . ( $name ?? '' ) . '.svg';
	if ( file_exists( $file ) ) {
		require $file;
	}
@endphp
