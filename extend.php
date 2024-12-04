<?php

/*
 * This file is part of xypp/flarum-limited-request.
 *
 * Copyright (c) 2024 小鱼飘飘.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Xypp\LimitedRequest;

use Flarum\Extend;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\Middleware("api"))
        ->add(Middleware\ApiLimit::class),
    (new Extend\Middleware("forum"))
        ->add(Middleware\ForumLimit::class),
];
