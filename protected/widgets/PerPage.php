<?php
namespace app\widgets;

use yii\base\Widget;
use yii\data\Pagination;
use Yii;

class PerPage extends Widget
{
    /**
     * @var Pagination
     */
    public $pagination;
    public $perPage = [
        10, 20, 25, 35, 50
    ];
    // vertical | horizontal
    public $layout = 'vertical';

    public function init() {
        if(!$this->pagination instanceof Pagination) {
            throw new \InvalidArgumentException('Pagination must be an instance of yii\data\Pagination');
        }
    }

    public function run() {
        $onPage = Yii::$app->request->get($this->pagination->pageSizeParam, $this->pagination->pageSize);
        return $this->render("//widgets/per-page", [
            "pagination"=>$this->pagination,
            "perPage"=>$this->perPage,
            "onPage"=>$onPage,
        ]);
    }
}