@props( [
	'title' => '',
] )

<p class="icon-info-columns__title">
	<x-escape :content="$title" />
</p>
