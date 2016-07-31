<?php 
require '../vendor/autoload.php';

$db = new ezSQL_mysqli('root','root','greyareas','localhost');

$computeCategories = function($postcode) use ($db) {
    $counts = $db->get_row("SELECT * FROM dss_demographics WHERE postcode = '$postcode'");
    if (!$counts) {
        echo "no pensioners in " . $postcode . "\n";
        return;
    }
    
    $seniors = is_numeric($counts->age_pension) ? (int) $counts->age_pension : 0; 
    $healthcare = is_numeric($counts->seniors_health_card) ? (int) $counts->seniors_health_card : 0; 

    //if we have more senior helthcare card holders we'll use that
    if ($healthcare < $seniors) {
        $pensioners = $seniors;
    } else {
        $pensioners = $healthcare;
    }

    $facilities = $db->get_results("SELECT * FROM community_facilities WHERE postcode = '{$postcode}'; ");

    if (empty($facilities) || count($facilities) == 0) {
        echo "no facilities in " . $postcode . "\n";
        return;
    }

    $categories = [
        'cultural' => 0,
        'social' => 0,
        'connected' => 0,
        'active' => 0,
        'economic' => 0
    ];

    $facilitiesMap = [
        'Museum' => ['cultural'],                          
        'Place of Worship' => ['connected', 'social'],                 
        'Court House' => ['connected', 'social'],         
        'Theatre' => ['connected', 'social'],              
        'Tourist Information Centre' => ['connected', 'social'],       
        'Government' => ['connected'], 
        'Art Gallery' => ['cultural'],               
        'Convention Entertainment Centre' => ['connected', 'social'],  
        'Surf Life Saving Clubhouse' => ['active', 'social'], 
        'Post Office' => ['connected'], 
        'Community Hall' => ['connected', 'social'],              
        'Library' => ['connected', 'cultural']
    ];

    foreach ($facilities as $facility) {
        if (!array_key_exists($facility->FEATURETYPE, $facilitiesMap)) {
            continue;
        }			

        $cats = $facilitiesMap[$facility->FEATURETYPE];

        foreach ($cats as $cat) {
            $categories[$cat]++;
        }
    }

    foreach ($categories as $key => $count) {
        $categories[$key] = $count / ceil($pensioners / 1000) ;
    }

    $discounts = $db->get_row("select count(id) as discounts from business_discounts where Outlet_Postcode = '$postcode'");

    if (!empty($discounts) || count($discounts) != 0) {
        $categories['economic'] += $discounts->discounts;
    }

    $db->query(sprintf("
        insert into postcode_scores (postcode, cultural, social, connected, active, economic, average) 
        values ('%s', %d, %d, %d, %d, %d, %d)
    ", $postcode, $categories['cultural'], $categories['social'], $categories['connected'], $categories['active'], $categories['economic'],
        ($categories['cultural'] + $categories['social'] + $categories['connected'] + $categories['active'] + $categories['economic']) / 5
    ));
};

$postcodes = $db->get_results("SELECT * FROM postcodes WHERE poa_code LIKE '4%' ");

foreach ($postcodes as $postcode) {
    $computeCategories($postcode->poa_code);
}