@props( [
	'drawer_id'    => '',
	'accordion_id' => '',
] )

<quark-dates-rates-filter-sticky-filter>
	<x-dates-rates.filters.chip :drawer_id="$drawer_id" title="{{ __( 'Filter', 'qrk' ) }}" :accordion_id="$accordion_id" />
</quark-dates-rates-filter-sticky-filter>
