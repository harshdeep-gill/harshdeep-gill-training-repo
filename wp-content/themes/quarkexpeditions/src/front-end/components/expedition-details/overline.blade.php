@props( [
	'region'     => '',
	'duration'   => '',
	'from_price' => '',
] )

@php
	if ( empty( $region ) && empty( $duration ) && empty( $from_price ) ){
		return;
	}
@endphp

<div class="expedition-details__overline overline">
	@if ( ! empty( $region ) )
		<span><x-escape :content="$region" /></span>
	@endif
	@if ( ! empty( $duration ) )
		<span>{{ __( 'From', 'qrk' ) }} <x-escape :content="$duration" /> {{ __( 'days', 'qrk' ) }}</span>
	@endif
	@if ( ! empty( $from_price ) )
		<span>{{ __( 'From', 'qrk' ) }} <x-escape :content="$from_price" /></span>
	@endif
</div>
