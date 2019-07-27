<?php

namespace App\Handlers;

use Image;

class ImageUploadHandler
{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        //构建存储文件的规则 如:uploads/images/avaratrs/201907/21
        //文件切割能提高查找效率
        $folder_name = "uploads/images/$folder" . date("Ym/d",time());
        //文件夹存储的物理位置,`public_path()`获取的是`public`文件夹的物理路径,
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21///值如/home
        $upload_path = public_path() . '/' . $folder_name;
        //获取文件的后缀名,
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        //拼接文件名,加前缀是为了增加辨识度,前缀可以试相关的模型ID
        //值如:1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . "." . $extension;

        //如果上传的不是图片将终止操作
        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }
        //移动图片到目录
        $file->move($upload_path, $filename);

        //如果限制了图片宽,就进行裁剪
        if ($max_width && $extension != 'gif') {
            //此类中封装的函数,用于裁剪图片
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => $folder_name."/".$filename,
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        //先实例化,参数是文件的磁盘路径
        $image = Image::make($file_path);
        //进行大小的调整
        $image->resize($max_width,null,function($constraint){
            //设定宽是$max_width, 高度自动调整
            $constraint->aspectRatio();
            //防止图片尺寸变大,
            $constraint->upsize();
        });
        //对图片进行保存
        $image->save();
    }
}
