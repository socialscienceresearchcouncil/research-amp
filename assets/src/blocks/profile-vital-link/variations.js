import { __ } from '@wordpress/i18n'

import { EmailIcon } from './icons/email'

const variations = [
	{
		isDefault: true,
		name: 'email',
		metaKey: 'ramp_vital_email',
		attributes: { vitalType: 'emailAddress' },
		title: __( 'Email Address', 'research-amp' ),
		placeholder: __( 'Enter email address', 'research-amp' ),
		icon: EmailIcon
	},
	{
		name: 'twitter',
		metaKey: 'ramp_vital_twitter',
		attributes: { vitalType: 'twitterHandle' },
		title: __( 'Twitter Handle', 'research-amp' ),
		placeholder: __( 'Enter Twitter handle', 'research-amp' ),
		icon: EmailIcon
	},
	{
		name: 'orcidId',
		metaKey: 'ramp_vital_orcid',
		attributes: { vitalType: 'orcidId' },
		title: __( 'ORCID ID', 'research-amp' ),
		placeholder: __( 'Enter ORCID ID', 'research-amp' ),
		icon: EmailIcon
	},
	{
		name: 'website',
		metaKey: 'ramp_vital_website',
		attributes: { vitalType: 'website' },
		title: __( 'Website URL', 'research-amp' ),
		placeholder: __( 'Enter Website URL', 'research-amp' ),
		icon: EmailIcon
	}
]

export default variations
