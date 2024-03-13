@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp
<div class="hero__overline overline">
	<x-content :content="$slot"/>
</div>
