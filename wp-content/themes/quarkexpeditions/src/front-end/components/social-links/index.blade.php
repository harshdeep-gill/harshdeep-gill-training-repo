@props( [
	'social_links' => [],
] )

@php
	if ( ! is_array( $social_links ) || empty( $social_links ) ) {
		$social_links = tcs_get_template_data( 'social_links' );
	}
@endphp

<ul class="social-icons">
	@if ( ! empty( $social_links['facebook'] ) )
		<li><a href="{{ $social_links['facebook'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__facebook" title="facebook"><x-svg name="social/facebook" /></a></li>
	@endif
	@if ( ! empty( $social_links['instagram'] ) )
		<li><a href="{{ $social_links['instagram'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__instagram" title="instagram"><x-svg name="social/instagram" /></a></li>
	@endif
	@if ( ! empty( $social_links['twitter'] ) )
		<li><a href="{{ $social_links['twitter'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__twitter" title="twitter"><x-svg name="social/twitter" /></a></li>
	@endif
	@if ( ! empty( $social_links['youtube'] ) )
		<li><a href="{{ $social_links['youtube'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__youtube" title="youtube"><x-svg name="social/youtube" /></a></li>
	@endif
	@if ( ! empty( $social_links['pinterest'] ) )
		<li><a href="{{ $social_links['pinterest'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__pinterest" title="pinterest"><x-svg name="social/pinterest" /></a></li>
	@endif
	@if ( ! empty( $social_links['email'] ) )
		<li><a href="mailto:{{ $social_links['email'] }}" target="_blank" rel="nofollow noopener noreferrer" class="social-icons__email" title="email"><x-svg name="social/email" /></a></li>
	@endif
</ul>
