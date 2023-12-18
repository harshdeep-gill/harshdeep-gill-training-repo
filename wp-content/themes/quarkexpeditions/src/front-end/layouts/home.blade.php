@props( [
	'post'           => $post,
	'permalink'      => '',
	'post_thumbnail' => 0,
	'post_content'   => '',
] )

@php
	if ( ! $post instanceof WP_Post ) {
		return;
	}
@endphp

<x-layout>
	<x-content :content="$post_content" />
</x-layout>
