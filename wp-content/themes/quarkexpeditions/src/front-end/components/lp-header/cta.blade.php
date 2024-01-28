@props( [
	'image_id'     => 0,
	'phone_number' => '',
	'cta_text'     => '',
] )

@php
	$image_args = [
		'size' => [
			'width'   => 120,
			'height'  => 120,
		],
	];
@endphp

<a href="tel:{{ $phone_number }}" class="lp-header__cta">
	<figure class="lp-header__cta-avatar">
		<x-image :image_id="$image_id" :args="$image_args" />
	</figure>
	<span class="lp-header__cta-content">
		@if ( ! empty( $cta_text ) )
			<span class="lp-header__cta-content-text">
				<x-escape :content="$cta_text" />
			</span>
		@endif
		<span class="lp-header__cta-content-phone-number">
			<span><x-escape :content="$phone_number" /></span>
			<x-svg name="call" />
		</span>
	</span>
</a>
