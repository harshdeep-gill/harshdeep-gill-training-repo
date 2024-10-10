@props ( [
    'class' => '',
] )

@php
    if ( empty( $slot ) ) {
        return;
    }

    $classes = [ 'product-options-cards__transfer-package' ];

    if ( ! empty( $class ) ) {
        $classes[] = $class;
    }
@endphp

<div @class( $classes )>
    {!! $slot !!}
</div>
