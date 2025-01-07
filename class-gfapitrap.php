<?php

defined( 'ABSPATH' ) || die();

// Load Feed Add-On Framework.
GFForms::include_feed_addon_framework();

class GFAPITrap extends GFFeedAddOn {
 
    protected $_version = GF_API_TRAP_VERSION;
    protected $_min_gravityforms_version = '2.8';
    protected $_slug = 'gravity-api-trap';
    protected $_path = 'gravity-api-trap/gravity-api-trap.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Enquire/Aline Integration';
    protected $_short_title = 'Enquire/Aline API';
 
    private static $_instance = null;
 
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new GFAPITrap();
        }
 
        return self::$_instance;
    }

    public function plugin_page() {
    }

    public function feed_settings_fields() {
        return array(
            array(
                'title'  => esc_html__( 'Enquire/Aline Settings', 'gravity-api-trap' ),
                'fields' => array(
                    array(
                        'name'                => 'feedName',
                        'label'               => '<h3>Feed Label</h3>',
                        'type'                => 'text',
                    ),
                    array(
                        'name'                => 'formFieldMap',
                        'label'               => '<h3>' . esc_html__( 'Map API Fields to Form Fields', 'gravity-api-trap' ) . '</h3>',
                        'type'                => 'generic_map',
                        'key_field'           => array(
                            'title'             => 'API Field',
                            'allow_custom'      => FALSE,
                            'choices'           => array(
                                array(
                                    'label'         => 'Email',
                                    'value'         => 'email',
                                ),
                                array(
                                    'label'         => 'firstName',
                                    'value'         => 'firstname',
                                ),
                                array(
                                    'label'         => 'lastName',
                                    'value'         => 'lastname',
                                ),
                                array(
                                    'label'         => 'Phone',
                                    'value'         => 'phone',
                                ),
                                array(
                                    'label'         => 'Comments',
                                    'value'         => 'Message',
                                ),
                                array(
                                    'label'         => 'CommunityUnique',
                                    'value'         => 'communityunique',
                                ),
                                array(
                                    'label'         => 'InquiringFor',
                                    'value'         => 'inquiringfor',
                                ),
                                array(
                                    'label'         => 'lovedFirst',
                                    'value'         => 'lovedfirst',
                                ),
                                array(
                                    'label'         => 'lovedLast',
                                    'value'         => 'lovedlast',
                                ),
                                array(
                                    'label'         => 'utmSource',
                                    'value'         => 'utmsource',
                                ),
                                array(
                                    'label'         => 'utmMedium',
                                    'value'         => 'utmmedium',
                                ),
                                array(
                                    'label'         => 'utmCampaign',
                                    'value'         => 'utmcampaign',
                                ),
                                array(
                                    'label'         => 'utmId',
                                    'value'         => 'utmid',
                                ),
                                array(
                                    'label'         => 'GCLID',
                                    'value'         => 'gclid',
                                ),
                                array(
                                    'label'         => 'careLevel',
                                    'value'         => 'carelevel',
                                ),
                                array(
                                    'label'         => 'apartmentPreference',
                                    'value'         => 'apartmentpreference',
                                ),
                                array(
                                    'label'         => 'expansionStatus',
                                    'value'         => 'expansionstatus',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }

    public function feed_list_columns() {
        return array(
            'feedName' => __( 'Name', 'gravity-api-trap' ),
        );
    }

    public function process_feed( $feed, $entry, $form ) {
        var_dump($feed);
        error_log('this is the feed:');
        error_log(print_r($feed, true));
        $metaData = $this->get_generic_map_fields( $feed, 'formFieldMap' );
    
        $communityunique = isset($metaData['communityunique']) ? $this->get_field_value($form, $entry, $metaData['communityunique']) : null;
    
        $email = isset($metaData['email']) ? $this->get_field_value($form, $entry, $metaData['email']) : null;
    
        $firstName = isset($metaData['firstname']) ? $this->get_field_value($form, $entry, $metaData['firstname']) : null;
    
        $lastName = isset($metaData['lastname']) ? $this->get_field_value($form, $entry, $metaData['lastname']) : null;
    
        $phone  = isset($metaData['phone']) ? $this->get_field_value($form, $entry, $metaData['phone']) : null;
    
        $comments = isset($metaData['Message']) ? GFCommon::replace_variables($metaData['Message'], $form, $entry) : null;
    
        /*Interest in*/
        $carelevel= isset($metaData['carellevel']) ? $this->get_field_value($form, $entry, $metaData['carelevel']) : null;
    
        // Check if interestIn is one of the excluded values
        $excludedValues = array('Volunteer Inquiries', 'Career Inquiries', 'Vendor Inquiries');
        if (in_array($carelevel, $excludedValues)) {
            error_log('Skipping API request due to excluded interest In value: Career, Volunteer, or Vendor' . $carelevel);
            return;
        }
    
        /*prospect or contact into type*/
        $inquiringfor = isset($metaData['inquiringfor']) ? $this->get_field_value($form, $entry, $metaData['inquiringfor']) : null;

        $type = ($inquiringfor == 'self') ? 'prospect' : 'Contact';

        $lovedfirst = isset($metaData['lovedfirst']) ? $this->get_field_value($form, $entry, $metaData['lovedfirst']) : null;
        $lovedlast = isset($metaData['lovedlast']) ? $this->get_field_value($form, $entry, $metaData['lovedlast']) : null;
    
        $utmsource = isset($metaData['utmsource']) ? $this->get_field_value($form, $entry, $metaData['utmsource']) : null;
        $utmcampaign = isset($metaData['utmcampaign']) ? $this->get_field_value($form, $entry, $metaData['utmcampaign']) : null;
        $utmmedium = isset($metaData['utmmedium']) ? $this->get_field_value($form, $entry, $metaData['utmmedium']) : null;
        $utmid = isset($metaData['utmid']) ? $this->get_field_value($form, $entry, $metaData['utmid']) : null;
        $gclid = isset($metaData['gclid']) ? $this->get_field_value($form, $entry, $metaData['gclid']) : null;
    
        $apartmentpreference = isset($metaData['apartmentpreference']) ? $this->get_field_value($form, $entry, $metaData['apartmentpreference']) : null;
        $expansionstatus = isset($metaData['expansionstatus']) ? $this->get_field_value($form, $entry, $metaData['expansionstatus']) : null;
    
        $data = array(
            'communityunique' => $communityunique,
            'email' => $email,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'Phone ' => $phone ,
            'Message' => $comments,
            'CareLevel' => $carelevel,
            'lovedfirst' => $lovedfirst,
            'lovedlast' => $lovedlast,
            'utmsource' => $utmsource,
            'utmcampaign' => $utmcampaign,
            'utmmedium' => $utmmedium,
            'utmid' => $utmid,
            'gclid' => $gclid,
            'apartmentpreference' => $apartmentpreference,
            'expansionstatus' => $expansionstatus,
            'type' => $type,
        );
    
        error_log('this is the data: ' . print_r($data, true));
        $response = $this->sendApiRequest($data);
        error_log('this is the response: ' . print_r($response, true));
    }

    public function sendApiRequest(array $data, $inquiringFor) {

        $sendData = array(
            "individuals" => [
                "communities" => [
                    "NameUnique" => $data['communityunique'],
                ],
                "properties" => [
                    [
                        "property" => "firstname",
                        "value" => $data['firstname']
                    ], [
                        "property" => "lastname",
                        "value" => $data['lastname']
                    ], [
                        "property" => "email",
                        "value" => $data['email']
                    ], [
                        "property" => "Phone",
                        "value" => $data['phone']
                    ],[
                        "property" => "CareLevel", 
                        "value" => $data['carelevel']
                    ],[
                        "property" => "type",
                        "value" => $data['type']
                    ],[
                        "property" => "lovedfirst",
                        "value" => $data['lovedfirst']
                    ],[
                        "property" => "lovedlast", 
                        "value" => $data['lovedlast'] 
                    ],[
                        "property" => "utmSource",
                        "value" => $data['utmsource']
                    ],[
                        "property" => "utmCampaign",
                        "value" => $data['utmcampaign']
                    ],[
                        "property" => "utmMedium",
                        "value" => $data['utmmedium']
                    ],[
                        "property" => "utmId",
                        "value" => $data['utmid']
                    ],[
                        "property" => "gclid",
                        "value" => $data['gclid']
                    ],[
                        "property" => "apartmentPreference",
                        "value" => $data['apartmentpreference']
                    ],[
                        "property" => "expansionStatus",
                        "value" => $data['exponsionstatus']
                    ],
                ],
                "notes"  => [
                    "Message" => $data['Message'],
                ]
            ]
        );

        $primaryApiKey = get_option('gravity_api_trap_primary_api_key');
        $secondaryApiKey = get_option('gravity_api_trap_secondary_api_key');
        $url = get_option('gravity_api_trap_endpoint_url');

        $args = [
            'method' => 'POST',
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $primaryApiKey,
                'Content-Type' => 'application/json',
                'PortalId'     => get_option('gravity_api_trap_portal_id'),
            ],
            'body' => json_encode($sendData)
        ];

        error_log('args: ' . print_r($args, true));

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $args['headers']['Ocp-Apim-Subscription-Key'] = $secondaryApiKey;
            $response = wp_remote_post($url, $args);
        }

        if (is_wp_error($response)) {
            // Display an error message to the user
            add_settings_error('gravity-api-trap', 'api_request_error', 'API request failed: ' . $response->get_error_message(), 'error');
        } elseif ($response['response']['code'] !== 200) {
            // Display an error message to the user
            add_settings_error('gravity-api-trap', 'api_request_error', 'API request failed with status code ' . $response['response']['code'], 'error');
        } else {
            // Log the entire output
            error_log('API request successful: ' . print_r($response, true));
        }

        return $response;
    }

}

