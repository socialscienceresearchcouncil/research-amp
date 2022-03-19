import { __ } from '@wordpress/i18n'

import { ToggleControl } from '@wordpress/components'

const PublicationDateToggle = ( props ) => {
	const {
		onChangeCallback,
		showPublicationDate
	} = props

	return (
		<ToggleControl
			label={ __( 'Show publication date?', 'ramp' ) }
			checked={ showPublicationDate }
			onChange={ onChangeCallback }
			help={ __( 'Show the publication date for each item as part of the byline.', 'ramp' ) }
		/>
	)
}

export default PublicationDateToggle
