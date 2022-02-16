import { __ } from '@wordpress/i18n';
import {
	useBlockProps
} from '@wordpress/block-editor';

import {
	getIconByVitalType,
} from './variation-utils'

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
		value,
		vitalType
	} = attributes;

	const IconComponent = getIconByVitalType( vitalType )

	const blockProps = () => {
		const classNames = [
			'ramp-profile-vital-link'
		]

		return useBlockProps( {
			className: classNames
		} )
	}

	if ( value ) {
		console.log('rendering');
		return (
			<div { ...blockProps }>
				<IconComponent />

				<span className="ramp-profile-vital-link-text">
					{ value }
				</span>
			</div>
		);
	} else {
		return ( <div></div> )
	}
}
