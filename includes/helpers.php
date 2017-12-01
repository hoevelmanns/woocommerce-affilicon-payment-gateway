<?php

/**
 * @param WC_Product $item
 * @param string $key
 * @return bool
 */
function getMetaDataValue($item, $key)
{
    foreach ($item->get_meta_data() as $meta) {
        if ($meta->key === $key) {
            return $meta->value;
        }
    }
    return false;
}