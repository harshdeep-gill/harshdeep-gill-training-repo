@props( [
	'form_id'                   => 'job-application-form',
	'class'                     => '',
	'countries'                 => [],
	'states'                    => [],
	'thank_you_page'            => '',
] )

@php
	$classes = [ 'form-job-application' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$resume_allowed_file_types = [ '.pdf' ];

	wp_enqueue_script( 'tp-toggle-attribute' );
	wp_enqueue_style( 'tp-toggle-attribute' );
@endphp

<x-form
	salesforce_object="WebForm_Job_Application__c"
	id="{{ $form_id }}"
	thank_you_page="{{ $thank_you_page }}"
	:marketing_fields="false"
	:webform_url="false"
	ga_client_id=false
	@class( $classes )
>
	<div class="form-job-application__content">
		<div class="form-job-application__form">
			<div class="form-job-application__section">
				<h3 class="form-job-application__title">{{ __( 'Quark Expeditions Department', 'qrk' ) }}</h3>
				<x-form.row>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="{{ __( 'Job Type', 'qrk' ) }}">
							<x-form.option value="">{{ __( '- Select -', 'qrk' ) }}</x-form.option>
							<x-form.option value="careers_expedition" label="{{ __( 'Careers - Expedition Guides and Education Team', 'qrk' ) }}">{{ __( 'Careers - Expedition Guides and Education Team', 'qrk' ) }}</x-form.option>
						</x-form.select>
					</x-form.field>
				</x-form.row>
			</div>

			<div class="form-job-application__section">
				<h3 class="form-job-application__title">{{ __( 'Contact Information', 'qrk' ) }}</h3>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
						<x-form.input type="text" label="{{ __( 'First Name', 'qrk' ) }}" name="fields[FirstName__c]" placeholder="Enter First Name" />
					</x-form.field>
					<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
						<x-form.input type="text" label="{{ __( 'Last Name', 'qrk' ) }}" name="fields[LastName__c]" placeholder="Enter Last Name" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required', 'email' ]">
						<x-form.input type="email" label="{{ __( 'Email Address', 'qrk' ) }}" placeholder="{{ __( 'Enter Email Address', 'qrk' ) }}" name="fields[Email__c]" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="tel" label="{{ __( 'Phone Number', 'qrk' ) }}" placeholder="{{ __( 'Enter Phone Number', 'qrk' ) }}" name="fields[Phone__c]" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="{{ __( 'Address 1', 'qrk' ) }}" name="fields[Address1__c]" placeholder="Enter Address 1" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field>
						<x-form.input type="text" label="{{ __( 'Address 2', 'qrk' ) }}" name="fields[Address2__c]" placeholder="Enter Address 2" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="{{ __( 'Postal Code', 'qrk' ) }}" name="fields[Postal_Code__c]" placeholder="Enter Postal Code" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="{{ __( 'City', 'qrk' ) }}" name="fields[City__c]" placeholder="Enter City" />
					</x-form.field>
				</x-form.row>
				<x-form.row>
					<x-form.field :validation="[ 'required' ]">
						<x-country-selector :countries="$countries" :states="$states" :state_code_key="'State_Province__c'" />
					</x-form.field>
				</x-form.row>
			</div>

			<div class="form-job-application__section">
				<h3 class="form-job-application__title">{{ __( 'Expedition Team Application Questions', 'qrk' ) }}</h3>
				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'Have you sailed with Quarks Expeditions as a passenger?', 'qrk' ) }}" :validation="[ 'radio-group-required' ]" class="form-job-application__toggle">
							<x-form.radio label="{{ __( 'Yes', 'qrk' ) }}" name="fields[Was_a_Passenger__c]" value="Yes" />
							<x-form.radio label="{{ __( 'No', 'qrk' ) }}" name="fields[Was_a_Passenger__c]" value="No" />
						</x-form.field-group>
					</x-form.row>
				</div>

				<div class="form-job-application__sub-section form-job-application--expand-fields">
					<x-form.row>
						<tp-toggle-attribute trigger="input[type='radio']" target=".form-job-application__experience" value="Yes" attribute="required">
							<x-form.field-group title="{{ __( 'Do you have experience working on a cruise line or expedition vessel?', 'qrk' ) }}" :validation="[ 'radio-group-required' ]" class="form-job-application__toggle">
								<x-form.radio label="{{ __( 'Yes', 'qrk' ) }}" name="fields[Has_Worked_on_Cruise_Line_or_Vessel__c]" value="Yes" />
								<x-form.radio label="{{ __( 'No', 'qrk' ) }}" name="fields[Has_Worked_on_Cruise_Line_or_Vessel__c]" value="No" />
							</x-form.field-group>
						</tp-toggle-attribute>
					</x-form.row>
					<x-form.field class="form-job-application__experience">
						<x-form.textarea label="{{ __( 'Describe your experience, your role and what company you worked for.', 'qrk' ) }}" name="fields[Experience_with_Cruise_Line_or_Vessel__c]" placeholder="Describe" />
					</x-form.field>
				</div>

				<div class="form-job-application__sub-section form-job-application--expand-fields">
					<x-form.row>
						<tp-toggle-attribute trigger="input[type='radio']" target=".form-job-application__polar-experience" value="Yes" attribute="required">
							<x-form.field-group title="{{ __( 'Have you worked in either of the Polar Regions before?', 'qrk' ) }}" :validation="[ 'radio-group-required' ]" class="form-job-application__toggle">
								<x-form.radio label="{{ __( 'Yes', 'qrk' ) }}" name="fields[Has_Worked_in_Polar_Regions_Before__c]" value="Yes" />
								<x-form.radio label="{{ __( 'No', 'qrk' ) }}" name="fields[Has_Worked_in_Polar_Regions_Before__c]" value="No" />
							</x-form.field-group>
						</tp-toggle-attribute>
					</x-form.row>

					<x-form.field class="form-job-application__polar-experience">
						<x-form.textarea label="{{ __( 'Describe your experience working in the Polar Regions.', 'qrk' ) }}" name="fields[Experience_in_Polar_Regions__c]" placeholder="Describe" />
					</x-form.field>
				</div>

				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'What Expedition Team Role(s) are you applying for?', 'qrk' ) }}" :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Guest Services(On Ship)', 'qrk' ) }}" value="guest_services" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Polar Retail Boutique Manager(On Ship)', 'qrk' ) }}" value="boutique_manager" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Expedition Leader', 'qrk' ) }}" value="expedition_leader" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Expedition Coordinator', 'qrk' ) }}" value="expedition_coordinator" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Expedition Guide', 'qrk' ) }}" value="expedition_guide" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Glaciology Specialist', 'qrk' ) }}" value="glaciology_specialist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Geology Specialist', 'qrk' ) }}" value="geology_specialist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Ornithology Specialist', 'qrk' ) }}" value="ornithology_specialist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Marine Biology Specialist', 'qrk' ) }}" value="marine_biology_specialist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Polar History Specialist', 'qrk' ) }}" value="polar_history_specialist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Cultural Educator', 'qrk' ) }}" value="cultural_educator" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Photography Guide', 'qrk' ) }}" value="photography_guide" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'French Linguist', 'qrk' ) }}" value="french_linguist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'German Linguist', 'qrk' ) }}" value="german_linguist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Japanese Linguist', 'qrk' ) }}" value="japanese_linguist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Mandarin Linguist', 'qrk' ) }}" value="mandarin_linguist" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Mountain Guide', 'qrk' ) }}" value="mountain_guide" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Sea Kayak Guide', 'qrk' ) }}" value="sea_kayak_guide" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Stand Up Paddleboard Guide', 'qrk' ) }}" value="sup_guide" />
							<x-form.checkbox name="fields[Expedition_Team_Roles__c][]" label="{{ __( 'Helicopter Operations Manager', 'qrk' ) }}" value="helicopter_operations_manager" />
						</x-form.field-group>
					</x-form.row>
				</div>

				<div class="form-job-application__sub-section form-job-application--expand-fields">
					<x-form.row>
						<x-form.field-group title="{{ __( 'What languages do you speak fluently?', 'qrk' ) }}" :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'English', 'qrk' ) }}" value="English" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Italian', 'qrk' ) }}" value="Italian" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Arabic', 'qrk' ) }}" value="Arabic" />
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'Chinese, Mandarin', 'qrk' ) }}" value="Chinese, Mandarin" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Hindi', 'qrk' ) }}" value="Hindi" />
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'Japanese', 'qrk' ) }}" value="Japanese" />
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'French', 'qrk' ) }}" value="French" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Korean', 'qrk' ) }}" value="Korean" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Portuguese', 'qrk' ) }}" value="Portuguese" />
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'German', 'qrk' ) }}" value="German" />
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'Russian', 'qrk' ) }}" value="Russian" />
							<tp-toggle-attribute target=".form-job-application__other-languages" value="true" attribute="required" attribute-value="yes">
								<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'Other', 'qrk' ) }}" value="Other" />
							</tp-toggle-attribute>
							<x-form.checkbox name="fields[Languages__c][]" label="{{ __( 'Spanish', 'qrk' ) }}" value="Spanish" />
							<x-form.checkbox name="fields[Other_Languages__c][]" label="{{ __( 'Bengali', 'qrk' ) }}" value="Bengali" />
						</x-form.field-group>
					</x-form.row>
					<x-form.field class="form-job-application__other-languages">
						<x-form.input type="text" label="{{ __( 'Other Languages Spoken', 'qrk' ) }}" name="fields[Other_Languages__c][]" placeholder="Enter Other Language you Speak"/>
					</x-form.field>
				</div>

				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'What areas do you have work experience in?', 'qrk' ) }}">
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Guiding in Polar Region(s)', 'qrk' ) }}" value="polar_guiding" />
							<div class="form-job-application__option-tooltip">
								<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Outdoor Guiding', 'qrk' ) }}" value="outdoor_guiding" />
								<x-tooltip icon="info">{{ __( 'Hiking, Kayaking, mountain biking, mountaineering', 'qrk' ) }}</x-tooltip>
							</div>
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Ship, Vessel or Boating', 'qrk' ) }}" value="boating" />
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Zodiac Driving', 'qrk' ) }}" value="zodiac_driving" />
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Public Speaking, Lectures and/or Presentations', 'qrk' ) }}" value="public_speaking" />
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Customer Service', 'qrk' ) }}" value="customer_service" />
							<x-form.checkbox name="fields[Work_Areas__c][]" label="{{ __( 'Helicopter Operations', 'qrk' ) }}" value="helicopter_operations" />
						</x-form.field-group>
					</x-form.row>
				</div>

				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'What certifications do you hold?', 'qrk' ) }}">
							<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'STCW-95', 'qrk' ) }}" value="stcw-95" />
							<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Ship Security Awareness Certificate', 'qrk' ) }}" value="ship_security_awareness" />
							<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Seaman\'s Medical Certificate', 'qrk' ) }}" value="seamans_medical" />
							<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Seaman\'s Book', 'qrk' ) }}" value="seamans_book" />
							<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'IAATO Field Staff Certificate', 'qrk' ) }}" value="iaato_field_staff" />
							<div class="form-job-application__option-tooltip">
								<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Boating License', 'qrk' ) }}" value="boating_license" />
								<x-tooltip icon="info">{{ __( 'SVOP, RYA or higher', 'qrk' ) }}</x-tooltip>
							</div>
							<div class="form-job-application__option-tooltip">
								<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Wilderness First Aid', 'qrk' ) }}" value="wilderness_first_aid" />
								<x-tooltip icon="info">{{ __( 'WFR 40 hours or equivalent', 'qrk' ) }}</x-tooltip>
							</div>
							<div class="form-job-application__option-tooltip">
								<x-form.checkbox name="fields[Certifications__c][]" label="{{ __( 'Wilderness First Responder', 'qrk' ) }}" value="wilderness_first_responder" />
								<x-tooltip icon="info">{{ __( 'WFR 80 hours or equivalent', 'qrk' ) }}</x-tooltip>
							</div>
						</x-form.field-group>
					</x-form.row>
				</div>

				<div class="form-job-application__sub-section form-job-application--expand-fields">
					<x-form.row>
						<x-form.field-group title="{{ __( 'Do you have a university degree (or higher) in any of the following subjects?', 'qrk' ) }}">
							<x-form.checkbox name="fields[Degree_Areas__c][]" label="{{ __( 'Climate Science', 'qrk' ) }}" value="climate_science" />
							<x-form.checkbox name="fields[Degree_Areas__c][]" label="{{ __( 'Geology', 'qrk' ) }}" value="geology" />
							<x-form.checkbox name="fields[Degree_Areas__c][]" label="{{ __( 'History', 'qrk' ) }}" value="history" />
							<x-form.checkbox name="fields[Degree_Areas__c][]" label="{{ __( 'Marine Biology', 'qrk' ) }}" value="marine_biology" />
							<tp-toggle-attribute value="true" attribute="required" attribute-value="yes" target=".form-job-application__other-degree">
								<x-form.checkbox name="fields[Degree_Areas__c][]" label="{{ __( 'Other', 'qrk' ) }}" value="other" />
							</tp-toggle-attribute>
						</x-form.field-group>
					</x-form.row>
					<x-form.field class="form-job-application__other-degree">
						<x-form.input type="text" label="{{ __( 'Other Degree Area', 'qrk' ) }}" name="fields[Other_Degrees__c]" placeholder="Enter Subjects" />
					</x-form.field>
				</div>

				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'In what season(s) are you available to work?', 'qrk' ) }}" :validation="[ 'checkbox-group-required' ]" class="form-job-application__season-availability">
							<x-form.checkbox name="fields[Season_Availability__c][]" label="{{ __( 'Arctic season: April to October', 'qrk' ) }}" value="arctic" />
							<x-form.checkbox name="fields[Season_Availability__c][]" label="{{ __( 'Antarctic season: October to April', 'qrk' ) }}" value="antarctic" />
							<div />
						</x-form.field-group>
					</x-form.row>
				</div>

				<div class="form-job-application__sub-section">
					<x-form.row>
						<x-form.field-group title="{{ __( 'What is the longest contract you are able to commit to at a time?', 'qrk' ) }}" :validation="[ 'radio-group-required' ]">
							<x-form.radio name="fields[Maximum_Contract_Length__c]" label="{{ __( 'Less than 2 weeks', 'qrk' ) }}" value="less_than_2_weeks" />
							<x-form.radio name="fields[Maximum_Contract_Length__c]" label="{{ __( '2 weeks', 'qrk' ) }}" value="2_weeks" />
							<x-form.radio name="fields[Maximum_Contract_Length__c]" label="{{ __( '1 month', 'qrk' ) }}" value="1_month" />
							<x-form.radio name="fields[Maximum_Contract_Length__c]" label="{{ __( '1-3 months', 'qrk' ) }}" value="1_3_months" />
							<x-form.radio name="fields[Maximum_Contract_Length__c]" label="{{ __( '3 months or longer', 'qrk' ) }}" value="longer_than_3_months" />
						</x-form.field-group>
					</x-form.row>
				</div>
			</div>

			<div class="form-job-application__section form-job-application--expand-fields">
				<h3 class="form-job-application__title">{{ __( 'Additional Information', 'qrk' ) }}</h3>
				<x-form.row>
					<tp-toggle-attribute trigger="input[type='radio']" target=".form-job-application__referrer-name" value="Yes" attribute="required">
						<x-form.field-group title="{{ __( 'Were you referred to us by someone who works at Quark Expeditions?', 'qrk' ) }}" :validation="[ 'radio-group-required' ]" class="form-job-application__toggle">
							<x-form.radio label="{{ __( 'Yes', 'qrk' ) }}" name="fields[Was_Referred__c]" value="Yes" />
							<x-form.radio label="{{ __( 'No', 'qrk' ) }}" name="fields[Was_Referred__c]" value="No" />
						</x-form.field-group>
					</tp-toggle-attribute>
				</x-form.row>
				<x-form.field class="form-job-application__referrer-name">
					<x-form.input type="text" label="{{ __( 'Who referred you?', 'qrk' ) }}" name="fields[Referrer_Name__c]" placeholder="Enter Name" />
				</x-form.field>

				<x-form.field class="form-job-application__additional-information">
					<x-form.textarea label="{{ __( 'Do you have any additional comments or questions?', 'qrk' ) }}" name="fields[Comments_Questions__c]" placeholder="Enter Additional Information" />
				</x-form.field>
			</div>

			<div class="form-job-application__section form-job-application__resume">
				<h3 class="form-job-application__title">{{ __( 'Resume and Cover Letter', 'qrk' ) }}</h3>
				<x-form.field class="form-field__file" :validation="[ 'required', 'file-size-valid' ]">
					<x-form.label for="resume" class="form-job-application__file-label">{{ __( 'Please Attach Your CV/Résumé', 'qrk' ) }}</x-form.label>
					<x-form.file
						name="resume"
						label="{{ __( 'Choose File', 'qrk' ) }}"
						:allowed_file_types="$resume_allowed_file_types"
						form="{{ $form_id }}"
					/>
				</x-form.field>
				<p class="form-job-application__description">
					{!!
						esc_html__(
							'One file only. 8 MB limit. Allowed types: pdf.',
							'qrk'
						)
					!!}
				</p>
			</div>

			<div class="form-job-application__section">
				<x-form.submit class="form-job-application__submit">
					{{ __( 'Submit', 'qrk' ) }}
				</x-form.submit>
			</div>
		</div>
	</div>
</x-form>
