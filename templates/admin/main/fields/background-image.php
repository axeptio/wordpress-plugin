<?php
$field_name = esc_attr( $data->name );
$field_id   = 'xpwp_' . $field_name;
$is_checked = $data->value === '1';
?>

<div x-data="{ <?php echo $field_name; ?>: <?php echo json_encode( $is_checked ); ?> }">
	<label for="<?php echo $field_id; ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html__( 'Widget Background Image', 'axeptio-wordpress-plugin' ); ?>
	</label>
	<div class="inline-flex items-center w-full relative my-2">
		<?php
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/toggle',
			array(
				'label'        => $data->label,
				'name'         => "axeptio_settings[$field_name]",
				'description'  => $data->description,
				'id'           => $field_id,
				'alpine_state' => $field_name,
				'checked'      => $is_checked,
			)
		);
		?>
	</div>
</div>
