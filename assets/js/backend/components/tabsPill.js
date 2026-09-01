export default function tabsPill() {
	return {
		clip: 'inset(0 100% 0 0 round 9999px)',
		move( animate = true ) {
			const tab = this.$el.querySelector( `[data-tab="${ this.currentTab }"]` );
			if ( ! tab ) {
				return;
			}
			const nav = this.$el.getBoundingClientRect();
			if ( nav.width === 0 ) {
				return;
			}
			const box = tab.getBoundingClientRect();
			this.$el.style.setProperty( '--tab-pill-duration', animate ? '250ms' : '0ms' );
			this.clip = `inset(0 ${ nav.right - box.right }px 0 ${ box.left - nav.left }px round 9999px)`;
		},
		init() {
			this.move( false );
			this.$watch( 'currentTab', () => this.move() );
			document.fonts?.ready.then( () => this.move( false ) );

			const observer = new window.ResizeObserver( () => this.move( false ) );
			observer.observe( this.$el );
			this.$el
				.querySelectorAll( '[data-tab]' )
				.forEach( ( tab ) => observer.observe( tab ) );
		},
	};
}
