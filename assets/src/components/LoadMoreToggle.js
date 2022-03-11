import { __ } from '@wordpress/i18n'

import { ToggleControl } from '@wordpress/components'

const LoadMoreToggle = ( props ) => {
	const {
		onChangeCallback,
		showLoadMore
	} = props

	return (
		<ToggleControl
			label={ __( 'Show Load More button?', 'ramp' ) }
			checked={ showLoadMore }
			onChange={ onChangeCallback }
			help={ __( 'Show a "Load More" button, which users can click to load another page of results. Useful primarily for archive pages.', 'ramp' ) }
		/>
	)
}

export default LoadMoreToggle
