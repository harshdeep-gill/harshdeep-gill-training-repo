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
	'thank_you_page'    => '',
	'marketing_fields'  => true,
	'webform_url'       => true,
	'referrer_url'      => false,
	'ga_client_id'      => true,
] )

<quark-form
	class="form {{ $class }}"
	@if ( ! empty( $style ) )
		style="{{ $style }}"
	@endif
	@if ( ! empty( $thank_you_page ) )
		thank-you-url="{{ $thank_you_page }}"
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

			@if ( true === $webform_url )
				<input type="hidden" name="fields[Webform_URL__c]" value="{{ $permalink }}">
			@endif

			@if ( true === $referrer_url )
				<input type="hidden" name="fields[Referrer_URL__c]" value="{{ $permalink }}">
			@endif

			@if ( true === $marketing_fields )
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
				<input type="hidden" name="fields[PCLID__c]" value="" class="form__pclid">
			@endif

			@if ( true === $ga_client_id )
				<input type="hidden" name="fields[GA_Client_ID__c]" value="" class="form__ga-client">
			@endif

			@if ( true === $recaptcha )
				<input type="hidden" name="recaptcha_token" value="" />
			@endif

			{{ $slot }}

			<x-form.field class="form__confirm-phone">
				<x-form.input type="text" label="{{ __( 'Confirm Phone', 'qrk' ) }}" placeholder="{{ __( 'Confirm Phone', 'qrk' ) }}" name="fields[confirm_phone]" />
			</x-form.field>
			<x-form.field class="form__confirm-email">
				<x-form.input type="email" label="{{ __( 'Confirm Email', 'qrk' ) }}" placeholder="{{ __( 'Confirm Email', 'qrk' ) }}" name="fields[confirm_email]" />
			</x-form.field>
		</form>
	</tp-form>
</quark-form>
