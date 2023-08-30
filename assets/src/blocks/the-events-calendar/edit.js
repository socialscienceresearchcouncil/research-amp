import { __ } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param  root0
 * @param  root0.attributes
 * @param  root0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const blockProps = () => useBlockProps();

	return (
		<>
			<div { ...blockProps() }>
				{ __(
					'The interface for The Events Calendar will appear here.',
					'research-amp'
				) }
			</div>
		</>
	);
}
