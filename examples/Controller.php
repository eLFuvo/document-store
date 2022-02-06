<?php

use elfuvo\documentStore\connection\Connection;
use elfuvo\documentStore\Expression;

/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-24
 * Time: 15:05
 */
class Controller
{
    /**
     * @throws \elfuvo\documentStore\Exception
     */
    public function actionIndex()
    {
        $list = $this->getRepository()
            ->select([
                '*',
                new Expression('AVG(rating) as rating'),
                new Expression('IF(titleEn > \'\', titleEn, title) as title')
            ])
            ->active()
            ->category([1, 2, 3])
            ->andExprWhere(new Expression('789 in order'))
            ->andGroupWhere('or', [
                new Expression('CONCAT_WS("-", cte, olid) = :code1', [':code1' => 'CTE-KL90N78']),
                new Expression('CONCAT_WS("-", cte, olid) = :code2', [':code2' => 'CTE-KL90A34'])
            ])->groupBy([
                'uid'
            ])
            ->all();

        return $this->render('index', ['list' => $list]);
    }


    /**
     * @throws \elfuvo\documentStore\Exception
     */
    public function actionView(string $id)
    {
        $document = $this->getRepository()
            ->andWhere('=', '_id', $id)
            ->active()
            ->one();

        return $this->render('view', ['document' => $document]);
    }

    /**
     * @return DocumentRepository
     */
    protected function getRepository(): DocumentRepository
    {
        $repository = (new DocumentCollection)->getRepository();
        $repository::setDb(new Connection([
            'database' => 'project',
            'username' => 'user',
            'password' => '123',
        ]));

        return $repository;
    }
}
