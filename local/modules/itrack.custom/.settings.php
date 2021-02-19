<?php
return array(
    'controllers' => array(
        'value' => array(
            'namespaces' => array(
                '\\Itrack\\Custom\\Controller' => 'api',
            ),
            'defaultNamespace' => '\\Itrack\\Custom\\Controller',
        ),
        'readonly' => true,
    )
);

/**
 * <script>
 * var request = BX.ajax.runAction('itrack:custom.api.test.example', {
 * data: {
 * param1: 'hhh'
 * }
 * });
 *
 * request.then(function(response){
 * console.dir(response);
 * });
 * </script>
 */