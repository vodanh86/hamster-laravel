<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\Earn;
use App\Models\Skin;
use App\Models\User;
use App\Models\UserEarn;
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
        $form->image('image', __('Image'))->move("images/earns");
        $form->number('order', __('Order'));
//        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();
//        $form->radio('status', __('Trạng thái'))->options([1 => 'Active', 0 => 'InActive'])->required();

        if (!$form->isEditing()) {
           //insert form
            $form->saved(function ($form){
                $id=$form->model()->id ;
                $users=(new UtilsQueryHelper())::getAllUsers();
                if(count($users)!==0) {
                    foreach ($users as $user) {
                        $userEarn=new UserEarn();
                        $userEarn->user_id=$user->id;
                        $userEarn->earn_id=$id;
                        $userEarn->is_completed = ConstantHelper::STATUS_IN_ACTIVE;

                        $userEarn->save();
                    }
                }
            });
        }

        return $form;
    }
}
