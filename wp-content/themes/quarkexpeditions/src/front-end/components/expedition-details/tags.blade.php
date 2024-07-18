@props( [
	'tags' => [],
] )

@php
	if ( empty( $tags ) ) {
		return;
	}
@endphp

<ul class="expedition-details__tags h4">
	@foreach ( $tags as $tag )
		<li class="expedition-details__tag">
			<x-escape :content="$tag" />
		</li>
	@endforeach
</ul>
