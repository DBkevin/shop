@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-body product-info">
                <div class="row">
                    <div class="col-5"><img src="{{$product->ImageUrl}}" alt="" class="cover img-responsive" /></div>
                    <div class="col-7">
                        <div class="title">{{$product->title}}</div>
                        <div class="price"><label> 价格</label><em>$</em><span>{{$product->price}}</span></div>
                        <div class="sales_and_reviews">
                            <div class="sold_count">累计销量<span class="count">{{$product->sold_count}}</span></div>
                            <div class="review_count">累计评价<span class="count">{{$product->review_count}}</span></div>
                            <div class="rating" title="评分{{$product->rating}}">评分<span
                                    class="count">{{str_repeat('★',floor($product->rating))}}{{str_repeat('☆',5-floor($product->rating))}}</span>
                            </div>
                        </div>
                        <div class="skus">
                            <label for="">选择</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                @foreach($product->skus as $sku)
                                <label class="btn sku-btn" title="{{$sku->description}}" data-price="{{$sku->price}}"
                                    data-stock="{{$sku->stock}}" data-toggle="tooltip" data-placement="buttom">
                                    <input type="radio" name="skus" autocomplete="off"
                                        value="{{$sku->id}}">{{$sku->title}}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="cart_amount"><label for="">数量</label><input type="text"
                                class="form-control form-control-sm" value="1"> <span>件</span> <span
                                class="stock"></span></div>
                        <div class="buttons">
                            @if($favored)
                            <button class="btn btn-danger btn-disfavor">取消收藏</button>
                            @else
                            <button class="btn btn-success btn-favor">❤收藏</button>
                            @endif
                            <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                        </div>
                    </div>
                </div>
                <div class="prdocut-detail">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#product-detail-tab" aria-controls="product-detail-tab" role='tab'
                                data-toggle="tab" aria-selected="true" class="nav-link active">
                                商品详情
                            </a>
                        </li>
                        <li class="nav-item"><a href="#product-reviews-tab" aria-controls="product-reviews-tab"
                                role="tab" data-toggle="tab" aria-selected="false" class="nav-link">用户评价</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="product-detail-tab" role="tabpanel">
                            {!!$product->description!!}
                        </div>
                        <div class="tab-pane" id="product-reviews-tab" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scriptAfterJs')
<script>
    $(document).ready(function (){
        $('[data-toggle="tooltip"]').tooltip({tigger:'hover'});
        $('.sku-btn').click(function (){
            $('.product-info .price span').text($(this).data('price'));
            $('.product-info .stock').text('库存:'+$(this).data('stock')+'件');
        });
        //监听收藏按钮的点击事件
        $(".btn-favor").click(function (){
            //发起一个psot.ajax请求,
            axios.post('{{route('products.favor',['product'=>$product->id])}}')
                .then(function (){
                    swal('操作成功','','success')
                    .then(function(){
                        location.reload();
                    });
                },function(error){
                    //返回401未登陆
                    if(error.response && error.response.status === 401){
                        swal('请先登陆','','error')
                        .then(function (){
                             location.href = '{{ route('login') }}';
                        });
                    }else if(error.response && error.response.data.msg){
                        //其他错误,有错误信息的情况下,
                        swal(error.response.data.msg,'','error');
                    }else{
                        //其它情况应该是系统挂了
                        swal('系统错误','','error');
                    }
                });
        });
        //监听取消收藏事件
        $('.btn-disfavor').click(function(){
            //发起取消请求
            axios.delete('{{route('products.disfavor',['product'=>$product->id])}}')
                .then(function(){
                    swal('操作成功','','success')
                    .then(function() {
                        location.reload();
                    });
                });
        });
        //监听加入购物车
        $('.btn-add-to-cart').click(function(){
            //发起加入请求
            axios.post("{{ route('cart.add')}}",{
                sku_id: $('label.active input[name=skus]').val(),
                amount:$('.cart_amount input').val(),
            })
            .then(function (){
                //请求成功,
                swal('加入购物车成功','','success');
            },function(error){
                //失败
                if(error.response.status===401){
                    swal('请先登陆','','error')
                    .then(function (){
                        location.href='{{route("login")}}';
                    });
                }else if(error.response.status === 422){
                    //http 代表输入校验失败
                    var html='<div>';
                    _.each(error.response.data.errors,function(errors){
                        _.each(errors,function(error){
                            html +=error+"</br>";
                        })
                    });
                    html+='</div>';
                    swal({content:$(html)[0],icon:'error'})
                }else{
                    //其他状态
                    swal('系统错误','','error');
                }
            })
        });
    });

</script>
@stop
@endsection