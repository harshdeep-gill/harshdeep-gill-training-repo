@props( [
	'instagram_post_id' => '',
] )

@php
	if ( empty( $instagram_post_id ) ) {
		return;
	}

	wp_enqueue_script( 'instagram-embed' );
@endphp

<div class="instagram-embed">
	<blockquote class="instagram-media instagram-embed__media" data-instgrm-permalink="https://www.instagram.com/p/{{ $instagram_post_id }}">
	</blockquote>
</div>
