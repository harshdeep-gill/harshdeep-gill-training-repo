@php
	$image_args = [
		'size' => [
			'width'   => 120,
			'height'  => 120,
		],
	];
@endphp

<a href="tel:+1-877-585-1235" class="lp-header__cta">
	<figure class="lp-header__cta-avatar">
		<x-image image_id="18" :args="$image_args" />
	</figure>
	<span class="lp-header__cta-content">
		<span class="lp-header__cta-content-text">
			<x-escape content="Talk to a Polar Expert" />
		</span>
		<span class="lp-header__cta-content-phone-number">
			<span><x-escape content="+1-877-585-1235" /></span>
			<x-svg name="call" />
		</span>
	</span>
</a>
