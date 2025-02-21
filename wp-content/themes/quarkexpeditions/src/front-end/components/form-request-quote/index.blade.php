@props( [
	'current_tab'       => 'travel-details',
	'update_url'        => 'no',
	'form_id'           => 'form-request-quote',
	'expeditions'       => [],
	'filters_endpoint'  => '',
	'countries'         => [],
	'states'            => [],
	'thank_you_page'    => '',
	'salesforce_object' => 'WebForm_RAQ__c',
	'home_url'          => '',
] )

@php
	// Tabs.
	quark_enqueue_style( 'tp-tabs' );
	quark_enqueue_script( 'tp-tabs' );

	// Toogle the field.
	wp_enqueue_script( 'tp-toggle-attribute' );
	wp_enqueue_style( 'tp-toggle-attribute' );
@endphp

<x-section class="form-request-quote">
	<quark-form-request-quote class="form-request-quote__container" data-filters-endpoint="{{ $filters_endpoint }}">
		<tp-tabs class="form-request-quote__tabs" current-tab="{{ $current_tab }}" update-url="{{ $update_url }}">
			{{-- Title --}}
			<h1 class="form-request-quote__heading">{{ __( 'Request a Quote', 'qrk' ) }}</h1>

			{{-- Toast Message --}}
			<x-toast-message type="error" message="{{ __( 'Fields marked with an asterisk (*) are required', 'qrk' ) }}" />

			{{-- Naviation --}}
			<x-form-request-quote.tabs-nav>
				<x-form-request-quote.tabs-nav-item id="travel-details">
					<span class="form-request-quote__step-count">1</span>
					<span class="form-request-quote__step-title">{{ __( 'Travel Details', 'qrk' ) }}</span>
				</x-form-request-quote.tabs-nav-item>

				<x-form-request-quote.tabs-nav-item id="contact-details">
					<span class="form-request-quote__step-count">2</span>
					<span class="form-request-quote__step-title">{{ __( 'Contact Details', 'qrk' ) }}</span>
				</x-form-request-quote.tabs-nav-item>
			</x-form-request-quote.tabs-nav>

			{{-- Content --}}
			<x-form
				salesforce_object="{{ $salesforce_object }}"
				id="{{ $form_id }}"
				thank_you_page="{{ $thank_you_page }}"
				class="form-request-quote__form"
			>
				{{-- Step 1 --}}
				<x-form-request-quote.tab id="travel-details" class="form-request-quote__step-1" open="yes">
					<x-form-request-quote.title title="{{ __( 'Travel Details', 'qrk' ) }}" />

					<x-form.row>
						<x-form.field :validation="[ 'required' ]">
							<x-form.label id="journey_stage">
								{{ __( 'Are you interested in', 'qrk' ) }}
							</x-form.label>
							<select name="fields[Journey_Stage__c]" class="form-request-quote__journey-stage">
								<option value="">{{ __( '- Select -', 'qrk' ) }}</option>
								<option value="booking">{{ __( 'Booking a Trip', 'qrk' ) }}</option>
								<option value="planning">{{ __( 'Planning a Trip', 'qrk' ) }}</option>
								<option value="dreaming">{{ __( 'Learning about Polar Travel', 'qrk' ) }}</option>
							</select>
						</x-form.field>

						<x-form.field>
							<x-number-spinner
								label="{{ __( 'Number of passengers', 'qrk' ) }}"
								min="1"
								max="10"
								step="1"
								name="fields[PAX_Count__c]"
							/>
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field class="form-request-quote__toggle">
							<tp-toggle-attribute trigger="select" target=".form-request-quote__travel-time">
								<x-form.label id="expeditions">
									{{ __( 'Choose your expedition', 'qrk' ) }}
									<span class="form__label-optional-text">{{ __( '(optional)', 'qrk' ) }}</span>
								</x-form.label>
								<select name="fields[Expedition__c]" class="form-request-quote__expedition">
									<option value="">{{ __( '- None -', 'qrk' ) }}</option>
									@foreach ( $expeditions as $expedition )
										<option value="{{ $expedition['value'] }}">{{ $expedition['label'] }}</option>
									@endforeach
								</select>
							</tp-toggle-attribute>
						</x-form.field>
					</x-form.row>

					<x-form.row class="form-request-quote__travel-time" data-toggle-value="any_available_departure">
						<x-form.field-group title="{{ __( 'When would you like to travel?', 'qrk' ) }}" class="form-request-quote__options">
							<x-form.checkbox name="fields[Preferred_Travel_Seasons__c][]" label="{{ __( 'Any Available Departure', 'qrk' ) }}" value="any_available_departure" checked="checked" />
						</x-form.field-group>
					</x-form.row>

					<template class="form-request-quote__template-month-option"><x-form.checkbox name="fields[Preferred_Travel_Seasons__c][]" /></template>

					<x-form-request-quote.step-one-button />

					<x-form-request-quote.note>
						<p>{{ __( 'We guarantee 100% privacy. Your information will not be shared.', 'qrk' ) }}</p>
					</x-form-request-quote.note>
				</x-form-request-quote.tab>

				{{-- Step 2 --}}
				<x-form-request-quote.tab id="contact-details"  class="form-request-quote__step-2">
					<x-form-request-quote.title title="{{ __( 'Contact Details', 'qrk' ) }}" />

					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" maxlength="40" label="{{ __( 'First Name', 'qrk' ) }}" placeholder="{{ __( 'Enter First Name', 'qrk' ) }}" name="fields[FirstName__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" maxlength="80" label="{{ __( 'Last Name', 'qrk' ) }}" placeholder="{{ __( 'Enter Last Name', 'qrk' ) }}" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-country-selector :countries="$countries" :states="$states" :enable_name_fields="true"/>
					</x-form.row>

					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" label="Email" placeholder="{{ __( 'Enter Email', 'qrk' ) }}" name="fields[Email__c]" />
						</x-form.field>

						<x-form.field :validation="[ 'required', 'phone-field-validation' ]">
							<x-form.input type="tel" maxlength="20" label="{{ __( 'Phone Number', 'qrk' ) }}" placeholder="{{ __( 'eg. (123) 456 7890', 'qrk' ) }}" name="fields[Phone__c]" />
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field-group class="form-request-quote__contact-method" title="{{ __( 'Preferred Contact Method', 'qrk' ) }}">
							<x-form.radio label="{{ __( 'Email', 'qrk' ) }}" name="fields[Preferred_Contact_Methods__c]" value="email" />
							<x-form.radio label="{{ __( 'Phone', 'qrk' ) }}" name="fields[Preferred_Contact_Methods__c]" value="phone" checked="checked" />
						</x-form.field-group>
					</x-form.row>

					<x-form.row>
						<x-form.field>
							<x-form.textarea label="{{ __( 'What else would you like us to know that is important to you?', 'qrk' ) }}" :optional="true" placeholder="{{ __( 'Ask us about our adventure options, any dietary requirements, specific departure dates in mind, etc.', 'qrk' ) }}" name="fields[Comments__c]" />
						</x-form.field>
					</x-form.row>

					<x-form-request-quote.step-two-button />

					<x-form-request-quote.note>
						<p>{{ __( 'We guarantee 100% privacy. Your information will not be shared.', 'qrk' ) }}</p>
					</x-form-request-quote.note>
				</x-form-request-quote.tab>
			</x-form>
		</tp-tabs>

		<x-section id="form-request-quote__success" class="form-request-quote__success">
			<div class="form-request-quote__success-header">
				<x-svg name="circular-tick" />
				<h2 class="h1">{{ __( 'Request Submitted!', 'qrk' ) }}</h2>
			</div>
			<p class="form-request-quote__success-content">{{ __( 'One of our Polar Travel Advisors will be in touch. You’re one step closer to making your polar dream a reality!', 'qrk' ) }}</p>

			@if ( ! empty( $home_url ) )
				<div class="form-request-quote__success-button-wrap">
					<x-button href="{{ $home_url }}" size="big" class="form-request-quote__back-to-home">{{ __( 'Back to Home', 'qrk' ) }}</x-button>
				</div>
			@endif
		</x-section>
	</quark-form-request-quote>
</x-section>
