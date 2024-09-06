@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-dates-rates-pagination-next-page>
	<button class="page-numbers next"><x-content :content="$slot" /></button>
</quark-dates-rates-pagination-next-page>
