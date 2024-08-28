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
		{{ $title }}
	</h1>

	@if ( ! empty( $meta ) )
		<p class="title-meta__meta h5">
			{{ $meta }}
		</p>
	@endif
</div>
