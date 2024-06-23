@props( [
	'class'            => '',
	'thank_you_page'   => '',
	'form_id'          => 'form-two-step',
	'countries'        => [],
	'states'           => [],
	'hidden_fields'    => [],
	'background_color' => 'black',
	'fields'           => [],
] )

@php
	$classes = [ 'form-two-step' ];

	// Add background color class.
	if ( ! empty( $background_color ) ) {
		switch ( $background_color ) {
			case 'white':
				$classes[] = 'form-two-step--background-white';
				break;
			case 'black':
				$classes[] = 'color-context--dark';
				break;
		}
	}

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<quark-form-two-step>
		@if ( ! empty( $fields ) )
			@foreach ( $fields as $field )
				@switch( $field['field_type'] )
					@case( 'sub-region' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: 'Where would you like to travel?' }}"
								name="fields[Sub_Region__c]"
								form="{{ $form_id }}"
							>
								<option value="">- Select -</option>
								@if ( empty( $field['options'] ) )
									<option value="Antarctic Peninsula">Antarctic Peninsula</option>
									<option value="Falklands & South Georgia">Falklands & South Georgia</option>
									<option value="Patagonia">Patagonia</option>
									<option value="Snow Hill Island">Snow Hill Island</option>
								@else
									@foreach ( $field['options'] as $option )
										<option value="{{ $option['value'] ?? '' }}">{{ $option['text'] ?? '' }}</option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break
					@case( 'most-important-factors' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: 'The most important factor for you?' }}"
								name="fields[Most_Important_Factors__c]"
								form="{{ $form_id }}"
							>
								<option value="">- Select -</option>
								@if ( empty( $field['options'] ) )
									<option value="Adventure Activities">Adventure Activities</option>
									<option value="Budget">Budget</option>
									<option value="Region">Destination</option>
									<option value="Schedule">Schedule</option>
									<option value="Wildlife">Wildlife</option>
								@else
									@foreach ( $field['options'] as $option )
										<option value="{{ $option['value'] ?? '' }}">{{ $option['text'] ?? '' }}</option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break
					@case( 'pax-count' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: 'The most important factor for you?' }}"
								name="fields[Pax_Count__c]"
								form="{{ $form_id }}"
							>
								<option value="">- Select -</option>
								@if ( empty( $field['options'] ) )
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
								@else
									@foreach ( $field['options'] as $option )
										<option value="{{ $option['value'] ?? '' }}">{{ $option['text'] ?? '' }}</option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break
					@case( 'season' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: 'When would you like to go?' }}"
								name="fields[Season__c]"
								form="{{ $form_id }}"
							>
								<option value="">- Select -</option>
								@if ( empty( $field['options'] ) )
									<option value="2024-25">Antarctic 2024/25 (Nov '24 - Mar '25)</option>
									<option value="2025-26">Antarctic 2025/26 (Nov '25 - Mar '26)</option>
								@else
									@foreach ( $field['options'] as $option )
										<option value="{{ $option['value'] ?? '' }}">{{ $option['text'] ?? '' }}</option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break
					@case( 'expedition-name' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: 'Which voyage are you intersted in?' }}"
								name="fields[Expedition__c]"
								form="{{ $form_id }}"
							>
								<option value="">- Select -</option>
								@if ( ! empty( $field['options'] ) )
									@foreach ( $field['options'] as $option )
										<option value="{{ $option['value'] ?? '' }}">{{ $option['text'] ?? '' }}</option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break
				@endswitch
			@endforeach
		@endif
		<x-form.buttons>
			<x-form-two-step.modal-cta
				class="form-two-step__modal-open"
				form_id="{{ $form_id }}"
				thank_you_page="{{ $thank_you_page }}"
				:hidden_fields="$hidden_fields"
				:countries="$countries"
				:states="$states"
			>
				<x-button type="button">
					Request a Quote
					<x-button.sub-title title="It only takes 2 minutes!" />
				</x-button>
			</x-form-two-step.modal-cta>
		</x-form.buttons>
	</quark-form-two-step>
</div>
