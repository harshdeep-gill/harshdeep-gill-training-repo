@php
$image_args = [
	'size' => [
		'width'   => 120,
		'height'  => 120,
	],
	'transform' => [
		'crop' => 'fit',
	],
];
@endphp

<a href="tel:+1-877-585-1235" class="header__cta">
	<span class="header__cta-avatar">
		<x-image image_id="18" :args="$image_args" />
	</span>
	<span class="header__cta-content">
		<span class="header__cta-content-text">
			<x-escape content="Talk to a Polar Expert" />
		</span>
		<span class="header__cta-content-phone-number">
			<span><x-escape content="+1-877-585-1235" /></span>
			<x-svg name="call" />
		</span>
	</span>
</a>
