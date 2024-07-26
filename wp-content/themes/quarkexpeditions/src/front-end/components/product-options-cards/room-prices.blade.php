@props( [
	'class'            => '',
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) || empty( $discounted_price ) ) {
		return;
	}

	$classes = [ 'product-options-cards__room-prices' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<div class="product-options-cards__room-prices-info">
		<div class="product-options-cards__room-prices-discounted">
			<x-escape :content="$original_price" />
		</div>
		<div class="product-options-cards__room-prices-original">
			<x-escape :content="$discounted_price" />
		</div>
	</div>
	<x-form.field class="product-options-cards__room-prices-selector">
		<x-form.select label="Number of Rooms" name="">
			<x-form.option value="">Select</x-form.option>
				<x-form.option value="1" label="1">1</x-form.option>
				<x-form.option value="2" label="2">2</x-form.option>
				<x-form.option value="3" label="3">3</x-form.option>
				<x-form.option value="4" label="4">4</x-form.option>
				<x-form.option value="5" label="5">5</x-form.option>
		</x-form.select>
	</x-form.field>
</div>
