import { __ } from '@wordpress/i18n';

import { ToggleControl } from '@wordpress/components';

const LoadMoreToggle = ( props ) => {
	const { disabled, onChangeCallback, showLoadMore } = props;

	return (
		<ToggleControl
			disabled={ disabled }
			label={ __( 'Show Load More button?', 'research-amp' ) }
			checked={ showLoadMore }
			onChange={ onChangeCallback }
			help={ __(
				'Show a "Load More" button, which users can click to load another page of results. Useful primarily for archive pages.',
				'research-amp'
			) }
		/>
	);
};

export default LoadMoreToggle;
