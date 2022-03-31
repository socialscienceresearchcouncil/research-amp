import { __ } from '@wordpress/i18n';
import {
	useBlockProps
} from '@wordpress/block-editor';

export default function save( {
	attributes,
	className,
} ) {
	const {
		value,
		vitalType
	} = attributes;

	const blockProps = () => {
		const additionalClassNames = [
		]

		return useBlockProps.save( {
			className: 'ramp-profile-vital-link-' + vitalType
		} )
	}

	if ( value ) {
		return (
			<div { ...blockProps() }>
				<span className="ramp-profile-vital-link-text">
					{ value }
				</span>
			</div>
		);
	} else {
		return ( <div></div> )
	}
}
