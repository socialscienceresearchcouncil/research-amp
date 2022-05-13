import { __, sprintf } from '@wordpress/i18n'

import './block.scss'

import {
	useBlockProps
} from '@wordpress/block-editor'

import FakeButton from '../../components/FakeButton'

export default function edit() {
	const blockProps = useBlockProps()

	return (
		<div { ...blockProps }>
			<FakeButton
				alignClass="aligncenter"
				buttonStyle="secondary"
				text={ __( 'Load More', 'research-amp' ) }
			/>
		</div>
	)
}
