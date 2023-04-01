<?php
return [
    "basics"    => [
        "icon"  => "fa fa-bookmark",
        "title"  => "基础配置",
        "type"  => ["addon"],
        "list"  => [
            "site_name" => [
                "type"      => "input",
                "title"     => "站点名称",
                "param"     => [
                    "value"     => "宝塔管理器",
                ],
                "explain"   => "",
                "require"   => "",
            ],
            "free_num"  => [
                "type"      => "input",
                "title"     => "用户初始免费数量",
                "param"     => [
                    "value"     => "10",
                ],
                "explain"   => "",
                "require"   => "",
            ],
            "server_ip" => [
                "type"      => "input",
                "title"     => "接口白名单",
                "param"     => [
                    "value"     => "",
                ],
                "explain"   => "",
                "require"   => "",
            ]
        ],
    ]
];