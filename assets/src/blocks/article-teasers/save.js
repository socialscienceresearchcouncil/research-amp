import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
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
		interests
	} = attributes;
	console.log(attributes);

	const blockProps = useBlockProps.save( {
		className: ( className ? className : 'ginger-interests-block' ),
	} );

	return (
		<div { ...blockProps }>
			<h2>Interests</h2>
			<ul className="interest-tags">
				{interests.map(interest => (
					<li key={ "interest-" + interest.id }>
						<a href={interest.link}>
							{interest.name}
						</a>
					</li>
				))}
			</ul>
		</div>
	);
}
