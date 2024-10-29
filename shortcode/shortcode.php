<?php

/**
 * Generate basic Axeptio Shortcode
 *
 * @param array $params
 * @param $more
 * @return string
 */
function axeptio_shortcode($params = array())
{
    $input = '';
    if ($params['id']) {
        $axeptioConfiguration = new AxeptioConfiguration($params['id']);

        if (!empty($params['wpcf7required']) && $params['wpcf7required'] == 1) {
            $input = "<span class='wpcf7-form-control wpcf7-acceptance'><input class='wpcf7acceptance' id=".$axeptioConfiguration->name." type='checkbox' data-identifier=".$axeptioConfiguration->id_axeptio_configuration." name=".$axeptioConfiguration->name." data-axeptio = ".json_encode(
                    $axeptioConfiguration
                )." required data-wpcf7-acceptance='1'></span>";
        } elseif (!empty($params['required']) && $params['required'] == 1) {
            $input = "<input type='checkbox' name=".$axeptioConfiguration->name." data-axeptio = ".json_encode(
                    $axeptioConfiguration
                )." required>";
        } else {
            $input = "<input type='checkbox' name=".$axeptioConfiguration->name." data-axeptio = ".json_encode(
                    $axeptioConfiguration
                ).">";
        }
    }

    return $input;
}