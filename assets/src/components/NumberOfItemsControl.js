import { __ } from '@wordpress/i18n'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

const NumberOfItemsControl = ( props ) => {
	const {
		onChangeCallback,
		numberOfItems
	} = props

	return (
		<NumberControl
			label={ __( 'Number of items to show', 'ramp' ) }
			value={ numberOfItems }
			min={ 1 }
			step={ 1 }
			onChange={ onChangeCallback }
		/>
	)
}

export default NumberOfItemsControl
