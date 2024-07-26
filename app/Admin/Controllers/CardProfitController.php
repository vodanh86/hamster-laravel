<?php

namespace App\Admin\Controllers;

use App\Models\Card;
use App\Models\CardProfit;
use App\Models\Category;
use App\Models\Skin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CardProfitController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Card Profit';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CardProfit());

        $grid->column('id', __('Id'));
        $grid->column('card.name', __('Card Name'));
        $grid->column('level', __('Level'));
        $grid->column('profit', __('Profit'));
        $grid->column('required_card', __('Required Card'))->display(function ($requiredCardId) {
            return UtilsQueryHelper::getCombinedCardById($requiredCardId);
        });
        $grid->column('required_money', __('Required Money'));
        $grid->column('required_short_money', __('Required Short Money'));
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
        $show = new Show(CardProfit::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('card.name', __('Card Name'));
        $show->field('level', __('Level'));
        $show->field('profit', __('Profit'));
        $show->field('required_card', __('Required Card'))->as(function ($requiredCardId) {
            return UtilsQueryHelper::getCombinedCardById($requiredCardId);
        });
        $show->field('required_money', __('Required Money'));
        $show->field('required_short_money', __('Required Short Money'));
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
        $cardProfitOptions = (new UtilsQueryHelper())::getCombinedCard();
        error_log("cardProfitOptions");
        error_log(json_encode($cardProfitOptions));
        $cardProfitOptions->prepend('Không có', 0);
        $cardProfitDefault = $cardProfitOptions->keys()->first();

        $cardOptions = (new UtilsQueryHelper())::getAllCards();
        $cardDefault = $cardOptions->keys()->first();

        $form = new Form(new CardProfit());

        if ($form->isEditing()) {
            $id = request()->route()->parameter('card_profit');

            $card = $form->model()->find($id)->getOriginal("card_id");
            $form->select('card_id', __('Required Card'))->options($cardOptions)->default($card);

            $requiredCard = $form->model()->find($id)->getOriginal("required_card");
            $form->select('required_card', __('Required Card'))->options($cardProfitOptions)->default($requiredCard);
        } else {
            $form->select('card_id', __('Card'))->options($cardOptions)->default($cardDefault);
            $form->select('required_card', __('Required Card'))->options($cardProfitOptions)->default($cardProfitDefault);
        }

        $form->number('level', __('Level'));
        $form->number('profit', __('Profit'));
        $form->number('required_money', __('Required Money'));
        $form->text('required_short_money', __('Required Short Money'));

        return $form;
    }
}
