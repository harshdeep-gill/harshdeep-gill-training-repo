@props( [
	'phone_number' => quark_get_template_data( 'dynamic_phone_number', [] )['default_phone_number'] ?? '',
	'text'         => '',
] )

@php
	if ( empty( $text ) ) {
		$text = __( 'Give us a Call' );
		$text = ! empty( $phone_number ) ? sprintf( '%s: %s', $text, $phone_number ) : $text;
	}

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
