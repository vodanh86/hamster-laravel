<?php

namespace App\Admin\Controllers;

use App\Models\Skin;
use App\Models\UserEarn;
use App\Models\UserSkin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SkinController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Skin';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Skin());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('image', __('Image'))->image();
        $grid->column('price', __('Price'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('required_level', __('Required level'));

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
        $show = new Show(Skin::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('required_level', __('Required level'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Skin());
        if ($form->isEditing()) {
            $id = request()->route()->parameter('skin');

        }
        $form->text('name', __('Name'));
        $form->text('description', __('Description'));
//        $form->image('image', __('Image'))->move("images/skin");
        $form->image('image', __('Image'))->move("images/skin")->thumbnail([
            'small' => [150, 150],
        ]);
        $form->number('price', __('Price'));
        $form->number('required_level', __('Required level'));

        if (!$form->isEditing()) {
            //insert form
            $form->saved(function ($form){
                $id=$form->model()->id ;
                $users=(new UtilsQueryHelper())::getAllUsers();
                if(count($users)!==0) {
                    foreach ($users as $user) {
                        $userSkin = new UserSkin();
                        $userSkin->user_id = $user->id;
                        $userSkin->skin_id = $id;
                        $userSkin->is_purchased = ConstantHelper::STATUS_IN_ACTIVE;
                        $userSkin->save();
                    }
                }
            });
        }
        return $form;
    }
}
