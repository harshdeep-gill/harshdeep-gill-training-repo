@props( [
	'text'                => '',
	'availability_status' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$appearance = '';
	$disabled   = false;

	if ( ! empty( $availability_status ) && 'S' === $availability_status ) {
		$appearance = 'outline';
		$disabled   = true;
	}
@endphp

<x-button type="button" size="big" class="departure-cards__cta" :appearance="$appearance" :disabled="$disabled">
	<x-escape :content="$text" />
</x-button>
