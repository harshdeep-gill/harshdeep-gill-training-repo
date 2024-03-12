@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp
<div class="hero__overline overline">
	<x-escape :content="$slot"/>
</div>
