import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Spinner } from '@wordpress/components'
import {
	useBlockProps
} from '@wordpress/block-editor';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	setAttributes,
} ) {
	const blockProps = () => {
		let classNames = []

		return useBlockProps( {
			className: classNames
		} )
	}

	const { blockMarkup } = useSelect( ( select ) => {
		const blockMarkup = select( 'ramp' ).getBlockMarkup( 'research-topics' )

		return {
			blockMarkup
		}
	}, [] )

	return (
		<div { ...blockProps() }>
			{ blockMarkup ? blockMarkup : <Spinner /> }
		</div>
	);
}
