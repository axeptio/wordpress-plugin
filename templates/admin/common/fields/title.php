<h2>
	<?php echo esc_html( $data->title ); ?>
</h2>

<?php if ( isset( $data->description ) ) : ?>
	<p class="mt-1 text-sm leading-6 text-gray-600"><?php echo esc_html( $data->description ); ?></p>
<?php endif; ?>
