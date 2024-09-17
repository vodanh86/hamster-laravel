<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Skin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('en_name', __('Name(English)'));
        $grid->column('description', __('Description'));
        $grid->column('en_description', __('Description(English)'));
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
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('en_name', __('Name(English)'));
        $show->field('description', __('Description'));
        $show->field('en_description', __('Description(English)'));
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
        $form = new Form(new Category());
        $form->embeds('name', function ($form) {
            $form->text('en')->rules('required');
            $form->text('vi')->rules('required');
        });
        $form->text('en_name', __('Name(English)'));
        $form->text('description', __('Description'))->required();
        $form->text('en_description', __('Description(English)'));
        $form->number('order', __('Order'));

        return $form;
    }
}
