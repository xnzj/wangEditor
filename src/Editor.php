<?php

namespace Encore\WangEditor;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected $view = 'laravel-admin-wangEditor::editor';

    protected static $css = [
        'vendor/laravel-admin-ext/wang-editor/wangEditor-3.0.10/release/wangEditor.css',
    ];

    protected static $js = [
        'vendor/laravel-admin-ext/wang-editor/wangEditor-3.0.10/release/wangEditor.js',
    ];

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
}
