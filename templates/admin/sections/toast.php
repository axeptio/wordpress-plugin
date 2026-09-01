<?php defined( 'ABSPATH' ) || exit; ?>
<div
	x-cloak
	x-show="$store.notifications.items.length > 0"
	role="region"
	aria-label="<?php esc_attr_e( 'Notifications', 'axeptio-sdk-integration' ); ?>"
	class="fixed bottom-4 right-4 z-50 flex w-80 flex-col gap-2"
>
	<template x-for="notification in $store.notifications.items" :key="notification.id">
		<div
			x-show="notification.visible"
			x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="opacity-0 translate-x-12"
			x-transition:enter-end="opacity-100 translate-x-0"
			x-transition:leave="transition ease-in duration-200"
			x-transition:leave-start="opacity-100 translate-x-0"
			x-transition:leave-end="opacity-0 translate-x-12"
			class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-4 text-sm shadow-lg"
			:role="notification.type === 'error' ? 'alert' : 'status'"
		>
			<div
				class="flex size-8 flex-none items-center justify-center rounded-full"
				:class="{ 'bg-red-100': notification.type === 'error', 'bg-green-100': notification.type === 'success' }"
			>
				<img x-show="notification.type === 'error'" class="size-4" src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'alert.svg' ) ); ?>" alt=""/>
				<img x-show="notification.type === 'success'" class="size-4" src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'check.svg' ) ); ?>" alt=""/>
			</div>
			<div class="grow text-gray-900" x-text="notification.message"></div>
			<button
				@click="$store.notifications.dismiss(notification.id)"
				type="button"
				class="size-5 flex-none p-0 opacity-60 transition-opacity duration-150 hover:opacity-100"
			>
				<img class="size-full object-contain" src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'x-mark.svg' ) ); ?>" alt=""/>
				<span class="sr-only"><?php esc_html_e( 'Dismiss', 'axeptio-sdk-integration' ); ?></span>
			</button>
		</div>
	</template>
</div>
