@props( [
	'display_banner' => false,
] )

@php
	if( empty( $slot ) || empty( $display_banner ) ) {
		return;
	}
@endphp

<div class="site-banner full-width">
	<div class="site-banner__track">
		<div class="site-banner__content">
			<span class="site-banner__slide">
				<x-content :content="$slot" />
			</span>
			<span class="site-banner__slide">
				<x-content :content="$slot" />
			</span>
		</div>
	</div>
</div>
