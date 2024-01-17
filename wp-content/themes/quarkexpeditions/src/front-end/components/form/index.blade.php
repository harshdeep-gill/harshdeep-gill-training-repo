@props( [
	'form_name'  => '',
	'name'       => '',
	'class'      => '',
	'style'      => '',
	'method'     => 'post',
	'action'     => quark_get_template_data( 'action', '' ),
	'page_title' => '',
	'permalink'  => '',
] )

@php
	$permalink = quark_get_template_data( 'permalink', '' );
	$the_post  = quark_get_template_data( 'post', null );
	$language  = quark_get_template_data( 'language', null );

	if( empty( $the_post ) || ! $the_post instanceof  WP_Post ) {
		return;
	}

	$page_title = $the_post->post_title ?? '';
	$post_id    = $the_post->ID;
@endphp

<quark-form class="form {{ $class }}" data-action="{{ $action }}">
	<tp-form prevent-submit="yes">
		<form
			action="#"
			data-action="{{ $action }}"
			data-name="{{ $name }}"
			method="{{ $method }}"
			novalidate
			@if( ! empty( $style ) )
				style="{{ $style }}"
			@endif
		>
			<input type="hidden" name="fields[device]" value="" class="form__device">
			<input type="hidden" name="fields[device_size]" value="" class="form__device-width">
			<input type="hidden" name="fields[form_type]" value="{!! esc_attr( $form_name ) !!}">
			<input type="hidden" name="fields[http_referrer]" value="" class="form__http-referrer">
			<input type="hidden" name="fields[gclid]" value="" class="form__gclid">
			<input type="hidden" name="fields[msclkid]" value="" class="form__msclkid">
			<input type="hidden" name="fields[caller_title]" value="{!! esc_attr( $page_title ) !!}">
			<input type="hidden" name="fields[caller_url]" value="{{ $permalink }}">
			<input type="hidden" name="fields[caller_id]" value="{{ $post_id }}">
			<input type="hidden" name="fields[language]" value="{{ $language }}" class="form__language">
			<input type="hidden" name="fields[utm_source]" value="" class="form__utm-source">
			<input type="hidden" name="fields[utm_term]" value="" class="form__utm-term">
			<input type="hidden" name="fields[utm_medium]" value="" class="form__utm-medium">
			<input type="hidden" name="fields[utm_campaign]" value="" class="form__utm-campaign">
			<input type="hidden" name="fields[test_id]" value="" class="form__test-id">
			<input type="hidden" name="fields[test_variant]" value="" class="form__test-variant">

			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
