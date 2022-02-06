<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-05-17
 * Time: 14:08
 */

namespace elfuvo\documentStore\repository\traits;

/**
 * Class ExistsRecordTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait ExistsRecordTrait
{
    /**
     * check record is exists
     *
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function exists(): bool
    {
        $result = $this
            ->buildQuery()
            ->fields('_id')
            ->limit(1)
            ->execute()
            ->fetchOne();

        return !empty($result);
    }
}
