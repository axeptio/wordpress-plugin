<?php

use Axeptio\Admin;

$value = isset( $value ) ? $value : [
	"position"  => 'last',
	"step_name" => 'wordpress'
];

// 'ph' is a shorthand for placeholders
$ph = Admin::DEFAULT_WIDGET_CONFIGURATION;

$row_exists = isset( $row_exists ) ? $row_exists : false;

$admin = Admin::instance();

?>
<form style="max-width: 800px;" method="post">
    <input type="hidden" name="action" value="widget_configuration"/>
    <table class="form-table">
        <tr>
            <th scope="row"><label
                        for="cookies_version"><?= __( 'Axeptio Cookies Config', 'axeptio-wordpress-plugin' ) ?></label>
            </th>
            <td>
                <select id="cookies_version" name="cookies_version">
                    <option value=""><?= __( 'Every config', 'axeptio-wordpress-plugin' ) ?></option>
					<?php
					$savedCookiesVersion = get_option( Admin::OPTION_COOKIES_VERSION );
					foreach ( $admin->axeptioConfiguration->cookies as $cookieConfiguration ) {
						$selected = $cookieConfiguration->identifier === $value['axeptio_configuration_id'] ?
							'selected="selected"' : '';

						echo "
                            <option 
                                value='$cookieConfiguration->identifier'
                                data-name='$cookieConfiguration->name'
                                data-identifier='$cookieConfiguration->identifier'
                                data-language='$cookieConfiguration->language'
                                $selected
                            >
                                $cookieConfiguration->title
                            </option>";
					}
					?>
                </select>
                <p class="description cookies_version">
                    <span class="all hidden">
                        <?= __( '<strong>There\'s no cookies configuration selected.</strong>
                        This means this rule will apply to every cookie configurations
                        as long as there\'s not another Plugin setup that
                        specifies the cookies configuration explicitly.', 'axeptio-wordpress-plugin' ) ?>

                    </span>
                    <span class="selected hidden">

                        <strong><?= __( 'You have selected the configuration', 'axeptio-wordpress-plugin' ) ?>
                            <code id="axeptio_configuration_identifier"></code>
                        </strong>.<br/>
                        <?= __( 'This rule will only apply when this very configuration is the one loaded on the website.
                        For reference, this configuration has been associated with the following language code:', 'axeptio-wordpress-plugin' ) ?>
                        <code id="axeptio_configuration_language"></code><?= __( ', and has the following name:', 'axeptio-wordpress-plugin' ) ?>
                        <code id="axeptio_configuration_name"></code>.
                    </span>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
				<?= __( 'Settings', 'axeptio-wordpress-plugin' ) ?>
            </th>
            <td>
                <div class="form-field">
                    <p>
                        <label for="step_name"><?= __( 'Step name', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="step_name" name="step_name" placeholder="wordpress" class="code"
                               value="<?= $value['step_name'] ?: 'wordpress' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="insert_position"><?= __( 'Insert position', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <select type="text" id="insert_position" name="insert_position">
							<?php
							$options = [
								'after_welcome_step' => 'After the welcome step',
								'first'              => 'First',
								'last'               => 'Last'
							];
							foreach ( $options as $val => $label ) {
								$checked = $value['insert_position'] == $val ? 'selected="selected"' : '';
								echo "<option value='$val' $checked>$label</option>";
							}
							?>
                        </select>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="position"><?= __( 'Position', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="number" id="position" name="position"
                               value="<?= $value['position'] ?: '0' ?>"
                        />
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">
				<?= __( 'Texts', 'axeptio-wordpress-plugin' ) ?>
            </th>
            <td>
                <div class="form-field">
                    <p>
                        <label for="step_title"><?= __( 'Title', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="step_title" name="step_title"
                               placeholder="<?= $ph['step_title'] ?>"
                               value="<?= $value['step_title'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_topTitle"><?= __( 'Top Title', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="step_topTitle" name="step_topTitle"
                               placeholder="<?= $ph['step_topTitle'] ?>"
                               value="<?= $value['step_topTitle'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_subTitle"><?= __( 'Subtitle', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="step_subTitle" name="step_subTitle"
                               placeholder="<?= $ph['step_subTitle'] ?>"
                               value="<?= $value['step_subTitle'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_message"><?= __( 'Message (HTML)', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <textarea type="text" id="step_message" class="code" rows="5"
                                  placeholder="<?= $ph['step_message'] ?>"
                                  name="step_message"><?= $value['step_message'] ?: '' ?></textarea>
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">
				<?= __( 'Appearance', 'axeptio-wordpress-plugin' ) ?>
            </th>
            <td>
                <div class="form-field">
                    <p>
                        <label for="step_image"><?= __( 'Image URL', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="step_image" name="step_image"
                               value="<?= $value['step_image'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_imageHeight"><?= __( 'Image Height (px)', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <input type="number" id="step_imageHeight" name="step_imageHeight"
                               value="<?= $value['step_imageHeight'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_imageWidth"><?= __( 'Image Width (px)', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <input type="number" id="step_imageWidth" name="step_imageWidth"
                               value="<?= $value['step_imageWidth'] ?: '' ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="step_disablePaint"><?= __( 'Disable Paint Background', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <input type="checkbox"
                               id="step_disablePaint"
                               name="step_imageWidth"
							<?= $value['step_disablePaint'] ? 'checked="checked"' : '' ?> />
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <p class="submit">
                    <input type="submit"
                           name="submit"
                           id="submit"
                           class="button button-primary"
                           value="<?= __( 'Save Changes', 'axeptio-wordpress-plugin' ) ?>">
                </p>
            </td>
        </tr>
    </table>
</form>
