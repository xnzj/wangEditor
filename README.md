wangEditor extension for laravel-admin
======

## 请使用 v1.2.2
## 请使用 v1.2.2
## 请使用 v1.2.2

## 修改了 render 方法
```php
// Editor.php
    public function render()
    {
        $this->id = str_replace('.', '', $this->id . microtime(true));

        $config = (array) WangEditor::config('config');

        $config = json_encode(array_merge([
            'zIndex'              => 0,
            'uploadImgShowBase64' => true,
        ], $config, $this->options));

        $token = csrf_token();

        $this->script = <<<EOT
if (typeof index !== 'undefined') {
    var id = '$this->id' + index;
    var id_selector = '#' + id;
    var input_id = 'input-' + id;
    var input_id_selector = '#' + input_id;

    $("div[class^='has-many']").each(function(){
        if ($(this).hasClass('fields-group')){
            $(this).find("label[for='$this->id']").attr('for', id);
            $(this).find("div[id='$this->id']").attr('id', id);
            $(this).find("input[id='input-$this->id']").attr('id', input_id);
        }
    })
    if ($(id_selector).attr('initialized')) {
        return;
    }

    var E = window.wangEditor
    var editor = new E(id_selector);
    
    editor.customConfig.uploadImgParams = {_token: '$token'}
    
    Object.assign(editor.customConfig, {$config})
    
    editor.customConfig.onchange = function (html) {
        console.log(input_id_selector);
        $(input_id_selector).val(html);
    }
    editor.create();
    
    $(id_selector).attr('initialized', 1);
} else {
    if ($('#{$this->id}').attr('initialized')) {
        return;
    }
    var E = window.wangEditor
    var editor = new E('#{$this->id}');
    
    editor.customConfig.uploadImgParams = {_token: '$token'}
    
    Object.assign(editor.customConfig, {$config})
    
    editor.customConfig.onchange = function (html) {
        console.log('#input-$this->id');
        $('#input-$this->id').val(html);
    }
    editor.create();
    
    $('#{$this->id}').attr('initialized', 1);
}
EOT;
        return parent::render();
    }
```

这是一个`laravel-admin`扩展，用来将`wangEditor`集成进`laravel-admin`的表单中

laravel-admin | extension
---- | ---
1.x | 1.x
2.x |2.x

## 安装

```bash
// laravel-admin 1.x
composer require "xnzj/wang-editor-fix:1.*"
```

然后
```bash
php artisan vendor:publish --tag=laravel-admin-wangEditor
```

## 配置

在`config/admin.php`文件的`extensions`，加上属于这个扩展的一些配置
```php

    'extensions' => [

        'wang-editor' => [
        
            // 如果要关掉这个扩展，设置为false
            'enable' => true,
            
            // 编辑器的配置
            'config' => [
                
            ]
        ]
    ]

```

编辑器的配置可以到[wangEditor文档](https://www.kancloud.cn/wangfupeng/wangeditor3/335776)找到，比如配置上传图片的地址[上传图片](https://www.kancloud.cn/wangfupeng/wangeditor3/335782)

```php
    'config' => [
        // `/upload`接口用来上传文件，上传逻辑要自己实现，可参考下面的`上传图片`
        'uploadImgServer' => '/upload'
    ]
```

## 使用

在form表单中使用它：
```php
$form->editor('content');
```

## 上传图片

图片上传默认使用base64格式化后与文本内容一起存入数据库，如果要上传图片到本地接口，那么下面是这个接口对应的action代码示例：

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

    public function upload(Request $request)
    {
        $urls = [];

        foreach ($request->file() as $file) {
            $urls[] = Storage::url($file->store('images'));
        }

        return [
            "errno" => 0,
            "data"  => $urls,
        ];
    }

> **Note:** 配置路由指向这个action，存储的disk配置在`config/filesystem.php`中，这个需参考laravel官方文档。

## 支持

如果觉得这个项目帮你节约了时间，不妨支持一下;)

![-1](https://cloud.githubusercontent.com/assets/1479100/23287423/45c68202-fa78-11e6-8125-3e365101a313.jpg)

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
