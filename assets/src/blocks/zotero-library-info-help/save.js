import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Save function.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save( {
	attributes,
	className,
} ) {
	const {
		content,
		year
	} = attributes;

	const blockProps = {
		className: 'ginger-timeline-year'
	}

	return (
		<div { ...blockProps }>
			<div className="ginger-timeline-year-year">
				<RichText.Content tagName="h3" value={ year } />
			</div>

			<div className="ginger-timeline-year-content">
				<RichText.Content tagName="ul" value={ content } />
			</div>
		</div>
	);
}
