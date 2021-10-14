<?php

namespace App\Services\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Exceptions\InvalidDirective;
use Spatie\Csp\Exceptions\InvalidValueSet;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Basic;

class AppPolicy extends Basic
{
    /**
     * @throws InvalidValueSet
     * @throws InvalidDirective
     */
    public function configure()
    {
        $this
            ->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, Keyword::SELF)
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::SCRIPT, Keyword::SELF)
            ->addDirective(Directive::STYLE, Keyword::SELF)
            ->addDirective(Directive::FONT, Keyword::SELF)

            ->addDirective(Directive::SCRIPT, 'cdn.jsdelivr.net')
            ->addDirective(Directive::STYLE, 'cdn.jsdelivr.net')
            ->addDirective(Directive::STYLE, 'fonts.googleapis.com')
            ->addDirective(Directive::FONT, 'fonts.gstatic.com')
            ->addDirective(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
            ->addDirective(Directive::SCRIPT, Keyword::UNSAFE_EVAL)
            ->addDirective(Directive::STYLE, Keyword::UNSAFE_INLINE)

            ->addDirective(Directive::IMG, 'data:')
            ->addDirective(Directive::IMG, '*.patreonusercontent.com')

            ->addDirective(Directive::SCRIPT, 'js.pusher.com')
            ->addDirective(Directive::CONNECT, 'wss://*.pusher.com')
            ->addDirective(Directive::CONNECT, 'wss://*.pusherapp.com')
            ->addDirective(Directive::CONNECT, '*.pusher.com')
            ->addDirective(Directive::CONNECT, '*.pusherapp.com');
    }
}
