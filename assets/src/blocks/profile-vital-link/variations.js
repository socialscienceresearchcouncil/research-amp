import { __ } from '@wordpress/i18n'

import { EmailIcon } from './icons/email'

const variations = [
	{
		isDefault: true,
		name: 'email',
		metaKey: 'ramp_vital_email',
		attributes: { vitalType: 'emailAddress' },
		title: __( 'Email Address', 'ramp' ),
		placeholder: __( 'Enter email address', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'twitter',
		metaKey: 'ramp_vital_twitter',
		attributes: { vitalType: 'twitterHandle' },
		title: __( 'Twitter Handle', 'ramp' ),
		placeholder: __( 'Enter Twitter handle', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'orcidId',
		metaKey: 'ramp_vital_orcid',
		attributes: { vitalType: 'orcidId' },
		title: __( 'ORCID ID', 'ramp' ),
		placeholder: __( 'Enter ORCID ID', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'website',
		metaKey: 'ramp_vital_website',
		attributes: { vitalType: 'website' },
		title: __( 'Website URL', 'ramp' ),
		placeholder: __( 'Enter Website URL', 'ramp' ),
		icon: EmailIcon
	}
]

export default variations
