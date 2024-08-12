<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\Skin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CardController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Card';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Card());

        $grid->column('id', __('Id'));
        $grid->column('category.name', __('Category'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('image', __('Image'))->image();
        $grid->column('order', __('Order'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->model()->orderBy('order', 'asc');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Card::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category.name', __('Category'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'))->image();
        $show->field('order', __('Order'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $categoryOptions = (new UtilsQueryHelper())::getAllCategories();
        $categoryDefault = $categoryOptions->keys()->first();

        $form = new Form(new Card());

        if ($form->isEditing()) {
            $id = request()->route()->parameter('card');
            $parentId = $form->model()->find($id)->getOriginal("category_id");

            $form->select('category_id', __('Category'))->options($categoryOptions)->default($parentId);
        } else {
            $form->select('category_id', __('Category'))->options($categoryOptions)->default($categoryDefault);
        }

        $form->text('name', __('Name'));
        $form->text('description', __('Description'));
        $form->image('image', __('Image'))->move("images/cards");
        $form->number('order', __('Order'));

        return $form;
    }
}
