<?php
namespace app\controllers;

use app\components\AppController;
use app\models\Category;
use app\models\search\CategorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;

class CategoryController extends AppController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::class,
            'rules' => [
                [
                    'allow'=>true,
                    'roles'=>['@'],
                ]
            ],
        ];
        return $behaviors;
    }

    public function actionIndex() {
        $this->getView()->title = Yii::t("app", "Categories");

        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render("index", [
            "dataProvider"=>$dataProvider,
            "searchModel"=>$searchModel
        ]);
    }

    public function actionCreate() {
        $model = new Category();
        $model->user_id = Yii::$app->user->id;

        if($model->load(Yii::$app->request->post()) AND $model->save()) {
            Yii::$app->getSession()->setFlash("success", Yii::t("app", "Category has been created"));
            return $this->redirect(["category/index"]);
        }

        $this->getView()->title = Yii::t("app", "Create new category");

        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Categories"),
                'url'=>Yii::$app->getUrlManager()->createUrl("category/index"),
            ],
            [
                'label'=>Yii::t("app", "New category"),
            ]
        ];

        return $this->render("create_edit", [
            "model"=>$model,
        ]);
    }

    public function actionUpdate($id) {
        /**
         * @var $category Category
         */
        $category = $this->loadModel(Category::class, $id);
        $category->user_id = Yii::$app->user->id;

        if($category->load(Yii::$app->request->post()) AND $category->save()) {
            Yii::$app->getSession()->setFlash("success", Yii::t("app", "Category has been updated"));
            return $this->redirect(["category/index"]);
        }

        $this->getView()->title = Yii::t("app", "Update Category | {categoryName}", [
            "categoryName"=>$category->getOldAttribute("title"),
        ]);

        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Categories"),
                'url'=>Yii::$app->getUrlManager()->createUrl("category/index"),
            ],
            [
                'label'=>$this->getView()->title,
            ]
        ];

        return $this->render("create_edit", [
            "model"=>$category,
        ]);
    }

    public function actionView($id) {
        /**
         * @var $category Category
         */
        $category = $this->loadModel(Category::class, $id);
        $this->getView()->title = Yii::t("app", "View Category | {categoryName}", [
            "categoryName"=>$category->getOldAttribute("title"),
        ]);
        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Categories"),
                'url'=>Yii::$app->getUrlManager()->createUrl("category/index"),
            ],
            [
                'label'=>$this->getView()->title,
            ]
        ];
        return $this->render("view", [
            "model"=>$category
        ]);
    }

    public function actionDelete($id) {
        /**
         * @var $category Category
         */
        $category = $this->loadModel(Category::class, $id);
        if($category->delete()) {
            Yii::$app->getSession()->setFlash("success", Yii::t("app", "Category has been deleted."));
        }
        return $this->redirect(["category/index"]);
    }
}