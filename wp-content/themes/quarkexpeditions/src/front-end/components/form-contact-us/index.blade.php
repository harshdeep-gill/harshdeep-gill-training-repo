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
									<x-form.select :label="__( 'Inquiry Type', 'qrk' )" name="fields[Inquiry_Type__c]">
										<x-form.option value="">- Select -</x-form.option>
										<x-form.option value="trip_information" :label="__( 'Trip Information', 'qrk' )">{!! __( 'Trip Information', 'qrk' ) !!}</x-form.option>
										<x-form.option value="new_booking" :label="__( 'A New Booking', 'qrk' )">{!! __( 'A New Booking', 'qrk' ) !!}</x-form.option>
										<x-form.option value="existing_booking" :label="__( 'An Existing Booking', 'qrk' )">{!! __( 'An Existing Booking', 'qrk' ) !!}</x-form.option>
										<x-form.option value="subscription" :label="__( 'Mailing List Subscription', 'qrk' )">{!! __( 'Mailing List Subscription', 'qrk' ) !!}</x-form.option>
										<x-form.option value="general" :label="__( 'Other', 'qrk' )">{!! __( 'Other', 'qrk' ) !!}</x-form.option>
									</x-form.select>
								</x-form.field>
							</tp-toggle-attribute>
						</x-form.row>

					<x-form.row>
						<tp-toggle-attribute trigger="select" target=".form-contact-us__existing-booking-agency-info" attribute="required">
							<x-form.field class="form-contact-us__existing-booking-info">
								<x-form.select :label="__( 'How did you book?', 'qrk' )" name="fields[Booking_Type__c]">
									<x-form.option value="">{!! __( '- Select -', 'qrk' ) !!}</x-form.option>
									<x-form.option value="direct" :label="__( 'I booked with Quark Expeditions', 'qrk' )">{!! __( 'I booked with Quark Expeditions', 'qrk' ) !!}</x-form.option>
									<x-form.option value="agent" :label="__( 'I booked with a Travel Agent or Third Party', 'qrk' )">{!! __( 'I booked with a Travel Agent or Third Party', 'qrk' ) !!}</x-form.option>
								</x-form.select>
							</x-form.field>
						</tp-toggle-attribute>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="direct,agent">
							<x-form.input type="text" :label="__( 'Reservation Number', 'qrk' )" :placeholder="__( 'Enter Reservation Number', 'qrk' )" name="fields[Reservation_Number__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="agent">
							<x-form.input type="text" :label="__( 'Agency Name', 'qrk' )" :placeholder="__( 'Enter Agency Name', 'qrk' )" name="fields[Agency_Name__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'no-empty-spaces' ]" class="form-contact-us__existing-booking-agency-info" data-toggle-value="agent">
							<x-form.input type="text" :label="__( 'Agent Name', 'qrk' )" :placeholder="__( 'Enter Agent Name', 'qrk' )" name="fields[Agency_Contact_Name__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" :label="__( 'First Name', 'qrk' )" :placeholder="__( 'Enter First Name', 'qrk' )" name="fields[FirstName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" :label="__( 'Last Name', 'qrk' )" :placeholder="__( 'Enter Last Name', 'qrk' )" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" :label="__( 'Email Address', 'qrk' )" :placeholder="__( 'Enter Email Address', 'qrk' )" name="fields[Email__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="tel" :label="__( 'Phone Number', 'qrk' )" :placeholder="__( 'Enter Phone Number', 'qrk' )" name="fields[Phone__c]" />
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
							<x-form.textarea :label="__( 'Comments', 'qrk' )" :placeholder="__( 'Enter your comments', 'qrk' )" name="fields[Comments__c]" />
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field-group>
							<x-form.checkbox name="fields[Subscribe_to_Newsletter__c]" :label="__( 'I would like to receive news and promotions from Quark Expeditions.', 'qrk' )" />
						</x-form.field-group>
					</x-form.row>
					<x-form.row>
						<x-form.field-group :validation="[ 'checkbox-field-required' ]" class="form-contact-us__privacy-statement">
							<x-form.checkbox name="fields[Privacy_Policy_Agreement__c]" :label="__( 'I have read and agree to the privacy statement.', 'qrk' )" />
						</x-form.field-group>
					</x-form.row>
				</div>
			</div>

			<x-form.buttons>
				<x-form.submit size="big" color="black">{!! __( 'Submit', 'qrk') !!}</x-form.submit>
			</x-form.buttons>
		</x-form>
	</quark-form-contact-us>
</x-section>
