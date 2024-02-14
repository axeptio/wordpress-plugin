<div class="flex w-0 flex-1 items-center">
	<label for="<?php echo esc_attr( $data->id ); ?>" class="group relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2" role="switch" aria-checked="false" :aria-checked="(<?php echo esc_attr( $data->alpine_state ); ?> ?? '0').toString()">
		<span aria-hidden="true" class="pointer-events-none absolute h-full w-full rounded-md bg-white"></span>
		<span aria-hidden="true" class="bg-gray-200 pointer-events-none absolute mx-auto h-4 w-9 rounded-full transition-colors duration-200 ease-in-out" x-state:on="Enabled" x-state:off="Not Enabled" :class="{ 'bg-amber-400': <?php echo esc_attr( $data->alpine_state ); ?>, 'bg-gray-200': !(<?php echo esc_attr( $data->alpine_state ); ?>) }"></span>
		<span aria-hidden="true" class="translate-x-0 pointer-events-none absolute left-0 inline-block h-5 w-5 transform rounded-full border border-gray-200 bg-white shadow ring-0 transition-transform duration-200 ease-in-out" x-state:on="Enabled" x-state:off="Not Enabled" :class="{ 'translate-x-5': <?php echo esc_attr( $data->alpine_state ); ?>, 'translate-x-0': !(<?php echo esc_attr( $data->alpine_state ); ?>) }"></span>
	</label>
	<label for="<?php echo esc_attr( $data->id ); ?>" class="ml-4 flex flex-col min-w-0 flex-1 gap-1">
		<span class="truncate font-medium"><?php echo esc_html( $data->label ); ?></span>
		<span class="text-gray-500 text-xs"><?php echo esc_html( $data->description ); ?></span>
	</label>
	<input type="checkbox" @change="<?php echo esc_attr( $data->alpine_state ); ?> = !<?php echo esc_attr( $data->alpine_state ); ?>"
			class="appearance-none w-full h-full active:outline-none focus:outline-none opacity-0 absolute -left-full top-0"
			id="<?php echo esc_attr( $data->id ); ?>" name="<?php echo esc_attr( $data->name ); ?>"
			value="1" <?php echo esc_attr( $data->checked ) ? 'checked' : ''; ?>
			placeholder="">
</div>
