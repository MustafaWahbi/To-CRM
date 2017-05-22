<?php

    $manifest =array(
        'acceptable_sugar_flavors' => array('CE','PRO','CORP','ENT','ULT'),
        'acceptable_sugar_versions' => array(
            'exact_matches' => array(),
            'regex_matches' => array('(.*?)\\.(.*?)\\.(.*?)$'),
        ),
        'author' => 'KSK Digital',
        'description' => 'This API allows interaction with CRM ',
        'icon' => '',
        'is_uninstallable' => true,
        'name' => 'To CRM interaction',
        'published_date' => '2017-05-015 2017 20:45:04',
        'type' => 'module',
        'version' => '003',
    );
    
    $installdefs =array(
        'id' => 'package_001',
        'copy' => array(
            0 => array(
                'from' => '<basepath>/Files/CRM_ajax.php',
                'to' => 'custom/ToCRM/CRM_ajax.php',
            ),
            1 => array(
                'from' => '<basepath>/Files/suitCRMAPI.php',
                'to' => 'custom/ToCRM/suitCRMAPI.php',
            ),
        ),
    );




    /*
      'logic_hooks' => array(
            array(
                'module' => 'AOS_Products_Quotes',
                'hook' => 'before_save',
                'order' => 1,
                'description' => 'line item update',
                'file' => 'custom/modules/contacts_save.php',
                'class' => 'magentoPush',
                'function' => 'lineItemUpdate',
            ),
        ),
        'logic_hooks' => array(
            array(
                'module' => 'cases',
                'hook' => 'before_save',
                'order' => 2,
                'description' => 'Case creation',
                'file' => 'custom/modules/contacts_save.php',
                'class' => 'magentoPush',
                'function' => 'caseCreation',
            ),
        ),
        'logic_hooks' => array(
            array(
                'module' => 'ksk05_Header3',
                'hook' => 'before_save',
                'order' => 1,
                'description' => 'order update',
                'file' => 'custom/modules/contacts_save.php',
                'class' => 'magentoPush',
                'function' => 'orderUpdate',
            ),
        ),
      */

?>