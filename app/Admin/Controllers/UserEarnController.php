<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Skin;
use App\Models\UserEarn;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserEarnController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User Earn';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserEarn());

        $grid->column('id', __('Id'));
        $grid->column('user.first_name', __('First Name'));
        $grid->column('user.last_name', __('Last Name'));
        $grid->column('earn.name', __('Earn Name'));
        $grid->column('is_completed', __('Is Completed'));

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->model()->orderBy('updated_at', 'asc');
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
        $show = new Show(UserEarn::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.first_name', __('First Name'));
        $show->field('user.last_name', __('Last Name'));
        $show->field('earn.name', __('Earn Name'));
        $show->field('is_completed', __('Is Completed'));

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
        $form = new Form(new UserEarn());

        $form->number('user_id', __('User Id'));
        $form->number('earn_id', __('Earn Id'));
        $form->number('is_completed', __('Is Completed'));

        return $form;
    }
}
