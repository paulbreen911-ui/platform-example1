<?php
// TEMPORARY DEBUG FILE — DELETE AFTER USE
// Visit: https://www.productioncentral.org/debug_env.php

$vars = ['R2_ACCOUNT_ID', 'R2_API_TOKEN', 'R2_ACCESS_KEY', 'R2_SECRET_KEY', 'R2_BUCKET', 'R2_PUBLIC_URL'];

echo '<pre>';
foreach ($vars as $v) {
    $val = getenv($v);
    echo $v . ': ' . ($val ? substr($val, 0, 6) . '... (' . strlen($val) . ' chars)' : 'NOT FOUND') . "\n";
}
echo '</pre>';
