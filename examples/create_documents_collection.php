<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 15:57
 */

class create_documents_collection extends Migration
{
    /**
     * create collection with their indexes
     * call create_documents_collection() for creating document collection
     */
    public function __invoke()
    {
        $collection = $this->createCollection('document');
        $this->createIndex($collection->getName(), new \elfuvo\documentStore\migration\Index([
            'name' => 'category_idx',
            'fields' => [
                new \elfuvo\documentStore\migration\IndexField([
                    'name' => '$.category',
                    'type' => \elfuvo\documentStore\migration\IndexField::TYPE_INTEGER,
                    'required' => true,
                    'array' => true,
                ])
            ],
            'unique' => false,
        ]));

        $this->createIndex($collection->getName(), new \elfuvo\documentStore\migration\Index([
            'name' => 'active_idx',
            'fields' => [
                new \elfuvo\documentStore\migration\IndexField([
                    'name' => '$.active',
                    'type' => \elfuvo\documentStore\migration\IndexField::TYPE_SMALLINT,
                    'required' => true,
                ])
            ],
            'unique' => false,
        ]));

        $this->createIndex($collection->getName(), new \elfuvo\documentStore\migration\Index([
            'name' => 'common_idx',
            'fields' => [
                new \elfuvo\documentStore\migration\IndexField([
                    'name' => '$.common',
                    'type' => \elfuvo\documentStore\migration\IndexField::TYPE_SMALLINT,
                    'required' => false,
                ])
            ],
            'unique' => false,
        ]));
    }
}
