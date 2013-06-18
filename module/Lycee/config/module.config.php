<?php
return array (
    'caches' => array (
        // doesn't have to look FQNS, but I think I heard it's good practise
        'Lycee\Cache' => array (
            'adapter' => 'filesystem',
            'ttl' => 88400,
            'cache_dir' => './data/cache/lycee',
            'dir_permission' => 0777,
            'file_permission' => 0666,
        )
    )
);
