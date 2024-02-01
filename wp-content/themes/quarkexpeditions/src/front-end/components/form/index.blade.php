@props( [
	'form_name'         => '',
	'name'              => '',
	'id'                => '',
	'class'             => '',
	'style'             => '',
	'method'            => 'post',
	'action'            => quark_get_template_data( 'leads_api_endpoint', '#' ),
	'salesforce_object' => '',
	'recaptcha'         => true,
	'permalink'         => quark_get_template_data( 'current_url', '#' ),
] )

<quark-form
	class="form {{ $class }}"
	@if ( ! empty( $style ) )
		style="{{ $style }}"
	@endif
>
	<tp-form prevent-submit="yes">
		<form
			action="#"
			data-action="{{ $action }}"
			data-name="{{ $name }}"
			method="{{ $method }}"
			novalidate
			@if ( ! empty( $id ) )
				id="{{ $id }}"
			@endif
		>
			<input type="hidden" name="salesforce_object" value="{{ $salesforce_object }}">
			<input type="hidden" name="fields[Webform_URL__c]" value="{{ $permalink }}">

			<!-- URL params may be stripped, or page may be cached, so they are added via JS: https://docs.pantheon.io/pantheon_stripped -->
			<input type="hidden" name="fields[UTM_Campaign__c]" value="" class="form__utm-campaign">
			<input type="hidden" name="fields[UTM_Content__c]" value="" class="form__utm-content">
			<input type="hidden" name="fields[UTM_Medium__c]" value="" class="form__utm-medium">
			<input type="hidden" name="fields[UTM_Source__c]" value="" class="form__utm-source">
			<input type="hidden" name="fields[UTM_Term__c]" value="" class="form__utm-term">
			<input type="hidden" name="fields[GCLID__c]" value="" class="form__gclid">
			<input type="hidden" name="fields[FBBID__c]" value="" class="form__fbid">
			<input type="hidden" name="fields[FBCLID__c]" value="" class="form__fbclid">
			<input type="hidden" name="fields[MSCLID__c]" value="" class="form__msclkid">

			@if ( true === $recaptcha )
				<input type="hidden" name="recaptcha_token" value="" />
			@endif

			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
