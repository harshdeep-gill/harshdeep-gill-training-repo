@props( [
	'original_price'   => '',
	'discounted_price' => '',
] )

@php
	if ( empty( $original_price ) ) {
		return;
	}
@endphp

<div class="expedition-cards__price-wrap">
	<div class="expedition-cards__price-wrap-inner">
		<strong class="expedition-cards__price-title h4">{{ __( 'From', 'qrk' ) }}</strong>

		@if ( ! empty( $discounted_price ) )
			@if ( $discounted_price !== $original_price )
				<strong class="expedition-cards__price expedition-cards__price-now h4">{{ $discounted_price }}</strong>
				<del class="expedition-cards__price expedition-cards__price--original">{{ $original_price }}</del>
			@else
				<span class="expedition-cards__price h4">{{ $original_price }}</span>
			@endif
		@endif

		@if ( empty( $discounted_price ) )
			<strong class="expedition-cards__price h4">{{ $original_price }}</strong>
		@endif
	</div>

	{!! $slot !!}
</div>
