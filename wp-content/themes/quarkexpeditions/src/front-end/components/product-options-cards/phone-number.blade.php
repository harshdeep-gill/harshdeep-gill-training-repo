@props( [
	'phone_number' => quark_get_template_data( 'dynamic_phone_number', [] )['default_phone_number'] ?? '',
	'text'         => __( 'Give us a Call', 'qrk' ),
] )

@php
	if ( empty( $phone_number ) ) {
		return;
	}
@endphp

<x-button
    size="big"
    appearance="outline"
    href="tel:{{ $phone_number }}"
>
    <div class="product-options-cards__call-text">
		<x-escape :content="$text" />
	</div>
	<x-escape :content="$phone_number" />
</x-button>
