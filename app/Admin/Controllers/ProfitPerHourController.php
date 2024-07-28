<?php

namespace App\Admin\Controllers;

use App\Models\ProfitPerHour;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProfitPerHourController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ProfitPerHour';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProfitPerHour());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('profit_per_hour', __('Profit per hour'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('exchange_id', __('Exchange id'));
        $grid->column('is_active', __('IsActive'));

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
        $show = new Show(ProfitPerHour::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('profit_per_hour', __('Profit per hour'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('exchange_id', __('Exchange id'));
        $show->field('is_active', __('IsActive'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProfitPerHour());

        $form->number('user_id', __('User id'));
        $form->number('profit_per_hour', __('Profit per hour'))->default(1500);
        $form->number('exchange_id', __('Exchange id'));

        return $form;
    }
}
