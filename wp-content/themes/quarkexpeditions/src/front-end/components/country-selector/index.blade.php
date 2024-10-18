@props( [
	'countries'          => [],
	'states'             => [],
	'enable_name_fields' => false,
	'country_label'      => 'Country',
	'state_label'        => 'State/Province',
	'country_code_key'   => 'Country_Code__c',
	'country_key'        => 'Country__c',
	'state_code_key'     => 'State_Code__c',
	'state_key'          => 'State__c',
] )

@php
	$country_code_field_name = sprintf( 'fields[%s]', $country_code_key );
	$country_field_name      = sprintf( 'fields[%s]', $country_key );
	$state_code_field_name   = sprintf( 'fields[%s]', $state_code_key );
	$state_field_name        = sprintf( 'fields[%s]', $state_key );

	// Sort countries alphabetically by name.
	asort( $countries );
@endphp

<quark-country-selector class="country-selector">
	<x-form.field :validation="[ 'required' ]" class="country-selector__country">
		<x-form.select label="{{ $country_label }}" :name="$country_code_field_name">
			<x-form.option value="">- Select -</x-form.option>
			@foreach ( $countries as $country_code => $country_name )
				<x-form.option value="{{ $country_code }}" label="{{ $country_name }}">{{ $country_name }}</x-form.option>
			@endforeach
		</x-form.select>
	</x-form.field>

	@foreach ( $states as $country_code => $country_states )
		<x-form.field :validation="[ 'required' ]" class="country-selector__state" data-country="{{ $country_code }}" data-name="{{ $state_code_field_name }}">
			<x-form.select label="{{ $state_label }}">
				<x-form.option value="">- Select -</x-form.option>
				@foreach ( $country_states as $state_code => $state_name )
					<x-form.option value="{{ $state_code }}" label="{{ $state_name }}">{{ $state_name }}</x-form.option>
				@endforeach
			</x-form.select>
		</x-form.field>
	@endforeach

	@if ( true === $enable_name_fields )
		<x-form.input type="hidden" :name="$country_field_name" class="country-selector__country-name"/>
		<x-form.input type="hidden" :name="$state_field_name" class="country-selector__state-name"/>
	@endif
</quark-country-selector>
