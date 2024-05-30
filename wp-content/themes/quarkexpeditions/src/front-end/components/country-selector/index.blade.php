@props( [
	'countries'    => [],
	'states'       => [],
	'class_prefix' => '',
] )

<quark-country-selector class="country-selector">
	<x-form.field :validation="[ 'required' ]" class="country-selector__country">
		<x-form.select label="Country" name="fields[Country_Code__c]">
			<x-form.option value="">- Select -</x-form.option>
			@foreach ( $countries as $country_code => $country_name )
				<x-form.option value="{{ $country_code }}" label="{{ $country_name }}">{{ $country_name }}</x-form.option>
			@endforeach
		</x-form.select>
	</x-form.field>

	@foreach ( $states as $country_code => $country_states )
		<x-form.field :validation="[ 'required' ]" data-country="{{ $country_code }}" class="country-selector__state" data-name="fields[State_Code__c]">
			<x-form.select label="State/Province">
				<x-form.option value="">- Select -</x-form.option>
				@foreach ( $country_states as $state_code => $state_name )
					<x-form.option value="{{ $state_code }}" label="{{ $state_name }}">{{ $state_name }}</x-form.option>
				@endforeach
			</x-form.select>
		</x-form.field>
	@endforeach
</quark-country-selector>
