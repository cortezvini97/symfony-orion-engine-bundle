<?php

use Orion\OrionEngine\Utils\OrionSymfony;

OrionSymfony::directive("dump", function($value){
    return '<?php dump('.$value.'); ?>';
});


OrionSymfony::directive("encore_entry_link_tags", function($expression){
    return "<?php echo importMP($expression, 'css') ?>";
});

OrionSymfony::directive("encore_entry_script_tags", function($expression){
    return "<?php echo importMP($expression, 'js') ?>";
});