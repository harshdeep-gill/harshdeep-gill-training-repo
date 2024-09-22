@props( [
	'form_id'              => '',
	'class'                => '',
	'thank_you_page'       => '',
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
	salesforce_object=""
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	@class( $classes )
>
	<div class="form-account-management__content">
		<div class="form-account-management__form">
			<p class="form-account-management__instructions">
				{!!
					esc_html__(
						'Fill the form below, and we will get back to you on the same or next business day.',
						'qrk'
					)
				!!}
			</p>

			<div class="form-account-management__fields">
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[First_Name__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
						<x-form.input type="text" label="Last Name" placeholder="Enter Last Name" name="fields[Last_Name__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'email' ]">
						<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email_Address__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="tel" label="Phone Number" placeholder="eg. (123) 456 7890" name="fields[Phone_Number__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__address">
						<x-form.input type="text" label="Address 1" placeholder="Enter Addresss" name="fields[Address1__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__address">
						<x-form.input type="text" label="Address 2" placeholder="Enter Addresss" name="fields[Address2__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__city">
						<x-form.input type="text" label="City" placeholder="Enter City Name" name="fields[City__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]" class="form-account-management__state-of-residency">
						<x-form.input type="text" label="State of Residency" placeholder="Enter State of Residency" name="fields[State_of_Residency__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<tp-toggle-attribute trigger="select" target=".form-contact-us__request-type" value="requiest_type" attribute="required">
						<x-form.field :validation="[ 'required' ]">
							<x-form.select label="Request Type" name="fields[Request_Type__c]">
								<x-form.option value="">- Select -</x-form.option>
								<x-form.option value="my_information_request" label="My Information">My Information</x-form.option>
								<x-form.option value="access_request" label="Access Request">Access Request</x-form.option>
								<x-form.option value="delete_information_request" label="Delete Information">Delete Information</x-form.option>
							</x-form.select>
						</x-form.field>
					</tp-toggle-attribute>
				</x-form.row>
				<x-form.row>
					<x-form.field-group :validation="[ 'checkbox-group-required' ]">
						<x-form.checkbox name="fields[Opt_out__c]" label="Opt-out." />
					</x-form.field-group>
				</x-form.row>
				<x-form.row>
					<x-form.field-group :validation="[ 'checkbox-group-required' ]">
						<x-form.checkbox name="fields[Verify_Request__c]" label="Verify Request." />
					</x-form.field-group>
				</x-form.row>
				{!! $slot !!}
			</div>
		</div>

		<x-form.buttons>
			<x-form.submit size="big" color="black">Submit</x-form.submit>
		</x-form.buttons>
	</div>
</x-form>
