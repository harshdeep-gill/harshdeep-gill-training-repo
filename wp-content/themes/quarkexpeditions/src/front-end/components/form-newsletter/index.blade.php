@props( [
	'form_id'   => '',
	'class'     => '',
	'countries' => [],
	'states'    => [],
] )

@php
	$classes = [ 'form-newsletter' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<x-section @class( $classes )>
	<quark-form-newsletter>
		<x-form
			salesforce_object="WebForm_Newsletter_Sign_up__c"
			id="{{ $form_id }}"
		>
			<div class="form-newsletter__content">
				<h1 class="form-newsletter__title">Subscribe To Our Newsletter</h1>
				<div class="form-newsletter__description">
					<h3 class="form-newsletter__subtitle">Sign up to receive a regular dose of polar inspiration in your inbox!</h3>
					<p>
						You'll get insider details on new Arctic and Antarctic itineraries
						and product launches, the heads-up on special discounts, advanced
						notice on expert-led webinars, plus early announcements of new
						curated videos. In short, you'll receive the latest polar
						inspiration before anybody else!
					</p>
				</div>
				<div class="form-newsletter__fields">
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input
								type="text"
								label="First Name"
								placeholder="Enter First Name"
								name="fields[First_Name__c]"
							/>
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input
								type="text"
								label="Last
								Name"
								placeholder="Enter Last Name" name="fields[Last_Name__c]"
							/>
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input
								type="email"
								label="Email"
								placeholder="Enter Email"
								name="fields[Email__c]"
							/>
						</x-form.field>
						<x-country-selector :countries="$countries" :states="$states" />
					</x-form.row>
					<x-form.row>
						<x-form.field-group>
							<x-form.checkbox name="fields[Travel_Agent__c]" label="I am a Travel Agent" />
						</x-form.field-group>
					</x-form.row>
					<p class="form-newsletter__privacy">
						We respect your privacy. You may unsubscribe from our communications at any time. Please refer to our privacy policy for full detail.
					</p>
					{!! $slot !!}
				</div>
				<x-form.buttons>
					<x-form.submit size="big">Submit</x-form.submit>
				</x-form.buttons>
			</div>
			<div class="form-newsletter__success">
				<h1 class="form-newsletter__success-title">You've Been Subscribed</h1>
				<div class="form-newsletter__success-info">
					<p>Thanks for signing up for our monthly newsletter.</p>
					<p>We also invite you to join the conversation, see photos, videos and read some comments from our past travelers or share your favorite travel moment with us.</p>
					<p class="form-newsletter__social-cta">Join the Quark Expeditions Community:</p>
					<div class="form-newsletter__social-links">
						<x-form-newsletter.social-link type="facebook" url="#" />
						<x-form-newsletter.social-link type="twitter" url="#" />
						<x-form-newsletter.social-link type="youtube" url="#" />
						<x-form-newsletter.social-link type="google" url="#" />
						<x-form-newsletter.social-link type="pinterest" url="#" />
					</div>
				</div>
			</div>
		</x-form>
	</quark-form-newsletter>
</x-section>

