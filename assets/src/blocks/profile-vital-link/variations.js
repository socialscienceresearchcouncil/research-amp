import { __ } from '@wordpress/i18n'

import { EmailIcon } from './icons/email'

const variations = [
	{
		isDefault: true,
		name: 'email',
		attributes: { vitalType: 'emailAddress' },
		title: __( 'Email Address', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'twitter',
		attributes: { vitalType: 'twitterHandle' },
		title: __( 'Twitter Handle', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'orcidId',
		attributes: { vitalType: 'orcidId' },
		title: __( 'ORCID ID', 'ramp' ),
		icon: EmailIcon
	},
	{
		name: 'website',
		attributes: { vitalType: 'website' },
		title: __( 'Website URL', 'ramp' ),
		icon: EmailIcon
	}
]

export default variations
