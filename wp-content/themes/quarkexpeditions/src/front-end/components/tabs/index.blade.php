@props( [
	'current_tab' => '',
	'update_url'  => 'no',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-tabs class="tabs">
	<tp-tabs current-tab="{{ $current_tab }}" update-url="{{ $update_url }}">
		{!! $slot !!}
	</tp-tabs>
</quark-tabs>
