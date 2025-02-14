@props( [
	'form_id'        => '',
	'class'          => '',
	'states'         => [],
	'thank_you_page' => '',
] )

@php
	$classes = [ 'form-account-management' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	wp_enqueue_script( 'tp-toggle-attribute' );
	wp_enqueue_style( 'tp-toggle-attribute' );
@endphp

<x-form
	salesforce_object="Webform_CCPA_Deletion_Request__c"
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	@class( $classes )
>
	<div class="form-account-management__content">
		<div class="form-account-management__form">
			<h3 class="form-account-management__title">{{ __( 'CCPA Access/Deletion Request', 'qrk' ) }}</h3>

			<x-form.row>
				<tp-toggle-attribute trigger="select" target=".form-contact-us__request-type" value="request_type" attribute="required">
					<x-form.field :validation="[ 'required' ]">
						<x-form.select :label="__( 'Request Type', 'qrk' )" name="fields[Request_Type__c]">
							<x-form.option value="">{{ __( '- Select -', 'qrk' ) }}</x-form.option>
							<x-form.option value="my_information_request" label="{{ __( 'Inform me about what categories of personal information you collect about me', 'qrk' ) }}">{{ __( 'Inform me about what categories of personal information you collect about me', 'qrk' ) }}</x-form.option>
							<x-form.option value="access_request" label="{{ __( 'Give me access to the specific pieces of personal information you store about me', 'qrk' ) }}">{{ __( 'Give me access to the specific pieces of personal information you store about me', 'qrk' ) }}</x-form.option>
							<x-form.option value="delete_information_request" label="{{ __( 'Delete the personal information you store about me', 'qrk' ) }}">{{ __( 'Delete the personal information you store about me', 'qrk' ) }}</x-form.option>
						</x-form.select>
					</x-form.field>
				</tp-toggle-attribute>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" label="{{ __( 'First Name', 'qrk' ) }}" placeholder="{{ __( 'Enter First Name', 'qrk' ) }}" name="fields[First_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
					<x-form.input type="text" label="{{ __( 'Last Name', 'qrk' ) }}" placeholder="{{ __( 'Enter Last Name', 'qrk' ) }}" name="fields[Last_Name__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'email' ]">
					<x-form.input type="email" label="{{ __( 'Email', 'qrk' ) }}" placeholder="{{ __( 'Enter Email', 'qrk' ) }}" name="fields[Email_Address__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required' ]">
					<x-form.input type="tel" label="{{ __( 'Phone Number', 'qrk' ) }}" placeholder="{{ __( 'eg. (123) 456 7890', 'qrk' ) }}" name="fields[Phone_Number__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__address">
					<x-form.input type="text" label="{{ __( 'Address #1', 'qrk' ) }}" placeholder="{{ __( 'Enter Address', 'qrk' ) }}" name="fields[Address1__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-account-management__address">
					<x-form.input type="text" label="{{ __( 'Address #2', 'qrk' ) }}" placeholder="{{ __( 'Enter Address', 'qrk' ) }}" name="fields[Address2__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__city">
					<x-form.input type="text" label="{{ __( 'City', 'qrk' ) }}" placeholder="{{ __( 'Enter City Name', 'qrk' ) }}" name="fields[City__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__state-of-residency">
					<x-state-selector :states="$states" state_code_key="State_of_Residency__c" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__zip">
					<x-form.input type="text" label="{{ __( 'ZIP', 'qrk' ) }}" placeholder="{{ __( 'Enter ZIP', 'qrk' ) }}" name="fields[ZIP__c]" />
				</x-form.field>
			</x-form.row>
			<x-form.row>
				<x-form.field-group>
					<x-form.checkbox name="fields[Opt_out__c]" label="{{ __( 'I wish to opt-out of the distribution of my information as defined under the CCPA.', 'qrk' ) }}" />
				</x-form.field-group>
			</x-form.row>
			<x-form.row>
				<x-form.field-group :validation="[ 'checkbox-group-required' ]">
					<x-form.checkbox name="fields[Verify_Request__c]" label="{{ __( 'Verify Request.', 'qrk' ) }}" />
				</x-form.field-group>
			</x-form.row>
			<small>
				<div>
					<ul>
						<li class="form-account-management__verify-description">{{ __( 'I verify that I am at least 13 years of age.', 'qrk' ) }}</li>
						<li class="form-account-management__verify-description">{{ __( 'I declare under penalty of perjury that the information in this form is true and correct and that I am either (i) the consumer identified in this request, or (ii) authorized to make this request on behalf of the consumer', 'qrk' ) }}</li>
					</ul>
				</div>
			</small>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">{{ __( 'Submit', 'qrk' ) }}</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
