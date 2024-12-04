<?php
namespace Xypp\LimitedRequest\Middleware;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;

class ForumLimit extends RequestLimitMiddlewareBase
{
    public function __construct(SettingsRepositoryInterface $settings, Translator $translator)
    {
        parent::__construct($settings, $translator, "forum");
    }
}