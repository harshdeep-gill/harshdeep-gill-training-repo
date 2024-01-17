@props( [
	'post'         => $post,
	'permalink'    => '',
	'post_content' => '',
] )

@php
	if ( ! $post instanceof WP_Post ) {
		return;
	}
@endphp

<x-layout :header="false" :footer="false">
	<x-content :content="$post_content" />
</x-layout>
