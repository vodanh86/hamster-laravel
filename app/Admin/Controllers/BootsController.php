<?php

namespace App\Admin\Controllers;

use App\Models\Boots;
use App\Models\Earn;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BootsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Boots';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Boots());

        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'));
        $grid->column('sub_type', __('Sub Type'));
        $grid->column('name', __('Name'));
        $grid->column('required_money', __('Required money'));
        $grid->column('required_short_money', __('Required short money'));
        $grid->column('image', __('Image'))->image();
        $grid->column('level', __('Level'));
        $grid->column('value', __('Value'));
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
        $show = new Show(Boots::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('sub_type', __('Sub Type'));
        $show->field('name', __('Name'));
        $show->field('required_money', __('Required money'));
        $show->field('required_short_money', __('Required short money'));
        $show->field('image', __('Image'))->image();
        $show->field('level', __('Level'));
        $show->field('value', __('Value'));
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

        $form = new Form(new Boots());

        if ($form->isEditing()) {
            $id = request()->route()->parameter('boots');
        }

        $form->radio('type', __('Type'))->options([1 => 'Fee', 0 => 'Free'])->required();
        $form->radio('sub_type', __('Sub Type'))->options([0 => 'Energy', 1 => 'Turbo', 2 => 'Multitap', 3 => 'Energy limit']);
        $form->text('name', __('Name'));
        $form->number('required_money', __('Required money'))->required();
        $form->text('required_short_money', __('Required short money'))->required();
        $form->image('image', __('Image'))->move("images/boosts");
        $form->number('level', __('Level'))->required();
        $form->number('value', __('Value'))->required();
        $form->number('order', __('Order'))->required();
        return $form;
    }
}
