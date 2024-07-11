@props( [
	'duration' => 0,
] )

@php
	if ( empty( $duration ) || intval( $duration ) <= 0 ) {
		return;
	}

	$duration = $duration . ' min read';
@endphp

<p class="post-author-info__duration">
	<x-escape :content="$duration" />
</p>
