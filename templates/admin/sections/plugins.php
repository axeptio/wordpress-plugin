<div class="mx-auto max-w-7xl relative">
	<div
		x-show="isGetting"
		x-transition
		class="absolute inset-0 flex justify-center items-center bg-white/70"
	>
		<div class="h-full relative">
			<div role="status" class="text-center sticky top-[120px]">
				<div class="mt-16">
					<svg aria-hidden="true" class="inline w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
						<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
					</svg>
					<div class="italic text-sm mt-2">Loading...</div>
				</div>
			</div>
		</div>
	</div>
	<ul
		role="list" class="divide-y divide-gray-100 transition-all duration-500 sm:duration-700 bg-white"
		:class="{ 'min-h-0': !isGetting, 'min-h-[20rem]': isGetting }"
	>
		<template x-for="(plugin, index) in plugins">
			<li class="flex items-center justify-between gap-x-6 py-5">
				<div class="min-w-0">
					<div class="flex items-center gap-x-6">
						<div>
							<?php \Axeptio\get_template_part( 'admin/plugins/fields/toggle' ); ?>
						</div>
						<div>
							<div class="min-w-0 flex items-start gap-x-3 flex-auto">
								<p class="text-sm font-semibold leading-6 text-gray-900" x-text="plugin.Name"></p>
								<p class="text-sm font-normal leading-6 text-gray-500" x-text="plugin.Author"></p>
								<p
									x-show="activePlugins.includes(index)"
									class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-green-700 bg-green-50 ring-green-600/20"
								>
									<?php echo esc_html__( 'Active', 'axeptio-wordpress-plugin' ); ?>
								</p>
								<div
									class="rounded-md inline-flex mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset whitespace-nowrap items-center"
									:class="{ 'text-green-700 bg-green-50 ring-green-600/20' : !plugin.AxeptioRecommendedSettings, 'text-red-700 bg-red-50 ring-red-600/20' : plugin.AxeptioRecommendedSettings }"
									x-show="plugin.AxeptioRecommendedSettings"
								>
									<svg class="-my-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 96 960 960"><path d="M420.118 498Q446 498 464 479.882q18-18.117 18-44Q482 410 463.882 392q-18.117-18-44-18Q394 374 376 392.118q-18 18.117-18 44Q358 462 376.118 480q18.117 18 44 18Zm-80 200Q366 698 384 679.882q18-18.117 18-44Q402 610 383.882 592q-18.117-18-44-18Q314 574 296 592.118q-18 18.117-18 44Q278 662 296.118 680q18.117 18 44 18ZM600 736q17 0 28.5-11.5T640 696q0-17-11.5-28.5T600 656q-17 0-28.5 11.5T560 696q0 17 11.5 28.5T600 736ZM480.234 976Q398 976 325 944.5q-73-31.5-127.5-86t-86-127.5Q80 658 80 576q0-92 39-172t104.5-135.5q65.5-55.5 151-80T552 182q-6 45 8 85t42.5 68q28.5 28 68.5 41t84 6q-20 61 22 109.5T879 545q8 87-20.5 165T775 847q-55 59-130.794 94-75.794 35-163.972 35ZM480 916q142 0 236-93.5T821 592q-54-20-87.5-59.5T692 442q-81-11-136.5-70T492 235q-74-3-138.5 24t-112 74Q194 380 167 443.5T140 576q0 142 99 241t241 99Zm1-345Z"></path></svg>
									<div class="ml-1"><?php esc_attr_e( 'This extension is subject to consent', 'axeptio-wordpress-plugin' ); ?></div>
								</div>
							</div>
							<div class="min-w-0 mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
								<p x-html="plugin.Description"></p>
							</div>
						</div>
					</div>
				</div>
				<div
					class="flex flex-none items-center gap-x-4"
					x-show="plugin.Metas.enabled !== false"
					x-transition:enter="transition ease-out duration-200"
					x-transition:enter-start="opacity-0"
					x-transition:enter-end="opacity-1"
					x-transition:leave="transition ease-in duration-300"
					x-transition:leave-start="opacity-1"
					x-transition:leave-end="opacity-0"
				>
					<button @click.prevent="editPlugin(plugin)" class="rounded-md bg-white px-3 py-2 text-sm font-semibold ml-2 inline-flex items-center transition ease-out duration-300">
						<svg class="-ml-1 mr-1 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
						</svg>
						<?php echo esc_html__( 'Edit', 'axeptio-wordpress-plugin' ); ?>
					</button>
				</div>
			</li>
		</template>
	</ul>
</div>
