<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\Earn;
use App\Models\Skin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EarnController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Earn';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Earn());

        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('link', __('Link'));
        $grid->column('reward', __('Reward'));
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
        $show = new Show(Earn::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('link', __('Link'));
        $show->field('reward', __('Reward'));
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

        $form = new Form(new Earn());

        if ($form->isEditing()) {
            $id = request()->route()->parameter('earn');

        }

        $form->number('type', __('Type'));
        $form->text('name', __('Name'));
        $form->text('description', __('Description'));
        $form->text('link', __('Link'));
        $form->number('reward', __('Reward'));
        $form->image('image', __('Image'));
        $form->number('order', __('Order'));
//        $form->radio('type', __('Type'))->options([1 => 'Active', 0=> 'Deactive'])->default(1);
        return $form;
    }
}
