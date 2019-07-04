<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品详情';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $on_sale = [
            'on'  => ['value' => true, 'text' => '上架', 'color' => 'primary'],
            'off' => ['value' => false, 'text' => '下架', 'color' => 'default'],
        ];
        $grid->on_sale('商品名称')->switch($on_sale);
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        /* $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));
        $grid->column('image', __('Image'));
        $grid->column('on_sale', __('On sale'));
        $grid->column('rating', __('Rating'));
        $grid->column('sold_count', __('Sold count'));
        $grid->column('review_count', __('Review count'));
        $grid->column('price', __('Price'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        */

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
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
        $form = new Form(new Product);
        $form->text('title', '商品名称')->rules('required');
        $form->image('image', '商品图片')->rules('required|image');
        $form->UEditor('description', '商品描述')->rules('required');
        $form->switch('on_sale', '是否上架')->default(1)->options(['1' => '是', '0' => '否']);

        //添加一对多的关联模型

        $form->hasMany('skus', 'SKU列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU名称')->rules('required');
            $form->text('description', 'SKU名称')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        //定义回调,当模型保存时会触犯
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });
        /*$form->text('title', __('Title'));
        $form->textarea('description', __('Description'));
        $form->image('image', __('Image'));
        $form->switch('on_sale', __('On sale'))->default(1);
        $form->decimal('rating', __('Rating'))->default(5.00);
        $form->number('sold_count', __('Sold count'));
        $form->number('review_count', __('Review count'));
        $form->decimal('price', __('Price'));
        */

        return $form;
    }
}
