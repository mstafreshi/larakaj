<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use ElGigi\CommonMarkEmoji\EmojiExtension;
use Wnx\CommonmarkMarkExtension\MarkExtension;
use Torchlight\Commonmark\V2\TorchlightExtension;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $config = [
            'footnote' => [
                'backref_class'      => 'footnote-backref',
                'backref_symbol'     => '↩',
                'container_add_hr'   => true,
                'container_class'    => 'footnotes',
                'ref_class'          => 'footnote-ref',
                'ref_id_prefix'      => 'fnref:',
                'footnote_class'     => 'footnote',
                'footnote_id_prefix' => 'fn:',
            ],
            'table_of_contents' => [
                'html_class' => 'table-of-contents',
                'position' => 'placeholder',
                'style' => 'bullet',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'normalize' => 'relative',
                'placeholder' => '[TOC]',
            ],
            'table' => [
                'wrap' => [
                    'enabled' => false,
                    'tag' => 'div',
                    'attributes' => [],
                ],
                'alignment_attributes' => [
                    'left'   => ['align' => 'left'],
                    'center' => ['align' => 'center'],
                    'right'  => ['align' => 'right'],
                ],
            ],
            'heading_permalink' => [
                'html_class' => 'headerlink',
                'id_prefix' => 'content',
                'apply_id_to_heading' => true,
                'heading_class' => '',
                'fragment_prefix' => 'content',
                'insert' => 'after',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'title' => 'Permalink',
                'symbol' => '📎', // HeadingPermalinkRenderer::DEFAULT_SYMBOL,
                'aria_hidden' => true,
            ],
        ];

        $this->app->singleton('commonMark', function () use ($config) {
            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new FootnoteExtension());
            $environment->addExtension(new HeadingPermalinkExtension());
            $environment->addExtension(new TableOfContentsExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $environment->addExtension(new TableExtension());
            $environment->addExtension(new EmojiExtension()); // :warning: and many more
            $environment->addExtension(new TorchlightExtension());
            $environment->addExtension(new MarkExtension()); // ==mark==

            // Instantiate the converter engine and start converting some Markdown!
            return new MarkdownConverter($environment);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
           URL::forceScheme('https');
        }
    }
}
