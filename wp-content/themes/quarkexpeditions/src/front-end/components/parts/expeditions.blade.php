@props( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-product-cards carousel_overflow="true">
	@foreach ( $cards as $card )
		<x-product-cards.card>
			@if ( ! empty( $card['image_id'] ) )
				<x-product-cards.image :image_id="$card['image_id']" />
			@endif
			@if ( ! empty(  $card['itinerary_days'] ) )
				@php
					$duration = sprintf( __( '%d day itinerary', 'qrk' ), $card['itinerary_days'] )
				@endphp
				<x-product-cards.itinerary :duration="$duration" />
			@endif

			@if ( ! empty( $card['title'] ) )
				<x-maybe-link href="{{ $card['url'] }}">
					<x-product-cards.title :title="$card['title']" />
				</x-maybe-link>
			@endif

			@if ( ! empty( $card['original_price'] ) && ! empty( $card['discounted_price'] ) )
				<x-product-cards.price
					:title="__( 'Starting from (per person)', 'qrk' )"
					:original_price="$card['original_price'] ?? ''"
					:discounted_price="$card['discounted_price'] ?? ''"
				/>
			@endif

			@if ( ! empty( $card['transfer_package'] ) )
				<x-product-cards.transfer_package
					drawer_id="transfer-package-{{ $loop->index }}"
					drawer_title="{{ __( 'Mandatory Transfer package', 'qrk' ) }}"
				>
					<strong>{{ $card['transfer_package']['title'] }}</strong>

					<ul>
						@foreach ( $card['transfer_package']['sets'] as $item )
							<li>{!! $item !!}</li>
						@endforeach
					</ul>

					@php
						$price = sprintf( __( 'Package Price: %s', 'qrk' ), $card['transfer_package']['formatted_price'] );
					@endphp
					<strong> {{ $price }}</strong>

				</x-product-cards.transfer-package>
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>
