<?php

namespace App\Admin\Controllers;

use App\Models\CommonCodeModel;
use App\Models\SystemConfigModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


class ASystemConfigController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Tham số hệ thống';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new SystemConfigModel());

        $grid->column('group', __('Nhóm'))->filter('like');
        $grid->column('type', __('Thể loại'))->filter('like');
        $grid->column('value', __('Giá trị'));
        $grid->column('description_vi', __('Mô tả tiếng Việt'))->textarea();
        $grid->column('image', __('Hình ảnh'))->image();
        $grid->column('order', __('Sắp xếp'));
        $grid->column('status', __('Trạng thái'))->display(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", 'Status',"grid");
        });
        $grid->column('created_at', __('Ngày tạo'))->display(function ($createdAt) {
            return ConstantHelper::dateFormatter($createdAt);
        });
        $grid->column('updated_at', __('Ngày cập nhật'))->display(function ($updatedAt) {
            return ConstantHelper::dateFormatter($updatedAt);
        });
        $grid->model()->orderBy('created_at', 'asc');
        $grid->disableExport();
//        $grid->fixColumns(0, 0);
        $grid->fixColumns(0, -1);

        $grid->actions(function ($actions) {
            $blockDelete = $actions->row->block_delete;
            if ($blockDelete === 1) {
                $actions->disableDelete();
            }
        });

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
        $show = new Show(SystemConfigModel::findOrFail($id));

        $show->field('group', __('Nhóm'));
        $show->field('type', __('Thể loại'));
        $show->field('value', __('Giá trị'));
        $show->field('description_vi', __('Mô tả tiếng việt'));
        $show->field('order', __('Sắp xếp'));
        $show->field('image', __('Hình ảnh'))->image();

        $show->field('status', __('Trạng thái'))->as(function ($status) {
            return UtilsCommonHelper::statusFormatter($status, "Core", 'Status', "detail");
        });
        $show->field('created_at', __('Ngày tạo'));
        $show->field('updated_at', __('Ngày cập nhật'));

        $blockDelete = $show->getModel()->getOriginal("block_delete");
        if ($blockDelete === 1) {
            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });
        }

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $statusOptions = (new UtilsCommonHelper)::commonCode("Core", "Status", "description_vi", "value");
        $statusDefault = $statusOptions->keys()->first();

        $form = new Form(new SystemConfigModel());
        if ($form->isEditing()) {
            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
            });
            $form->text('group', __('Nhóm'))->disable();
            $form->text('type', __('Thể loại'))->disable();
            $form->text('value', __('Giá trị'))->disable();
        } else {
            $form->text('group', __('Nhóm'))->help('Yêu cầu tên mã nhóm - Ví dụ(Edu)')->required();
            $form->text('type', __('Thể loại'))->help('Yêu cầu tên thể loại - Ví dụ(School)')->required();
            $form->text('value', __('Giá trị'))->help('Yêu cầu giá trị - Ví dụ(1: VIN, 2:FPT)')->required();
        }
        $form->text('description_vi', __('Mô tả tiếng việt'))->required();
        $form->text('description_en', __('Mô tả tiếng anh'))->required();
        $form->image('image', __('Hình ảnh'));
        $form->text('order', __('Sắp xếp'));
        $form->select('block_delete', __('Chặn xoá'))->options(array(0 => 'Không', 1 => 'Có'))->required();
        $form->select('status', __('Trạng thái'))->options($statusOptions)->default($statusDefault)->required();

        return $form;
    }
}
