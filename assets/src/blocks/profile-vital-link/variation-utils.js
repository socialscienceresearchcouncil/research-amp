import { find } from 'lodash';
import variations from './variations';

export const getIconByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.icon : EmailIcon
}

export const getPlaceholderByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.placeholder : __( 'Enter content' )
}

export const getTitleByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.title : __( 'Enter content' )
}

export const getMetaKeyByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.metaKey : ''
}
