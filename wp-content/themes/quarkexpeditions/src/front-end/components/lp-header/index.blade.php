@props( [
	'logo_url'     => '',
	'tc_image_id'  => 0,
	'phone_number' => '',
	'cta_text'     => '',
] )

<quark-lp-header class="lp-header full-width">
	<div class="lp-header__wrap">
		<x-lp-header.logo :url="$logo_url" />
		<x-lp-header.cta
			:image_id="$tc_image_id"
			:phone_number="$phone_number"
			:cta_text="$cta_text"
		/>
	</div>
</quark-lp-header>
