import React, { useState } from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';

import Item from './Item';

const SortableItem = ( props ) => {
	const { id, label, handleRemoveClick } = props;

	const [ hovered, setHovered ] = useState( false );

	const { attributes, listeners, setNodeRef, transform, transition } =
		useSortable( { id } );

	const style = transform
		? {
				transform: `translate3d(${ transform.x }px, ${ transform.y }px, 0px)`,
		  }
		: undefined;

	const itemClassnames = classNames( {
		'sortable-multi-select-item': true,
		'sortable-multi-select-item-hover': hovered,
	} );

	return (
		<Item
			ref={ setNodeRef }
			style={ style }
			label={ label }
			{ ...attributes }
			className={ itemClassnames }
		>
			<button
				className="sortable-multi-select-item-remove"
				onClick={ () => handleRemoveClick( id ) }
			>
				<span className="screen-reader-text">
					{ __( 'Remove item', 'research-amp' ) }
				</span>
			</button>

			<div
				className="sortable-multi-select-item-handle"
				onMouseEnter={ () => setHovered( true ) }
				onMouseLeave={ () => setHovered( false ) }
				{ ...listeners }
			>
				{ label }
			</div>
		</Item>
	);
};

export default SortableItem;
