@props( [
	'image_id' => 0,
	'link'     => '',
	'target'   => false,
] )

@php
if ( empty( $image_id ) ) {
	return;
}
@endphp

<figure class="wp-block-post-featured-image size-large">
	@if ( ! empty( $link ) )
		<a
			href="{{ $link }}"
			@if ( $target )
				target="{{ $target }}"
			@endif
		>
	@endif
		<x-image
			:image_id="$image_id"
		/>
	@if ( ! empty( $link ) )
		</a>
	@endif
</figure>
