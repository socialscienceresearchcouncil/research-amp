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

	const getHrefForVitalType = ( vitalType, value ) => {
		switch ( vitalType ) {
			case 'email' :
				return 'mailto:' + value

			// @todo validation
			case 'twitter' :
				return 'https://twitter.com/' + value

			// @todo validation
			case 'orcidId' :
				return 'https://orcid.org/' + value

			default :
				return value
		}
	}

	const linkHref = getHrefForVitalType( vitalType, value )

	if ( value ) {
		return (
			<div { ...blockProps() }>
				<a href={ linkHref } className="ramp-profile-vital-link-text">
					{ value }
				</a>
			</div>
		);
	} else {
		return ( <div></div> )
	}
}
