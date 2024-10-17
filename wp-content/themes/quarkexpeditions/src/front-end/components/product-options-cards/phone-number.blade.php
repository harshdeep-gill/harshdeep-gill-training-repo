@props( [
	'phone_number' => quark_get_template_data( 'dynamic_phone_number', [] )['default_phone_number'] ?? '',
	'text'         => __( 'Give us a Call', 'qrk' ),
] )

@php
	if ( empty( $phone_number ) ) {
		return;
	}

	if ( ! empty( $text ) ) {
		$text = sprintf( '%s: %s', $text, $phone_number );
	} else {
		$text = $phone_number;
	}
@endphp

<x-button
    size="big"
    appearance="outline"
    href="tel:{{ $phone_number }}"
>
    <x-escape :content="$text" />
</x-button>
