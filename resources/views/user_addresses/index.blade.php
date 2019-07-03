@extends('layouts.app')
@section('title','收货地址列表')
@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card panel-default">
            <div class="card-header">
                收货地址列表
                <a href="{{ route('user_addresses.create') }}" class="float-right">新增收货地址</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered tabel-striped">
                    <thead>
                        <tr>
                            <th>收货人</th>
                            <th>地址</th>
                            <th>邮编</th>
                            <th>电话</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($addresses as $address)
                        <tr>
                            <td>{{$address->contact_name}}</td>
                            <td>{{$address->full_address}}</td>
                            <td>{{$address->zip}}</td>
                            <td>{{$address->contact_phone}}</td>
                            <td>
                                <a href="{{ route('user_addresses.edit',['user_address'=>$address->id]) }}"
                                    class="btn btn-primary">修改</a>
                                <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">删除</button>
                                @section('scriptAfterJs')
                                    <script>
                                        $(document).ready(function(){
                                            //删除按钮点击事件
                                            $(".btn-del-address").click(function(){
                                                //获取btn上data-id的值
                                                let id=$(this).data('id');
                                                //调用sweetalert
                                                swal({
                                                    title:'你真的要删除么?',
                                                    icon:'warning',
                                                    buttons:['取消','确定'],
                                                    dangerMode:true,
                                                })
                                                .then(function (willDelete){//点击确定后触发
                                                    //判断willDelete的值,点击true
                                                    if(!willDelete){
                                                        return;
                                                    }
                                                    //调用删除
                                                    axios.delete('/user_addresses/'+id)
                                                        .then(function (){
                                                            //请求成功后重新加载页面
                                                            location.reload();
                                                        })
                                                });
                                            });
                                        });
                                    </script>
                                @endsection
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop