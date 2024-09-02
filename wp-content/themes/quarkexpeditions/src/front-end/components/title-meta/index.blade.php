@props( [
	'title' => '',
	'meta'  => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="title-meta">
	<h1 class="title-meta__title">
		<x-content :content="$title" />
	</h1>

	@if ( ! empty( $meta ) )
		<p class="title-meta__meta h5">
			<x-content :content="$meta" />
		</p>
	@endif
</div>
