<?php

namespace App\Admin\Controllers;

use App\Models\Boots;
use App\Models\Earn;
use App\Models\UserBoots;
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
        $grid->column('type', __('Type'))->filter('like')->sortable();
        $grid->column('sub_type', __('Sub Type'))->filter('like')->sortable();
        $grid->column('name', __('Name'))->filter('like')->sortable();
        $grid->column('en_name', __('Name(English)'))->filter('like')->sortable();
        $grid->column('required_money', __('Required money'))->filter('like')->sortable();
        $grid->column('required_short_money', __('Required short money'))->filter('like')->sortable();
        $grid->column('image', __('Image'))->image()->sortable();
        $grid->column('level', __('Level'))->filter('like');
        $grid->column('value', __('Value'))->filter('like');
        $grid->column('order', __('Order'))->filter('like');
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
        $show->field('en_name', __('Name(English)'));
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
        $form->text('name', __('Name'))->required();
        $form->text('en_name', __('Name(English)'));
        $form->number('required_money', __('Required money'))->required();
        $form->text('required_short_money', __('Required short money'))->required();
        $form->image('image', __('Image'))->move("images/boosts");
        $form->number('level', __('Level'))->required();
        $form->number('value', __('Value'))->required();
        $form->number('order', __('Order'))->required();

        if (!$form->isEditing()) {
            //insert form
            $form->saved(function ($form){
                $id=$form->model()->id ;
                $users=(new UtilsQueryHelper())::getAllUsers();
                if(count($users)!==0) {
                    foreach ($users as $user) {
                        $userBoots=new UserBoots();
                        $userBoots->user_id=$user->id;
                        $userBoots->boots_id=$id;
                        $userBoots->is_completed = ConstantHelper::STATUS_IN_ACTIVE;

                        $userBoots->save();
                    }
                }
            });
        }
        return $form;
    }
}
