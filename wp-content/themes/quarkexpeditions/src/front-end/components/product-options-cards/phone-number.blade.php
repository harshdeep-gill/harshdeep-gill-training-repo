@props( [
	'phone_number' => '',
	'text'         => '',
] )

@php
	if ( empty( $phone_number ) || empty( $text ) ) {
		return;
	}
@endphp

<x-button
    size="big"
    appearance="outline"
    href="tel:{{ $phone_number }}"
>
    <x-escape :content="$text" />
</x-button>
