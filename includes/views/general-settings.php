<?php
defined('ABSPATH') or exit;
?>

<div id="axeptio-admin" class="wrap axeptio-settings">
    <p class="breadcrumbs">
        <span class="prefix"><?php echo __('You are here: ', 'axeptio'); ?></span>
        <span class="current-crumb"><strong><?php echo __('Axeptio for Wordpress', 'axeptio'); ?></strong></span>
    </p>

    <div class="row">

        <!-- Main Content -->
        <div class="main-content col col-4">

            <h1 class="page-title">
                <?php _e('General Settings', 'axeptio'); ?>
            </h1>

            <h2 style="display: none;"></h2>
            <?php
            settings_errors();
            //$this->messages->show();
            ?>

            <div class="help-box">
                <h2>
                    <?php _e('Quick help', 'axeptio'); ?>
                </h2>
                <div>
                    <?php _e(
                        'To display a consent : copy and paste the shortcode under the consent where you want in your site',
                        'axeptio'
                    ); ?>
                </div>
                <div>
                    <?php _e(
                        'You can add the option "required=1" if the consent is required to validate a form',
                        'axeptio'
                    ); ?>
                </div>
                <div>
                    <?php _e(
                        'For Contact Form 7 You can add the option "wpcf7required=1" if the consent is required to validate a form',
                        'axeptio'
                    ); ?>
                </div>
                <div class="exemple">
                    <div class="title">
                        <?php _e('Exemple : ', 'axeptio'); ?>
                    </div>
                    <div>
                        <span class="subtitle"><?php _e('Basic consent : ', 'axeptio'); ?></span>
                        <?php _e('[axeptio id="2"]', 'axeptio'); ?>
                    </div>
                    <div>
                        <span class="subtitle"><?php _e('Required consent inside a form : ', 'axeptio'); ?></span>
                        <?php _e('[axeptio id="2" required="1"]', 'axeptio'); ?>
                    </div>
                    <div>
                        <span class="subtitle"><?php _e(
                                'Required consent inside a CONTACT FORM 7 form: ',
                                'axeptio'
                            ); ?></span>
                        <?php _e('[axeptio id="2" wpcf7required="1"]', 'axeptio'); ?>
                    </div>
                </div>
            </div>

            <form action="<?php echo admin_url('options.php'); ?>" method="post">
                <?php settings_fields('axeptio_settings'); ?>

                <h3>
                    <?php _e('Available consents list', 'axeptio'); ?>
                </h3>

                <table class="form-table">
                    <div id="axeptio-lang-tabs">
                        <ul>
                            <?php

                            foreach ($configurations as $lang => $configuration) {
                                ?>

                                <li>
                                    <a href="#<?php echo $lang ?>"><?php echo $lang ?></a>
                                </li>

                                <?php
                            }

                            ?>
                        </ul>
                        <?php
                        foreach ($configurations as $lang => $configuration) {
                            ?>
                            <div id="<?php echo $lang ?>">

                                <h4 class="type-title">
                                    <?php _e('Documents', 'axeptio'); ?>
                                </h4>
                                <?php

                                if (!empty($configuration['doc'])) {
                                    foreach ($configuration['doc'] as $consent) {
                                        ?>
                                        <div class="axeptio-container">
                                            <div class="axeptio-input">
                                                <input type="checkbox" name="<?php echo $consent->identifier ?>" data-axeptio='{"lang":"<?php echo $consent->lang ?>","id":"<?php echo $consent->identifier ?>","type":"doc" }'/>
                                            </div>
                                            <div class="axeptio-shortcode">
                                                <label><?php _e('Consent shortcode :', 'axeptio'); ?></label>
                                                [axeptio id="<?php echo $consent->id_axeptio_configuration ?>"]
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    _e('No consents available for this type', 'axeptio');
                                }
                                ?>

                                <h4 class="type-title">
                                    <?php _e('Personal data usages', 'axeptio'); ?>
                                </h4>

                                <?php
                                if (!empty($configuration['pdu'])) {
                                    foreach ($configuration['pdu'] as $consent) {
                                        ?>
                                        <div class="axeptio-container">
                                            <div class="axeptio-input">
                                                <input type="checkbox" name="<?php echo $consent->identifier ?>" data-axeptio='{"lang":"<?php echo $consent->lang ?>","id":"<?php echo $consent->identifier ?>","type":"pdu" }'/>
                                            </div>
                                            <div class="axeptio-shortcode">
                                                <label><?php _e('Consent shortcode :', 'axeptio'); ?></label>
                                                [axeptio id="<?php echo $consent->id_axeptio_configuration ?>"]
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </table>

                <script>
                    window.axeptioSettings = {
                        clientId: "<?php echo esc_attr($client_id) ?>",
                        axeptioApiUrl: "<?php echo AXEPTIO_API_URL ?>",
                        axeptioPlatformUrl: "<?php echo AXEPTIO_PLATFORM_URL ?>",
                        token: 'adm-wp-preview'
                    };
                </script>

                <script src="https://js.axept.io/embed.js"></script>

                <h3>
                    <?php _e('General settings', 'axeptio'); ?>
                </h3>

                <table>
                    <tr valign="top">

                        <th scope="row">
                            <label for="axeptio_client_id"><?php _e(
                                    'CLIENT ID',
                                    'axeptio-for-wp'
                                ); ?></label></th>
                        <td>
                            <input type="text" class="widefat" placeholder="<?php _e(
                                'Your Axeptio client id',
                                'axeptio'
                            ); ?>" id="axeptio_client_id" name="axeptio[client_id]" value="<?php echo esc_attr(
                                $client_id
                            ); ?>"/>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
</div>

