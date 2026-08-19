<?php defined( 'ABSPATH' ) || exit; ?>
<input
	type="<?php echo esc_attr( $data->type ); ?>"
	data-value-input
	x-model="row.value"
	:name="fieldName(index, 'value')"
	:placeholder="placeholderFor(row)"
	class="block w-full rounded-lg border-0 py-2.5 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-amber-500"
>
