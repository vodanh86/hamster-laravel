<?php

namespace App\Admin\Controllers;

use App\Models\Membership;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MembershipController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Membership';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Membership());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('image', __('Image'))->image();
        $grid->column('money', __('Money'));
        $grid->column('short_money', __('Short money'));
        $grid->column('level', __('Level'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Membership::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->image('image', __('Image'));
        $show->field('money', __('Money'));
        $show->field('short_money', __('Short money'));
        $show->field('level', __('Level'));
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
        $form = new Form(new Membership());

        $form->text('name', __('Name'));
        $form->image('image', __('Image'))->move("images/membership");
        $form->number('money', __('Money'));
        $form->text('short_money', __('Short money'));
        $form->number('level', __('Level'));

        return $form;
    }
}
