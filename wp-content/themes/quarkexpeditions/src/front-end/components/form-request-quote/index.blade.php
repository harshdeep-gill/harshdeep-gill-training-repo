@props( [
	'current_tab'       => 'travel-details',
	'update_url'        => 'no',
	'form_id'           => 'form-request-quote',
	'countries'         => [],
	'states'            => [],
	'thank_you_page'    => '',
	'salesforce_object' => '',
] )

@php
	// Tabs.
	quark_enqueue_style( 'tp-tabs' );
	quark_enqueue_script( 'tp-tabs' );

	// Toogle the field.
	wp_enqueue_script( 'tp-toggle-attribute' );
	wp_enqueue_style( 'tp-toggle-attribute' );

	// Number spinner.
	wp_enqueue_script( 'tp-number-spinner' );
	wp_enqueue_style( 'tp-number-spinner' );
@endphp

<x-section class="form-request-quote">
	<quark-form-request-quote class="form-request-quote__container">
		<tp-tabs class="form-request-quote__tabs" current-tab="{{ $current_tab }}" update-url="{{ $update_url }}">
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
							<x-form.select label="{{ __( 'Are you interested in', 'qrk' ) }}">
								<x-form.option value="">{{ __( '- Select -', 'qrk' ) }}</x-form.option>
								<x-form.option value="dreaming" label="{{ __( 'Learning about Polar Travel', 'qrk' ) }}">{{ __( 'Learning about Polar Travel', 'qrk' ) }}</x-form.option>
								<x-form.option value="planning" label="{{ __( 'Planning a Trip', 'qrk' ) }}">{{ __( 'Planning a Trip', 'qrk' ) }}</x-form.option>
								<x-form.option value="booking" label="{{ __( 'Booking a Trip', 'qrk' ) }}">{{ __( 'Booking a Trip', 'qrk' ) }}</x-form.option>
							</x-form.select>
						</x-form.field>

						<x-form.field>
							<x-form.label>{{ __( 'Number of passengers', 'qrk' ) }}</x-form.label>
							<tp-number-spinner class="form-request-quote__number-spinner" min="1" max="10" step="1">
								<tp-number-spinner-decrement>
									<button class="btn" type="button">-</button>
								</tp-number-spinner-decrement>
								<tp-number-spinner-input>
									<input type="text" value="1" readonly />
								</tp-number-spinner-input>
								<tp-number-spinner-increment>
									<button class="btn" type="button">+</button>
								</tp-number-spinner-increment>
							</tp-number-spinner>
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-country-selector :countries="$countries" :states="$states" />
					</x-form.row>

					<x-form.row>
						<x-form.field :validation="[ 'required' ]" class="form-request-quote__toggle">
							<tp-toggle-attribute trigger="select" target=".form-request-quote__travel-time">
								<x-form.select label="{{ __( 'Choose your expedition (optional)', 'qrk' ) }}">
									<x-form.option value="">{{ __( '- None -', 'qrk' ) }}</x-form.option>
									<x-form.option value="115841" label="{{ __( 'Adventures in Northeast Greenland: Glaciers, Fjords and the Northern Lights', 'qrk' ) }}">{{ __( 'Adventures in Northeast Greenland: Glaciers, Fjords and the Northern Lights', 'qrk' ) }}</x-form.option>
									<x-form.option value="125" label="{{ __( 'Antarctic Explorer: Discovering the 7th Continent', 'qrk' ) }}">{{ __( 'Antarctic Explorer: Discovering the 7th Continent', 'qrk' ) }}</x-form.option>
									<x-form.option value="24416" label="{{ __( 'Antarctic Explorer: Discovering the 7th Continent plus Cape Horn & Diego Ramirez', 'qrk' ) }}">{{ __( 'Antarctic Explorer: Discovering the 7th Continent plus Cape Horn & Diego Ramirez', 'qrk' ) }}</x-form.option>
								</x-form.select>
							</tp-toggle-attribute>
						</x-form.field>
					</x-form.row>

					<x-form.row class="form-request-quote__travel-time" data-toggle-value="any_available_departure,november_2024">
						<x-form.field-group title="{{ __( 'When would you like to travel?', 'qrk' ) }}">
							<x-form.checkbox name="fields[]" label="{{ __( 'Any Available Departure', 'qrk' ) }}" value="any_available_departure" checked="checked" />
							<x-form.checkbox name="fields[]" label="{{ __( 'November 2024', 'qrk' ) }}" value="november_2024" />
							<x-form.checkbox name="fields[]" label="{{ __( 'December 2024', 'qrk' ) }}" value="december_2024" />
							<x-form.checkbox name="fields[]" label="{{ __( 'January 2025', 'qrk' ) }}" value="january_2025" />
							<x-form.checkbox name="fields[]" label="{{ __( 'February 2025', 'qrk' ) }}" value="february_2025" />
							<x-form.checkbox name="fields[]" label="{{ __( 'March 2025', 'qrk' ) }}" value="march_2025" />
							<x-form.checkbox name="fields[]" label="{{ __( 'November 2025', 'qrk' ) }}" value="november_2025" />
							<x-form.checkbox name="fields[]" label="{{ __( 'December 2025', 'qrk' ) }}" value="december_2025" />
							<x-form.checkbox name="fields[]" label="{{ __( 'January 2026', 'qrk' ) }}" value="january_2026" />
							<x-form.checkbox name="fields[]" label="{{ __( 'February 2026', 'qrk' ) }}" value="february_2026" />
							<x-form.checkbox name="fields[]" label="{{ __( 'March 2026', 'qrk' ) }}" value="march_2026" />
						</x-form.field-group>
					</x-form.row>

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
							<x-form.input type="text" label="{{ __( 'First Name', 'qrk' ) }}" placeholder="{{ __( 'Enter First Name', 'qrk' ) }}" name="fields[FirstName__c]" />
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input type="text" label="{{ __( 'Last Name', 'qrk' ) }}" placeholder="{{ __( 'Enter Last Name', 'qrk' ) }}" name="fields[LastName__c]" />
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input type="email" label="Email" placeholder="Enter Email" name="fields[Email__c]" />
						</x-form.field>

						<x-form.field :validation="[ 'required' ]">
							<x-form.input type="tel" label="{{ __( 'Phone Number', 'qrk' ) }}" placeholder="eg. (123) 456 7890" name="fields[Phone__c]" />
						</x-form.field>
					</x-form.row>

					<x-form.row>
						<x-form.field-group class="form-request-quote__contact-method" title="{{ __( 'Preferred Contact Method', 'qrk' ) }}">
							<x-form.radio label="{{ __( 'Email', 'qrk' ) }}" name="fields[Preferred_Contact_Method__c]" value="email" />
							<x-form.radio label="{{ __( 'Phone', 'qrk' ) }}" name="fields[Preferred_Contact_Method__c]" value="phone" checked="checked" />
						</x-form.field-group>
					</x-form.row>

					<x-form.row>
						<x-form.field>
							<x-form.textarea label="{{ __( 'What else would you like us to know that is important to you? (optional)', 'qrk' ) }}" placeholder="{{ __( 'Ask us about our adventure options, any dietary requirements, specific departure dates in mind, etc.', 'qrk' ) }}" name="fields[]" />
						</x-form.field>
					</x-form.row>

					<x-form-request-quote.step-two-button />

					<x-form-request-quote.note>
						<p>{{ __( 'We guarantee 100% privacy. Your information will not be shared.', 'qrk' ) }}</p>
					</x-form-request-quote.note>
				</x-form-request-quote.tab>
			</x-form>
		</tp-tabs>
	</quark-form-request-quote>
</x-section>
