<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 12:01
 */

class DocumentRepository extends \elfuvo\documentStore\repository\AbstractRepository
{
    public const ACTIVE = 1;
    public const COMMON = 1;

    /**
     * @return DocumentRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function active(): self
    {
        return $this->andWhere('=', 'active', self::ACTIVE);
    }

    /**
     * @param array $category
     * @return DocumentRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function category(array $category): self
    {
        return $this->andGroupWhere('or', [
            ['in', 'category', implode(',', $category)],
            ['=', 'common', self::COMMON]
        ]);
    }
}
