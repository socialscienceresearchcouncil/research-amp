import './block.scss'

import './frontend.js'

import { __ } from '@wordpress/i18n';
import {
	useBlockProps
} from '@wordpress/block-editor';

export default function save( {
	className,
} ) {
	const blockProps = () => {
		return useBlockProps.save()
	}

	return (
		<div { ...blockProps() }>
			<button className="nav-search-button">
				<span className="screen-reader-text">{ __( 'Click to search site', 'ramp' ) }</span>
			</button>
		</div>
	);
}
