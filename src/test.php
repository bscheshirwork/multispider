<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 18.10.16
 * Time: 9:19
 */
$tree = <<<TXT
Исходная папка (/home/user/testFolder/):
├── 1
│   ├── 1
│   │   ├── file.css
│   │   ├── file.sass
│   │   ├── generated_1366x768.png
│   │   └── preview_1920x1080.png
│   └── 2
│       ├── file.css
│       ├── file.sass
│       ├── generated_1366x768.css
│       └── preview_1920x1080.png
├── 2
│   └── 3
│       ├── file.css
│       ├── file.sass
│       ├── preview_1280x720.png
│       └── preview_1920x1080.png
└── 3
    └── 4
        ├── 7055caf588d0a4bae6901366x7688868c87d7b8.sum
        ├── file.css
        ├── file.sass
        └── preview_1920x1080.png
TXT;

`mkdir /home/user/testFolder`;
`mkdir /home/user/testFolder/1`;
`mkdir /home/user/testFolder/2`;
`mkdir /home/user/testFolder/3`;
`mkdir /home/user/testFolder/1/1`;
`mkdir /home/user/testFolder/1/2`;
`mkdir /home/user/testFolder/2/3`;
`mkdir /home/user/testFolder/3/4`;
`touch /home/user/testFolder/1/1/file.css`;
`touch /home/user/testFolder/1/1/file.sass`;
`touch /home/user/testFolder/1/1/generated_1366x768.png`;
`touch /home/user/testFolder/1/1/preview_1920x1080.png`;
`touch /home/user/testFolder/1/2/file.css`;
`touch /home/user/testFolder/1/2/file.sass`;
`touch /home/user/testFolder/1/2/generated_1366x768.png`;
`touch /home/user/testFolder/1/2/preview_1920x1080.png`;
`touch /home/user/testFolder/2/3/file.css`;
`touch /home/user/testFolder/2/3/file.sass`;
`touch /home/user/testFolder/2/3/preview_1280x720.png`;
`touch /home/user/testFolder/2/3/preview_1920x1080.png`;
`touch /home/user/testFolder/3/4/7055caf588d0a4bae6901366x7688868c87d7b8.sum`;
`touch /home/user/testFolder/3/4/file.css`;
`touch /home/user/testFolder/3/4/file.sass`;
`touch /home/user/testFolder/3/4/preview_1920x1080.png`;
