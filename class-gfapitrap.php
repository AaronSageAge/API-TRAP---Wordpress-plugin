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
                                    'label'         => 'Care Level',
                                    'value'         => 'careLevel',
                                ),
                                array(
                                    'label'         => 'apartmentPreference',
                                    'value'         => 'apartmentpreference',
                                ),
                                array(
                                    'label'         => 'expansionStatus',
                                    'value'         => 'expansionstatus',
                                ),
                                array(
                                    'label'         => 'MarketSource',
                                    'value'         => 'marketsource',
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
        error_log('Feed data: ' . print_r($feed, true), 3, plugin_dir_path(__FILE__) . 'debug.log');

        var_dump($feed);
        error_log('this is the feed:');
        error_log(print_r($feed, true));
        $metaData = $this->get_generic_map_fields( $feed, 'formFieldMap' );
    
        $communityunique = isset($metaData['communityunique']) ? $this->get_field_value($form, $entry, $metaData['communityunique']) : null;
    
        $email = isset($metaData['email']) ? $this->get_field_value($form, $entry, $metaData['email']) : null;
    
        $first = isset($metaData['firstname']) ? $this->get_field_value($form, $entry, $metaData['firstname']) : null;
    
        $last = isset($metaData['lastname']) ? $this->get_field_value($form, $entry, $metaData['lastname']) : null;
    
        $phone  = isset($metaData['phone']) ? preg_replace('/\D/', '', $this->get_field_value($form, $entry, $metaData['phone'])) : null;
    
        $comments = isset($metaData['Message']) ? $this->get_field_value($form, $entry, $metaData['Message']) : null;
    
/*Interest in*/
$careLevelMap = array(
    25 => 'LakeForest',
    26 => 'Moorings',
    9 => 'Westminster',
    27 => 'TTG'
);

$careLevel = null;
foreach ($metaData as $meta) {
    if (is_array($meta) && isset($meta['key']) && $meta['key'] == 'careLevel') {
        $careLevels = explode(' ', $meta['custom_value']);
        $careLevelValues = array();
        foreach ($careLevels as $careLevel) {
            $parts = explode(':', $careLevel);
            $careLevelId = trim($parts[1], '{}');
            if (isset($careLevelMap[$careLevelId])) {
                $careLevelValues[] = $careLevelMap[$careLevelId];
            }
        }
        $careLevel = implode(', ', $careLevelValues);
        break;
    }
}


$data['CareLevel'] = $careLevel;

error_log('Care Level metadata: ' . print_r($metaData, true), 3, plugin_dir_path(__FILE__) . 'debug.log');
error_log('Selected care level: ' . $careLevel, 3, plugin_dir_path(__FILE__) . 'debug.log');

if (empty($careLevel)) {
    error_log('Care Level is empty', 3, plugin_dir_path(__FILE__) . 'debug.log');
}

$excludedValues = array('Volunteer Inquiries', 'Career Inquiries', 'Vendor Inquiries');
foreach ($careLevelValues as $value) {
    if (in_array($value, $excludedValues)) {
        error_log('Skipping API request due to excluded interest In value: Career, Volunteer, or Vendor' . $value);
        return;
    }
}
        /*prospect or contact into type*/
        $inquiringfor = isset($metaData['inquiringfor']) ? $this->get_field_value($form, $entry, $metaData['inquiringfor']) : null;



    /*prospect or contact change based on inquiring for*/
    /*if ($inquiringfor == 'Myself') {
        $individualType = 'Prospect';
        $relationshipType = 'Contact';
    } elseif ($inquiringfor == 'A Loved One') {
        $individualType = 'Contact';
        $relationshipType = 'Prospect';
    } else {
        $individualType = 'Prospect';
        $relationshipType = 'Contact';
    }*/

        /*if contact/loved one*/
        $lovedfirst = isset($metaData['lovedfirst']) ? $this->get_field_value($form, $entry, $metaData['lovedfirst']) : null;
        $lovedlast = isset($metaData['lovedlast']) ? $this->get_field_value($form, $entry, $metaData['lovedlast']) : null;
    
        /*utm*/
        $utmsource = isset($metaData['utmsource']) ? $this->get_field_value($form, $entry, $metaData['utmsource']) : null;
        $utmcampaign = isset($metaData['utmcampaign']) ? $this->get_field_value($form, $entry, $metaData['utmcampaign']) : null;
        $utmmedium = isset($metaData['utmmedium']) ? $this->get_field_value($form, $entry, $metaData['utmmedium']) : null;
        $utmid = isset($metaData['utmid']) ? $this->get_field_value($form, $entry, $metaData['utmid']) : null;
        $gclid = isset($metaData['gclid']) ? $this->get_field_value($form, $entry, $metaData['gclid']) : null;
    
        $apartmentpreference = isset($metaData['apartmentpreference']) ? $this->get_field_value($form, $entry, $metaData['apartmentpreference']) : null;
        $expansionstatus = isset($metaData['expansionstatus']) ? $this->get_field_value($form, $entry, $metaData['expansionstatus']) : null;

        $marketsource = isset($metaData['marketsource']) ? $this->get_field_value($form, $entry, $metaData['marketsource']) : null;

        $data = array(
            'communityunique' => $communityunique,
            'email' => $email,
            'FirstName' => $first,
            'LastName' => $last,
            'Phone' => $phone,
            'Message' => $comments,
            'CareLevel' => $careLevel,
            'lovedfirst' => $lovedfirst,
            'lovedlast' => $lovedlast,
            'utmsource' => $utmsource,
            'utmcampaign' => $utmcampaign,
            'utmmedium' => $utmmedium,
            'utmid' => $utmid,
            'gclid' => $gclid,
            'apartmentpreference' => $apartmentpreference,
            'expansionstatus' => $expansionstatus,
            'marketsource' => $marketsource
        );
    
        error_log('this is the data: ' . print_r($data, true));
        $response = $this->sendApiRequest($data, $inquiringfor, $individualType, $relationshipType);
        error_log('this is the response: ' . print_r($response, true));
    }

    public function sendApiRequest(array $data, $inquiringfor, $individualType, $relationshipType) {
        error_log('API request data: ' . print_r($data, true), 3, plugin_dir_path(__FILE__) . 'debug.log');

        $sendData = [
            "individuals" => [
                [
                    "communities" => [
                        [
                            "NameUnique" => $data['communityunique'],
                        ]
                    ],
                    "properties" => [
                        [
                            "property" => "firstname",
                            "value" => $data['FirstName']
                        ], [
                            "property" => "lastname",
                            "value" => $data['LastName']
                        ], [
                            "property" => "Email",
                            "value" => $data['email']
                        ], [
                            "property" => "Home Phone",
                            "value" => $data['Phone']
                        ],[
                            "property" => "Care Level", 
                            "value" => $data['CareLevel']
                        ],[
                            "property" => "type",
                           "value" => "Contact"
                        ],[
                            "property" => "utmSource",
                            "value" => $data['utmsource']
                        ],[
                            "property" => "UTM Campaign",
                            "value" => $data['utmcampaign']
                        ],[
                            "property" => "UTM Medium",
                            "value" => $data['utmmedium']
                        ],[
                            "property" => "UTM Id",
                            "value" => $data['utmid']
                        ],[
                            "property" => "GCLID",
                            "value" => $data['gclid']
                        ],[
                            "property" => "Apartment Preference",
                            "value" => $data['apartmentpreference']
                        ],[
                            "property" => "Market Source",
                            "value" => $data['marketsource']
                        ]
                    ],
                    "activities" => [
                        [
                            "reInquiry" => true,
                            "description" => "Webform",
                            "activityStatusMasterId" => 2,
                            "activityResultMasterId" => 2,
                            "activityTypeMasterId" => 17
                        ]
                    ],
                    "notes" => [
                        [
                            "Message" => (string)$data['Message'], // Cast to string
                        ]
                    ]
                ],
                [
                    "communities" => [
                        [
                            "NameUnique" => $data['communityunique'],
                        ]
                    ],
                    "relationship" => "Family Member",
                    "properties" => [
                        [
                            "property" => "Expansion Status",
                            "value" => $data['expansionstatus']
                        ],
                        [
                            "property" => "firstname",
                            "value" => $data['lovedfirst']
                        ],
                        [
                            "property" => "lastname",
                            "value" => $data['lovedlast']
                        ],
                        [
                            "property" => "type",
                            "value" => "Prospect"
                        ]
                    ]
                ]
            ]
        ];
        $primaryApiKey = get_option('gravity_api_trap_primary_api_key');
        $secondaryApiKey = get_option('gravity_api_trap_secondary_api_key');
        $url = get_option('gravity_api_trap_endpoint_url');
    
        $getResponse = wp_remote_get($url, [
            'method' => 'GET',
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $primaryApiKey,
                'Content-Type' => 'application/json',
                'PortalId'     => get_option('gravity_api_trap_portal_id'),
            ]
        ]);
    
        if (is_wp_error($getResponse)) {
            $getResponse = wp_remote_get($url, [
                'method' => 'GET',
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $secondaryApiKey,
                    'Content-Type' => 'application/json',
                    'PortalId'     => get_option('gravity_api_trap_portal_id'),
                ]
            ]);
        }

        if (is_wp_error($getResponse)) {
            error_log('API request failed: ' . $getResponse->get_error_message(), 3, plugin_dir_path(__FILE__) . 'debug.log');
            return;
        }
        
        $existingData = json_decode($getResponse['body'], true);
    
        if ($existingData === null) {
            error_log('Invalid response from API', 3, plugin_dir_path(__FILE__) . 'debug.log');
            return;
        } else {
            foreach ($sendData["individuals"]["properties"] as $property) {
                if (isset($existingData["individuals"]["properties"][$property["property"]]) && $existingData["individuals"]["properties"][$property["property"]] != $property["value"]) {
                    $sendData["individuals"]["comments"][] = "Changed " . $property["property"] . " from " . $existingData["individuals"]["properties"][$property["property"]] . " to " . $property["value"];
                }
            }

                $args = [
                    'method' => 'POST',
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $primaryApiKey,
                        'Content-Type' => 'application/json',
                        'PortalId'     => get_option('gravity_api_trap_portal_id'),
                    ],
                    'body' => json_encode($sendData)
                ];
    
            error_log('API request JSON data: ' . json_encode($sendData, JSON_PRETTY_PRINT), 3, plugin_dir_path(__FILE__) . 'debug.log');


            $response = wp_remote_post($url, $args);
    
            if (is_wp_error($response)) {
                $args['headers']['Ocp-Apim-Subscription-Key'] = $secondaryApiKey;
                $response = wp_remote_post($url, $args);
            }
    
            if (is_wp_error($response)) {
                error_log('API request failed: ' . $response->get_error_message(), 3, plugin_dir_path(__FILE__) . 'debug.log');
                return;
            }

            $responseCode = wp_remote_retrieve_response_code($response);
            $responseBody = wp_remote_retrieve_body($response);
            
            if ($responseCode === 200) {
                error_log('API request successful: ' . $responseCode . ' - ' . $responseBody, 3, plugin_dir_path(__FILE__) . 'debug.log');
            } else {
                error_log('API request failed: ' . $responseCode . ' - ' . $responseBody, 3, plugin_dir_path(__FILE__) . 'debug.log');
            }
    
            return $response;
        }
    }
}