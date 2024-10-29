<?php

class AxeptioUser
{

    /**
     * @var int
     */
    public $id_axeptio_user;

    /**
     * @var int
     */
    public $id_user;

    /**
     * @var string
     */
    public $token;

    public function __construct($id_axeptio_user)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_user';
        $configuration = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id_axeptio_user = %d", $id_axeptio_user)
        );

        foreach ($configuration[0] as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get all users
     *
     * @return array|bool|null|object
     */
    public static function getAll()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_user';
        $myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name"));

        return (!empty($myrows)) ? $myrows : false;
    }


    /**
     * Get user by wp id
     *
     * @param $identifier
     * @return array|bool|null|object
     */
    public static function getByWpId($id_user)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_user';
        $myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id_user = %d", $id_user));

        return (!empty($myrows)) ? $myrows : false;
    }
}
