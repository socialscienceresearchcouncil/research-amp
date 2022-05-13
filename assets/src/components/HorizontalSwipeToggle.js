import { __ } from '@wordpress/i18n'

import { ToggleControl } from '@wordpress/components'

const HorizontalSwipeToggle = ( props ) => {
	const {
		onChangeCallback,
		horizontalSwipe
	} = props

	return (
		<ToggleControl
			label={ __( 'Swipe on mobile?', 'research-amp' ) }
			checked={ horizontalSwipe }
			onChange={ onChangeCallback }
			help={ __( 'On mobile devices, items will be shown horizontally, and users can swipe to see items that are off-screen.', 'research-amp' ) }
		/>
	)
}

export default HorizontalSwipeToggle
