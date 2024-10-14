@props( [
    'rate' => 0,
] )

@php
    if ( empty( $rate ) ) {
        return;
    }
@endphp

<span>
    <strong>
        {{ $rate }}% {{ __( 'Goods and Services Tax (GST)', 'qrk' ) }}
    </strong>
    {{ __( '(not included)', 'qrk' ) }}
</span>