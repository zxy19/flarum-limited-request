<?php
namespace Xypp\LimitedRequest\Middleware;

use Flarum\Settings\SettingsRepositoryInterface;

class ForumLimit extends RequestLimitMiddlewareBase
{
    public function __construct(SettingsRepositoryInterface $settings)
    {
        parent::__construct($settings, "forum");
    }
}