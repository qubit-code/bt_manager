<?php
return [
    [
        "group"    => "basics",
        "icon"  => "fa fa-bookmark",
        "title"  => "基础配置",
        "list"  => [
            [
                "type"      => "input",
                "title"     => "站点名称",
                "param"     => [
                    "name"      => "site_name",
                    "value"     => "宝塔管理器",
                ],
                "explain"   => "",
                "require"   => "",
            ],
            [
                "type"      => "input",
                "title"     => "用户初始免费数量",
                "param"     => [
                    "name"      => "free_num",
                    "value"     => "10",
                ],
                "explain"   => "",
                "require"   => "",
            ],
            [
                "type"      => "input",
                "title"     => "接口白名单",
                "param"     => [
                    "name"      => "server_ip",
                    "value"     => "",
                ],
                "explain"   => "",
                "require"   => "",
            ]
        ],
    ]
];