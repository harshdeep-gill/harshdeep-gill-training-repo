@props( [
	'image_id' => 0,
	'link' => '',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [];

@endphp

<figure class="featured-image size-large typography-spacing">
	<x-maybe-link :href="$link">
		<x-image
			:image_id="$image_id"
			:args="$image_args"
		/>
	</x-maybe-link>
</figure>
