@props( [
	'association_links' => [],
] )

@php
	if ( ! is_array( $association_links ) || empty( $association_links ) ) {
		$association_links = quark_get_template_data( 'association_links' );
	}
@endphp

<ul class="footer__associations">
	@if ( ! empty( $association_links['iaato'] ) )
		<li class="footer__association">
			<a href="{{ $association_links['iaato'] }}" target="_blank" rel="nofollow noopener noreferrer" title="iaato"><x-svg name="association/iaato" /></a>
		</li>
	@endif
	@if ( ! empty( $association_links['aeco'] ) )
		<li class="footer__association">
			<a href="{{ $association_links['aeco'] }}" target="_blank" rel="nofollow noopener noreferrer" title="aeco"><x-svg name="association/aeco" /></a>
		</li>
	@endif
</ul>
