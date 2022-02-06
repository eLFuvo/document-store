<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 11:59
 */

class DocumentCollection extends \elfuvo\documentStore\collection\AbstractCollection
{
    /**
     * @var string
     */
    protected string $entityClass = DocumentEntity::class;

    /**
     * @return string
     */
    public static function collectionName(): string
    {
        return 'document';
    }

    /**
     * @return DocumentRepository
     */
    public function getRepository(): DocumentRepository
    {
        if (!$this->repository) {
            $this->repository = new DocumentRepository($this);
        }

        return $this->repository;
    }
}
