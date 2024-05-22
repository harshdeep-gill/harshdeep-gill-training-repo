@props( [
	'social_links' => [],
] )

@php
	if ( ! is_array( $social_links ) || empty( $social_links ) ) {
		$social_links = tcs_get_template_data( 'social_links' );
	}
@endphp

<ul class="footer__social-icons">
	@if ( ! empty( $social_links['facebook'] ) )
		<li><a href="{{ $social_links['facebook'] }}" target="_blank" rel="nofollow noopener noreferrer" class="footer__social-icons-facebook" title="facebook"><x-svg name="social/facebook" /></a></li>
	@endif
	@if ( ! empty( $social_links['instagram'] ) )
		<li><a href="{{ $social_links['instagram'] }}" target="_blank" rel="nofollow noopener noreferrer" class="footer__social-icons-instagram" title="instagram"><x-svg name="social/instagram" /></a></li>
	@endif
	@if ( ! empty( $social_links['twitter'] ) )
		<li><a href="{{ $social_links['twitter'] }}" target="_blank" rel="nofollow noopener noreferrer" class="footer__social-icons-twitter" title="twitter"><x-svg name="social/twitter" /></a></li>
	@endif
	@if ( ! empty( $social_links['youtube'] ) )
		<li><a href="{{ $social_links['youtube'] }}" target="_blank" rel="nofollow noopener noreferrer" class="footer__social-icons-youtube" title="youtube"><x-svg name="social/youtube" /></a></li>
	@endif
</ul>
