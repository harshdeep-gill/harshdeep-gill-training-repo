@props( [
	'title' => '',
] )

<p class="post-author-info__name">
	<x-escape :content="$title" />
</p>
