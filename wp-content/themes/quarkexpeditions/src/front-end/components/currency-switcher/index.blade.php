@props( [
	'appearance' => '',
] )

@php
	$classes = [ 'currency-switcher' ];

	if ( 'dark' === $appearance ) {
		$classes[] = 'currency-switcher--dark';
	}
@endphp

<quark-currency-switcher @class( $classes )>
	<x-form.field>
		<x-form.select label="{{ __( 'Change Currency', 'qrk' ) }}">
			<x-form.option value="test" label="Ask us about our adventure options any dietary requirements specific departure dates in mind">Ask us about our adventure options, any dietary requirements, specific departure dates in mind, etc.</x-form.option>
			<x-form.option value="USD" label="USD">USD</x-form.option>
			<x-form.option value="CAD" label="CAD">CAD</x-form.option>
			<x-form.option value="AUD" label="AUD">AUD</x-form.option>
			<x-form.option value="GBP" label="GBP">GBP</x-form.option>
			<x-form.option value="EUR" label="EUR">EUR</x-form.option>
		</x-form.select>
	</x-form.field>
</quark-currency-switcher>
