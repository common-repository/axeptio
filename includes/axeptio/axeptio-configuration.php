<?php

class AxeptioConfiguration
{

    /**
     * @var int
     */
    public $id_axeptio_configuration;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $lang;

    /**
     * @var boolean
     */
    public $is_newsletter;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var boolean
     */
    public $is_partner;

    public function __construct($id_axeptio_configuration)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_configuration';
        $configuration = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id_axeptio_configuration = %d", $id_axeptio_configuration)
        );

        foreach ($configuration[0] as $key => $value) {
            if ($key == 'name') {
                $value = str_replace(' ', '', $value);
            }
            $this->$key = $value;
        }

        $this->id = $this->identifier;
    }

    /**
     * Get all consents configuration
     * @return array|bool|null|object
     */
    public static function getAll()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_configuration';
        $myrows = $wpdb->get_results("SELECT * FROM $table_name");

        return (!empty($myrows)) ? $myrows : false;
    }

    /**
     * Get one consent configuration by id
     * @param $identifier
     * @return array|bool|null|object
     */
    public static function get($identifier)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_configuration';
        $myrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE identifier = %s ", $identifier));

        return (!empty($myrows)) ? $myrows : false;
    }

    /**
     * Get all configurations and sort by lang index
     * @return array
     */
    public static function getSortByLang()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_configuration';
        $configurations = $wpdb->get_results("SELECT * FROM $table_name");

        $result = array();
        foreach ($configurations as $configuration) {
            $result[$configuration->lang][$configuration->type][] = $configuration;
        }

        return $result;
    }

    /**
     * Create consent config from API
     * @param array $datas
     */
    public static function createFromApi($datas = array())
    {
        global $wpdb;

        $table_name = $wpdb->prefix.'axeptio_configuration';

        $wpdb->insert(
            $table_name,
            $datas
        );
    }

    /**
     * Update multiple fields
     * @param $datas
     * @param $where
     */
    public static function updateFields($datas, $where)
    {
        global $wpdb;

        $table_name = $wpdb->prefix.'axeptio_configuration';

        $wpdb->update(
            $table_name,
            $datas,
            $where
        );
    }

    /**
     * Reinit consents
     */
    public static function reset()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'axeptio_configuration';
        $wpdb->query("TRUNCATE TABLE $table_name");
    }

    public function getJsonInput()
    {
        $jsonConf = array(
            "lang" => $this->lang,
            "id" => $this->identifier,
            "type" => $this->type,
        );

        return json_encode($jsonConf);
    }
}
