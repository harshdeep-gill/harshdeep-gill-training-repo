@props( [
	'states'           => [],
	'state_label'      => __( 'State of Residency', 'qrk' ),
	'state_code_key'   => 'State_Code__c',
] )

@php
	$state_code_field_name   = sprintf( 'fields[%s]', $state_code_key );
@endphp

<quark-country-selector class="state-selector">
	<x-form.field :validation="[ 'required' ]" class="state-selector__state" data-name="{{ $state_code_field_name }}">
		<x-form.select label="{{ $state_label }}" :name="$state_code_field_name" >
			<x-form.option value="">{!! __( '- Select -', 'qrk' ) !!}</x-form.option>
			@foreach ( $states as $state_code => $state_name )
				<x-form.option value="{{ $state_code }}" label="{{ $state_name }}">{{ $state_name }}</x-form.option>
			@endforeach
		</x-form.select>
	</x-form.field>
</quark-country-selector>
