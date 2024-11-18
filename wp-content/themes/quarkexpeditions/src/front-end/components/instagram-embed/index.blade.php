@props( [
	'instagramPostId' => '',
] )

@php
	if ( empty( $instagramPostId ) ) {
		return;
	}

	wp_enqueue_script( 'instagram-embed' );
@endphp

<div class="instagram-embed">
	<blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/{{ $instagramPostId }}" data-instgrm-version="14">
	</blockquote>
</div>
