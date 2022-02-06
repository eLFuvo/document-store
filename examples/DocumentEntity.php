<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 12:29
 */

/**
 * Class DocumentEntity
 */
class DocumentEntity extends \elfuvo\documentStore\entity\AbstractEntity
{
    public $title = '';
    public $description = '';
    public $rating = 0;
    public $category;
    public $url = '';
}
