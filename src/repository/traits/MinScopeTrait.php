<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 10:45
 */

namespace elfuvo\documentStore\repository\traits;

/**
 * Class MinScopeTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait MinScopeTrait
{
    /**
     * @param string $column
     * @return string|null
     */
    public function min(string $column): ?string
    {
        $result = $this
            ->buildQuery()
            ->fields('MIN(' . $column . ') as agg')
            ->execute()
            ->fetchOne();

        return isset($result['agg']) ? (string)$result['agg'] : null;
    }
}
