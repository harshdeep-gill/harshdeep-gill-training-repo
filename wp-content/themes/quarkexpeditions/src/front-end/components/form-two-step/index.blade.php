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
					{{-- Sub Region Field --}}
					@case( 'sub-region' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: __( 'Where would you like to travel?', 'qrk' ) }}"
								name="fields[Sub_Region__c]"
								form="{{ $form_id }}"
							>
								@if ( empty( $field['options'] ) )
									<x-form.option
										value="Antarctic Peninsula"
										:label="__( 'Antarctic Peninsula', 'qrk' )"
									>
										{!! __( 'Antarctic Peninsula', 'qrk' ) !!}
									</x-form.option>
									<x-form.option
										value="Falklands & South Georgia"
										:label="__( 'Falklands & South Georgia', 'qrk' )"
									>
										{!! __( 'Falklands & South Georgia', 'qrk' ) !!}
									</x-form.option>
									<x-form.option
										value="Patagonia"
										:label="__( 'Patagonia', 'qrk' )"
									>
										{!! __( 'Patagonia', 'qrk' ) !!}
									</x-form.option>
									<x-form.option
										value="Snow Hill Island"
										:label="__( 'Snow Hill Island', 'qrk' )"
									>
										{!! __( 'Snow Hill Island', 'qrk' ) !!}
									</x-form.option>
								@else
									@foreach ( $field['options'] as $option )
										<x-form.option
											value="{!! $option['value'] ?? '' !!}"
											label="{{ $option['text'] ?? '' }}"
										>
											{{ $option['text'] ?? '' }}
										</x-form.option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break

					{{-- Most Important Factors Field --}}
					@case( 'most-important-factors' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: __( 'The most important factor for you?', 'qrk' ) }}"
								name="fields[Most_Important_Factors__c]"
								form="{{ $form_id }}"
							>
								@if ( empty( $field['options'] ) )
									<x-form.option
										value="Adventure Activities"
										:label="__( 'Adventure Activities', 'qrk' )"
									>
										{!! __( 'Adventure Activities', 'qrk' ) !!}
									</x-form.option>
									<x-form.option value="Budget" :label="__( 'Budget', 'qrk' )">{!! __( 'Budget', 'qrk' ) !!}</x-form.option>
									<x-form.option value="Region" :label="__( 'Destination', 'qrk' )">{!! __( 'Destination', 'qrk' ) !!}</x-form.option>
									<x-form.option value="Schedule" :label="__( 'Schedule', 'qrk' )">{!! __( 'Schedule', 'qrk' ) !!}</x-form.option>
									<x-form.option value="Wildlife" :label="__( 'Wildlife', 'qrk' )">{!! __( 'Wildlife', 'qrk' ) !!}</x-form.option>
								@else
									@foreach ( $field['options'] as $option )
										<x-form.option
											value="{!! $option['value'] ?? '' !!}"
											label="{{ $option['text'] ?? '' }}"
										>
											{{ $option['text'] ?? '' }}
										</x-form.option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break

					{{-- Pax Count Field. --}}
					@case( 'pax-count' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: __( 'How many guests?', 'qrk' ) }}"
								name="fields[Pax_Count__c]"
								form="{{ $form_id }}"
							>
								@if ( empty( $field['options'] ) )
									<x-form.option value="1" label="1">1</x-form.option>
									<x-form.option value="2" label="2">2</x-form.option>
									<x-form.option value="3" label="3">3</x-form.option>
									<x-form.option value="4" label="4">4</x-form.option>
								@else
									@foreach ( $field['options'] as $option )
										<x-form.option
											value="{!! $option['value'] ?? '' !!}"
											label="{{ $option['text'] ?? '' }}"
										>
											{{ $option['text'] ?? '' }}
										</x-form.option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break

					{{-- Season field. --}}
					@case( 'season' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: __( 'When would you like to go?', 'qrk' ) }}"
								name="fields[Season__c]"
								form="{{ $form_id }}"
							>
								@if ( empty( $field['options'] ) )
									<x-form.option
										value="2024-25"
										:label="__( 'Antarctic 2024/25 (Nov \'24 - Mar \'25)', 'qrk' )"
									>
										{!! __( 'Antarctic 2024/25 (Nov \'24 - Mar \'25)', 'qrk' ) !!}
									</x-form.option>
									<x-form.option
										value="2025-26"
										:label="__( 'Antarctic 2025/26 (Nov \'25 - Mar \'26)', 'qrk' )"
									>
										{!! __( 'Antarctic 2025/26 (Nov \'25 - Mar \'26)', 'qrk' ) !!}
									</x-form.option>
								@else
									@foreach ( $field['options'] as $option )
										<x-form.option
											value="{!! $option['value'] ?? '' !!}"
											label="{{ $option['text'] ?? '' }}"
										>
											{{ $option['text'] ?? '' }}
										</x-form.option>
									@endforeach
								@endif
							</x-form.select>
						</x-form.field>
						@break

					{{-- Expedition Name Field. --}}
					@case( 'expedition-name' )
						<x-form.field :validation="[ ! empty( $field['is_required'] ) ? 'required' : '' ]">
							<x-form.select
								label="{{ $field['label'] ?: __( 'Which voyage are you intersted in?', 'qrk' ) }}"
								name="fields[Expedition__c]"
								form="{{ $form_id }}"
							>
								@if ( ! empty( $field['options'] ) )
									@foreach ( $field['options'] as $option )
										<x-form.option
											value="{!! $option['value'] ?? '' !!}"
											label="{{ $option['text'] ?? '' }}"
										>
											{{ $option['text'] ?? '' }}
										</x-form.option>
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
					{{ __( 'Request a Quote', 'qrk' ) }}
					<x-button.sub-title :title="__( 'It only takes 2 minutes', 'qrk' )" />
				</x-button>
			</x-form-two-step.modal-cta>
		</x-form.buttons>
	</quark-form-two-step>
</div>
