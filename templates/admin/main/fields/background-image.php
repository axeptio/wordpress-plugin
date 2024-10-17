<?php
$axeptio_field_name = esc_attr( $data->name );
$axeptio_field_id   = 'xpwp_' . $axeptio_field_name;
$axeptio_is_checked = '1' === $data->value;
?>

<div x-data="{ <?php echo esc_attr( $axeptio_field_name ); ?>: <?php echo wp_json_encode( $axeptio_is_checked ); ?> }">
	<label for="<?php echo esc_attr( $axeptio_field_id ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html__( 'Widget Background Image', 'axeptio-wordpress-plugin' ); ?>
	</label>
	<div class="inline-flex items-center w-full relative my-2">
		<?php
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/toggle',
			array(
				'label'        => $data->label,
				'name'         => "axeptio_settings[$axeptio_field_name]",
				'description'  => $data->description,
				'id'           => $axeptio_field_id,
				'alpine_state' => $axeptio_field_name,
				'checked'      => $axeptio_is_checked,
			)
		);
		?>
	</div>
</div>
