@props( [
	'logo_url'     => '',
	'tc_image_id'  => 0,
	'phone_number' => '',
	'cta_text'     => '',
	'dark_mode'    => false,
] )

@php
	$classes = [ 'lp-header', 'full-width' ];

	if ( true === $dark_mode ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<quark-lp-header @class( $classes )>
	<div class="lp-header__wrap">
		<x-lp-header.logo
			:url="$logo_url"
			:dark_mode="$dark_mode"
		/>
		<x-lp-header.cta
			:image_id="$tc_image_id"
			:phone_number="$phone_number"
			:cta_text="$cta_text"
			:dark_mode="$dark_mode"
		/>
	</div>
</quark-lp-header>
