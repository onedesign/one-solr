<?php

return [
    'credentials' => [
        'host' => getenv('SOLR_HOST'),
        'port' => getenv('SOLR_PORT'),
        'path' => getenv('SOLR_PATH'),
        'core' => getenv('SOLR_CORE')
    ]
];
