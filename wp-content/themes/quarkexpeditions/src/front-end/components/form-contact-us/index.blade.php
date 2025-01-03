@props( [
	'form_id'        => '',
	'class'          => '',
	'countries'      => [],
	'states'         => [],
	'thank_you_page' => '',
] )

@php
	$classes = [ 'form-contact-us' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	wp_enqueue_script( 'tp-toggle-attribute' );
	wp_enqueue_style( 'tp-toggle-attribute' );
@endphp

<x-section @class( $classes )>
	<quark-form-contact-us>
		<x-form
			salesforce_object="Webform_Contact_Us__c"
			id="{{ $form_id }}"
			thank_you_page="{{ $thank_you_page }}"
			:webform_url="false"
			:referrer_url="true"
		>
			<div class="form-contact-us__content">
				<div class="form-contact-us__form">
					<p class="form-contact-us__instructions">
						{!!
							esc_html__(
								'Fill the form below, and we will get back to you on the same or next business day.',
								'qrk'
							)
						!!}
					</p>

					<div class="form-contact-us__fields">
						<x-form.row>
							<tp-toggle-attribute trigger="select" target=".form-contact-us__existing-booking-info" value="existing_booking" attribute="required" class="form-contact-us__inquiry-type">
								<x-form.field :validation="[ 'required' ]">
									<x-form.select label="Inquiry Type" name="fields[Inquiry_Type__c]">
										<x-form.option value="">- Select -</x-form.option>
										<x-form.option value="trip_information" label="Trip Information">Trip Information</x-form.option>
										<x-form.option value="new_booking" label="A New Booking">A New Booking</x-form.option>
										<x-form.option value="existing_booking" label="An Existing Booking">An Existing Booking</x-form.option>
										<x-form.option value="subscription" label="Mailing List Subscription">Mailing List Subscription</x-form.option>
										<x-form.option value="general" label="Other">Other</x-form.option>
									</x-form.select>
								</x-form.field>
							</tp-toggle-attribute>
						</x-form.row>

					<x-form.row>
						<tp-toggle-attribute trigger="select" target=".form-contact-us__existing-booking-agency-info" attribute="required">
							<x-form.field class="form-contact-us__existing-booking-info">
								<x-form.select label="How did you book?" name="fields[Booking_Type__c]">
									<x-form.option value="">- Select -</x-form.option>
									<x-form.option value="direct" label="I booked with Quark Expeditions">I booked with Quark Expeditions</x-form.option>
									<x-form.option value="agent" label="I booked with a Travel Agent or Third Party">I booked with a Travel Agent or Third Party</x-form.option>
								</x-form.select>
							</x-form.field>
						</tp-toggle-attribute>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="direct,agent">
							<x-form.input type="text" label="Reservation Number" placeholder="Enter Reservation Number" name="fields[Reservation_Number__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="agent">
							<x-form.input type="text" label="Agency Name" placeholder="Enter Agency Name" name="fields[Agency_Name__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="agent">
							<x-form.input type="text" label="Agent Name" placeholder="Enter Agent Name" name="fields[Agency_Contact_Name__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[FirstName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" label="Last Name" placeholder="Enter Last Name" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" label="Email Address" placeholder="Enter Email Address" name="fields[Email__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="tel" label="Phone Number" placeholder="Enter Phone Number" name="fields[Phone__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-country-selector
							country_label="Country of Residence"
							:countries="$countries"
							:states="$states"
							:enable_name_fields="true"
							state_key="State_Province__c"
						/>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.textarea label="Comments" placeholder="Enter your comments" name="fields[Comments__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field-group>
							<x-form.checkbox name="fields[Subscribe_to_Newsletter__c]" label="I would like to receive news and promotions from Quark Expeditions." />
						</x-form.field-group>
					</x-form.row>
					<x-form.row>
						<x-form.field-group :validation="[ 'checkbox-field-required' ]" class="form-contact-us__privacy-statement">
							<x-form.checkbox name="fields[Privacy_Policy_Agreement__c]" label="I have read and agree to the privacy statement." />
						</x-form.field-group>
					</x-form.row>
				</div>
			</div>

			<x-form.buttons>
				<x-form.submit size="big" color="black">Submit</x-form.submit>
			</x-form.buttons>
		</x-form>
	</quark-form-contact-us>
</x-section>
