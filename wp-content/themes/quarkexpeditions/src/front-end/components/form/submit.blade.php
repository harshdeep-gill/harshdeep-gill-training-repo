@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-form-submit class="form-submit" submitting-text="{{ __( 'Submitting...', 'qrk' ) }}">
	<x-button type="submit">
		{!! $slot !!}
	</x-button>
</tp-form-submit>
