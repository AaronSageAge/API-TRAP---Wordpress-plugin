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
                                array('label' => 'Email','value' => 'email',),
                                array('label' => 'firstName','value' => 'firstname',),
                                array('label' => 'lastName','value' => 'lastname',),
                                array('label' => 'Phone','value' => 'phone',),
                                array('label' => 'Comments','value' => 'Message',),
                                array('label' => 'CommunityUnique','value' => 'communityunique',),
                                array('label' => 'InquiringFor','value' => 'inquiringfor',),
                                array('label' => 'lovedFirst','value' => 'lovedfirst',),
                                array('label' => 'lovedLast','value' => 'lovedlast',),
                                array('label' => 'utmSource','value' => 'utmsource',),
                                array('label' => 'utmMedium','value' => 'utmmedium',),
                                array('label' => 'utmCampaign','value' => 'utmcampaign',),
                                array('label' => 'utmId', 'value' => 'utmid',),
                                array('label' => 'GCLID','value' => 'gclid'),
                                array('label' => 'Care Level - AL','value' => 'careLevelAL',),
                                array('label' => 'Care Level - IL no expansion','value' => 'careLevelIL',),
                                array('label' => 'Care Level - MS','value' => 'careLevelMS',),
                                array('label' => 'Care Level - SN','value' => 'careLevelSN',),
                                array('label' => 'Care Level - RT', 'value' => 'careLevelRT',),
                                array('label' => 'Care Level - RC','value' => 'careLevelRC'),
                                array('label' => 'Volunteer Inquiry','value' => 'volunteerinquiry',),
                                array('label' => 'Career Inquiry', 'value' => 'careerinquiry',),
                                array('label' => 'Vendor Inquiry', 'value' => 'vendorinquiry'),
                                array('label' => 'Result Residents Cottage', 'value' => 'resultcottage',),
                                array('label' => 'Result Residents Apartment', 'value' => 'resultapartment',),
                                array('label' => 'Result Residents Townhouse', 'value' => 'resulttownhouse',),
                                array('label' => 'Result Residents Apartment', 'value' => 'resultapartment',),
                                array('label' => 'expansionStatus', 'value' => 'expansionstatus',),
                                array('label' => 'MarketSource', 'value' => 'marketsource',),
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
    
        // Mapping Arrays (Make these complete!)
        $communityUniqueMap = [
            '546729' => '546729', // Lake Forest Place
            '546730' => '546730', // Ten Twenty Grove
            '546731' => '546731', // The Moorings
            '546732' => '546732', // Westminster Place
            // ... add all possible values from your form
        ];

        $careLevelMap = [ // This one looks okay
            'Assisted Living' => '762',
            // ... (rest of the care levels)
        ];

        $apartmentPreferenceMap = [
            '28' => '1298', // Example: One Bedroom w/ den Apartment (from Schema.json)
            '29' => '1303', // Example: One bedroom Townhouse (from Schema.json)
            // ... add ALL possible IDs from your form's Apartment Preference field
        ];

        $expansionStatusMap = [
            '547072' => '547072', // 1. Active Interest
            // ... add all other expansion statuses and their IDs
        ];

        $marketSourceMap = [
            '20217' => '20217', // A Place for Mom
            // ... add all other market sources and their IDs
        ];


        // ... (get field values)

        // APPLY MAPPINGS *BEFORE* CHECKING FOR NULL
        $communityunique = isset($metaData['communityunique']) ? $this->get_field_value($form, $entry, $metaData['communityunique']) : null;
        $communityunique = isset($communityUniqueMap[$communityunique]) ? $communityUniqueMap[$communityunique] : null; // Correct mapping

        $CareLevelValue = isset($metaData['careLevelAL']) ? $this->get_field_value($form, $entry, $metaData['careLevelAL']) : null; // Assuming careLevelAL is the primary care level field
        $CareLevelValue = isset($careLevelMap[$CareLevelValue]) ? $careLevelMap[$CareLevelValue] : null;

        $residencePreferenceRaw = $this->get_field_value($form, $entry, $metaData['apartmentpreference']); // Get the raw value
        preg_match_all('/\{(.*?)\}/', $residencePreferenceRaw, $matches); // Extract values within curly braces
        $residencePreferenceValues = $matches[1]; // Array of values like "residencePreference-LakeForest:28"
        $residenceValue = null;
        foreach ($residencePreferenceValues as $value) {
            $parts = explode(':', $value);
            if (isset($parts[1]) && isset($apartmentPreferenceMap[$parts[1]])) {
                $residenceValue = $apartmentPreferenceMap[$parts[1]];
                break; // Stop after finding the first valid mapping
            }
        }

        $expansionstatus = isset($metaData['expansionstatus']) ? $this->get_field_value($form, $entry, $metaData['expansionstatus']) : null;
        $expansionstatus = isset($expansionStatusMap[$expansionstatus]) ? $expansionStatusMap[$expansionstatus] : null;

        $marketsource = isset($metaData['marketsource']) ? $this->get_field_value($form, $entry, $metaData['marketsource']) : null;
        $marketsource = isset($marketSourceMap[$marketsource]) ? $marketSourceMap[$marketsource] : null;

        $email = isset($metaData['email']) ? $this->get_field_value($form, $entry, $metaData['email']) : null;
        $first = isset($metaData['firstname']) ? $this->get_field_value($form, $entry, $metaData['firstname']) : null;
        $last = isset($metaData['lastname']) ? $this->get_field_value($form, $entry, $metaData['lastname']) : null;
        $phone  = isset($metaData['phone']) ? preg_replace('/\D/', '', $this->get_field_value($form, $entry, $metaData['phone'])) : null;
        $comments = isset($metaData['Message']) ? $this->get_field_value($form, $entry, $metaData['Message']) : null;
        $inquiringfor = isset($metaData['inquiringfor']) ? $this->get_field_value($form, $entry, $metaData['inquiringfor']) : null;
        $lovedfirst = isset($metaData['lovedfirst']) ? $this->get_field_value($form, $entry, $metaData['lovedfirst']) : null;
        $lovedlast = isset($metaData['lovedlast']) ? $this->get_field_value($form, $entry, $metaData['lovedlast']) : null;
        $utmsource = isset($metaData['utmsource']) ? $this->get_field_value($form, $entry, $metaData['utmsource']) : null;
        $utmcampaign = isset($metaData['utmcampaign']) ? $this->get_field_value($form, $entry, $metaData['utmcampaign']) : null;
        $utmmedium = isset($metaData['utmmedium']) ? $this->get_field_value($form, $entry, $metaData['utmmedium']) : null;
        $utmid = isset($metaData['utmid']) ? $this->get_field_value($form, $entry, $metaData['utmid']) : null;
        $gclid = isset($metaData['gclid']) ? $this->get_field_value($form, $entry, $metaData['gclid']) : null;

        $primaryContactId = ($inquiringfor == 'Myself') ? 1 : 2;  // 1 for Yes, 2 for No

        $data = array(
            'communityunique' => $communityunique,
            'email' => $email,
            'FirstName' => $first,
            'LastName' => $last,
            'Phone' => $phone,
            'Message' => $comments,
            'lovedfirst' => $lovedfirst,
            'lovedlast' => $lovedlast,
            'utmsource' => $utmsource,
            'utmcampaign' => $utmcampaign,
            'utmmedium' => $utmmedium,
            'utmid' => $utmid,
            'gclid' => $gclid,
            'apartmentpreference' => $residenceValue,
            'expansionstatus' => $expansionstatus,
            'marketsource' => $marketsource,
            'carelevel' => $CareLevelValue,
            'primarycontactid' => $primaryContactId
        );

        error_log('this is the data: ' . print_r($data, true));
        $response = $this->sendApiRequest($data, $inquiringfor);
        error_log('this is the response: ' . print_r($response, true));
    }

    public function sendApiRequest(array $data, $inquiringfor) {
        error_log('API request data: ' . print_r($data, true), 3, plugin_dir_path(__FILE__) . 'debug.log');

        $primaryApiKey = get_option('gravity_api_trap_primary_api_key');
        $secondaryApiKey = get_option('gravity_api_trap_secondary_api_key');
        $url = get_option('gravity_api_trap_endpoint_url');

        $getResponse = wp_remote_get($url . '?email=' . $data['email'], [
            'method' => 'GET',
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $primaryApiKey,
                'Content-Type' => 'application/json',
                'PortalId'     => get_option('gravity_api_trap_portal_id'),
            ]
        ]);

        if (is_wp_error($getResponse)) {
            $getResponse = wp_remote_get($url . '?email=' . $data['email'], [
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

        $existingIndividuals = json_decode(wp_remote_retrieve_body($getResponse), true);

        $existingIndividual = null;
        foreach ($existingIndividuals as $individual) {
            if ($individual['properties'][0]['value'] === $data['email']) { // Check if email matches
                $existingIndividual = $individual;
                break;
            }
        }

        if ($existingIndividual) {
            // Update existing individual
            $sendData = [
                "individuals" => [
                    [
                        "id" => $existingIndividual['id'],
                        "IndividualID" => $existingIndividual['id'], // Add IndividualID
                        "communities" => [
                            ["NameUnique" => $data['communityunique']]
                        ],
                        "properties" => [
                            ["id" => "30323", "value" => $data['FirstName']], 
                            ["id" => "30325", "value" => $data['LastName']], 
                            ["id" => "30326", "value" => $data['Phone']],
                            ["id" => "30330", "value" => $data['email']],
                            ["id" => "30442", "value" => $data['carelevel']],
                            ["id" => "30367", "value" => $data['apartmentpreference']],
                            ["id" => "62897", "value" => $data['expansionstatus']],
                            ["id" => "62908", "value" => $data['utmsource']],
                            ["id" => "62909", "value" => $data['utmmedium']],
                            ["id" => "62910", "value" => $data['utmcampaign']],
                            ["id" => "62911", "value" => $data['utmid']],
                            ["id" => "62912", "value" => $data['gclid']],
                            ["id" => "30463", "value" => $data['marketsource']],
                            ["id" => "30396", "value" => $data['primarycontactid']]
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
                            ["Message" => (string)$data['Message']] // Cast to string
                        ]
                    ],
                    [
                        "communities" => [
                            ["NameUnique" => $data['communityunique']]
                        ],
                        "relationship" => "Family Member",
                        "properties" => [
                            ["id" => "30323", "value" => $data['lovedfirst']],
                            ["id" => "30325", "value" => $data['lovedlast']]
                        ]
                    ]
                ]
            ];
        } else {
            // Create new individual
            $sendData = [
                "individuals" => [
                    [
                        "communities" => [
                            ["NameUnique" => $data['communityunique']]
                        ],
                        "properties" => [
                            ["id" => "30323", "value" => $data['FirstName']], 
                            ["id" => "30325", "value" => $data['LastName']], 
                            ["id" => "30326", "value" => $data['Phone']],
                            ["id" => "30330", "value" => $data['email']],
                            ["id" => "30442", "value" => $data['carelevel']],
                            ["id" => "30367", "value" => $data['apartmentpreference']],
                            ["id" => "62897", "value" => $data['expansionstatus']],
                            ["id" => "62908", "value" => $data['utmsource']],
                            ["id" => "62909", "value" => $data['utmmedium']],
                            ["id" => "62910", "value" => $data['utmcampaign']],
                            ["id" => "62911", "value" => $data['utmid']],
                            ["id" => "62912", "value" => $data['gclid']],
                            ["id" => "30463", "value" => $data['marketsource']],
                            ["id" => "30396", "value" => $data['primarycontactid']]
                        ],
                        "activities" => [
                            [
                                "reInquiry" => false,
                                "description" => "Webform",
                                "activityStatusMasterId" => 2,
                                "activityResultMasterId" => 2,
                                "activityTypeMasterId" => 17
                            ]
                        ],
                        "notes" => [
                            ["Message" => (string)$data['Message']] // Cast to string
                        ]
                    ],
                    [
                        "communities" => [
                            ["NameUnique" => $data['communityunique']]
                        ],
                        "relationship" => "Family Member",
                        "properties" => [
                            ["id" => "30323", "value" => $data['lovedfirst']],
                            ["id" => "30325", "value" => $data['lovedlast']]
                        ]
                    ]
                ]
            ];
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
