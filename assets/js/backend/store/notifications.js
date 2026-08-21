const ENTER_DELAY = 30;
const LEAVE_DURATION = 300;

export default {
	items: [],
	nextId: 1,
	autoClose: true,
	autoCloseDelay: 5000,

	push( message, type = 'error' ) {
		const id = this.nextId++;
		this.items.push( { id, message, type, visible: false } );

		setTimeout( () => {
			const item = this.items.find( ( notification ) => notification.id === id );
			if ( item ) {
				item.visible = true;
			}
		}, ENTER_DELAY );

		if ( this.autoClose ) {
			setTimeout( () => this.dismiss( id ), this.autoCloseDelay );
		}
	},

	dismiss( id ) {
		const item = this.items.find( ( notification ) => notification.id === id );
		if ( ! item ) {
			return;
		}

		item.visible = false;
		setTimeout( () => {
			this.items = this.items.filter( ( notification ) => notification.id !== id );
		}, LEAVE_DURATION );
	},
};
