<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 10:43
 */

namespace elfuvo\documentStore\repository\traits;

/**
 * Trait MaxScopeTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait MaxScopeTrait
{
    /**
     * @param string $column
     * @return string|null
     */
    public function max(string $column): ?string
    {
        $result = $this
            ->buildQuery()
            ->fields('MAX(' . $column . ') as agg')
            ->execute()
            ->fetchOne();

        return isset($result['agg']) ? (string)$result['agg'] : null;
    }
}
