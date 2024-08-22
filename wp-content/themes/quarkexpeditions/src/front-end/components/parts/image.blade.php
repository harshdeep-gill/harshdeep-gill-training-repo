@props( [
	'image_id' => 0,
] )

@php
if ( empty( $image_id ) ) {
	return;
}
@endphp
<figure class="wp-block-post-featured-image size-large">
	<x-image
		:image_id="$image_id"
	/>
</figure>
