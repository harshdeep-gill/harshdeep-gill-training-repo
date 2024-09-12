@props( [
	'class' => '',
] )

<quark-dates-rates-pagination-items-per-page class="{!! esc_attr( $class ) !!}" >
	<x-pagination.items-per-page
		:pagination_steps="[ 8, 12, 24, 32, 48, 64 ]"
		:select_first="false"
	/>
</quark-dates-rates-pagination-items-per-page>
