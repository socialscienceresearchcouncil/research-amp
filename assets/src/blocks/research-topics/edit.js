import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render';
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

	const spinner = <Spinner />

	return (
		<div { ...blockProps() }>
			<ServerSideRender
				block="ramp/research-topics"
				httpMethod="GET"
				LoadingResponsePlaceholder={ Spinner }
			/>
		</div>
	);
}
