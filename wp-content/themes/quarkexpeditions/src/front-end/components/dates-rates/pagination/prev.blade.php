@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-dates-rates-pagination-prev-page>
	<button class="page-numbers prev"><x-content :content="$slot" /></button>
</quark-dates-rates-pagination-prev-page>
