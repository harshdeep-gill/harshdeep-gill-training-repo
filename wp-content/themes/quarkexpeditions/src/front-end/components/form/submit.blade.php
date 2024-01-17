@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-form-submit class="form-submit" submitting-text="{{ __( 'Submitting...', 'quark' ) }}">
	{!! $slot !!}
</tp-form-submit>
