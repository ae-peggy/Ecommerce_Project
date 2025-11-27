<?php
require_once '../settings/core.php';

/**
 * The legacy search page now delegates to the unified product listing so that
 * all filters (search, price range, sorting, best sellers, etc.) stay in sync.
 */
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'all_product.php';

if (!empty($queryString)) {
    $target .= '?' . $queryString;
}

header('Location: ' . $target);
exit;

