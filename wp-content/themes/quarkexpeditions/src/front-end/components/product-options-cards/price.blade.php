@props( [
	'class'            => '',
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) || empty( $discounted_price ) ) {
		return;
	}

	$classes = [ 'product-options-cards__price' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Initialize a new variable to test conditions.
	$is_discounted = false;
	$class_title = [ 'product-options-cards__price-title' ];


	// Checking if the discounted price is different from the original or not.
	if ( ! empty( $discounted_price ) ) {
		if ( $discounted_price !== $original_price ) {
			$is_discounted = true;
			$class_title[] = 'product-options-cards__price-title--discounted';
		} else {
			$is_discounted = false;
		}
	}
@endphp

<div @class( $classes )>
	<p @class( $class_title )">{{ __( 'From', 'qrk' ) }}</p>

	@if ( true === $is_discounted )
			<div class="product-options-cards__price-info">
				<div class="product-options-cards__price--discounted">
					<h5>
						<x-escape :content="$discounted_price" />
					</h5>{{ __( 'per person', 'qrk' ) }}
				</div>
				<div class="product-options-cards__price--original">
					<x-escape :content="$original_price" />
				</div>
			</div>
	@endif

	@if ( true !== $is_discounted )
		<div class="product-options-cards__price">
			<h5>
				<x-escape :content="$original_price" />
			</h5>{{ __( 'per person', 'qrk' ) }}
		</div>
	@endif
</div>
