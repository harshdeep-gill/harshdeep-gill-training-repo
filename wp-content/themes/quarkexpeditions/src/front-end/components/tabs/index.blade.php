@props( [
	'current_tab' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-tabs class="tabs">
	<tp-tabs current-tab="{{ $current_tab }}" update-url="yes">
		{!! $slot !!}
	</tp-tabs>
</quark-tabs>
