import { __ } from '@wordpress/i18n';

import { ToggleControl } from '@wordpress/components';

const PublicationDateToggle = ( props ) => {
	const { onChangeCallback, showPublicationDate } = props;

	return (
		<ToggleControl
			label={ __( 'Show publication date?', 'research-amp' ) }
			checked={ showPublicationDate }
			onChange={ onChangeCallback }
			help={ __(
				'Show the publication date for each item as part of the byline.',
				'research-amp'
			) }
		/>
	);
};

export default PublicationDateToggle;
