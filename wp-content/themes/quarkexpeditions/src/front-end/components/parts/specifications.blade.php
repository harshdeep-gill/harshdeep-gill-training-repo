@props( [
	'title'          => '',
	'specifications' => [],
])

@php
	if ( empty( $specifications ) ) {
		return;
	}
@endphp

<x-specifications>
	<x-specifications.title :title="$title ?? ''" />
	@if ( ! empty( $specifications ) )
		<x-specifications.items>
			@foreach ( $specifications as $specification )
				<x-specifications.item :label="$specification['label'] ?? ''" :value="$specification['value'] ?? ''" />
			@endforeach
		</x-specifications.items>
	@endif
</x-specifications>
