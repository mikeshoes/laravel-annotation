<?php

    return [
        'is_open'     => env('annotation_open', true), // 是否启用注解
        'file_patten' => '*Controller.php', // 扫描文件配置
        'cache_file'  => 'cache/annotation.php', // 存储缓存地址，在bootstrap文件夹
        'search_path' => ['app/Http/Controllers'], // 扫描文件目录相对路径
        'annotation_prefix' => [
            'log', // 默认有日志注解
        ],
        'callbacks' => [
            'terminate' => [ // 程序terminate时处理
                'log'  => ''
            ],
        ],
    ];