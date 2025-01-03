@props( [
	'cards' => [],
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-product-cards>
	@foreach ( $cards as $card )
		<x-product-cards.card>
			@if ( ! empty( $card['image_id'] ) )
				<x-product-cards.image :image_id="$card['image_id']" url="{{ $card['url'] }}" />
			@endif

			@if ( ! empty( $card['departure_date'] ) )
				@php
					$card['departure_date']  = sprintf( __( 'Departs %s', 'qrk' ), $card['departure_date'] );
					$card['departure_date'] .= ! empty( $card['itinerary_days'] ) ? sprintf( __( ' | %s Days', 'qrk' ), $card['itinerary_days'] ) : '';
				@endphp
				<x-product-cards.itinerary :duration="$card['departure_date']" />
			@else
				@if ( ! empty(  $card['itinerary_days'] ) )
					@php
						$duration = sprintf( __( '%d day itinerary', 'qrk' ), $card['itinerary_days'] )
					@endphp
					<x-product-cards.itinerary :duration="$duration" />
				@endif
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
					drawer_title="{!! ! empty( $card['transfer_package']['mandatory_transfer_title'] ) ? $card['transfer_package']['mandatory_transfer_title'] : __( 'Mandatory Transfer Package', 'qrk') !!}"
					label="{!! $card['transfer_package']['offer_inclusion_text'] !!}"
				>
					<strong>{!! $card['transfer_package']['title'] !!}</strong>

					<ul>
						@foreach ( $card['transfer_package']['sets'] as $item )
							<li>{!! $item !!}</li>
						@endforeach
					</ul>

					@php
						$price = sprintf( __( 'Package Price: %s', 'qrk' ), $card['transfer_package']['formatted_price'] );
					@endphp
					<strong> {!! $price !!}</strong>

				</x-product-cards.transfer-package>
			@endif
		</x-product-cards.card>
	@endforeach
</x-product-cards>
