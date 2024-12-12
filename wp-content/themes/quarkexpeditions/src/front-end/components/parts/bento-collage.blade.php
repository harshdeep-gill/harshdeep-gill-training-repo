@props( [
	'items' => [
		'size'             => '',
		'image_id'         => '',
		'title'            => '',
		'description'      => '',
		'content_position' => '',
		'cta'              => [
			'text'   => '',
			'url'    => '',
			'target' => '',
		],
	]
] )

@php
	var_dump( $items );
@endphp
