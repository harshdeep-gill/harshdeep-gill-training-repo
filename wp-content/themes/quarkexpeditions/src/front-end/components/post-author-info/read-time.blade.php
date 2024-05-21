@props( [
	'duration' => '',
] )

@php
	$duration = $duration . ' min read';
@endphp

<p class="post-author-info__duration">
	<x-escape :content="$duration" />
</p>
