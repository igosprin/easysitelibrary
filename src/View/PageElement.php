<?php
namespace Easysite\Library\View;
use Easysite\Library\Interface\iPageElement;
class PageElement implements iPageElement
{
    protected string $title;
    protected array $scripts;
    protected array $styles;
    protected string $content_Languages;
    protected array $keywords;
    protected array $description;
    protected string $content_stream;

    public function setTitle(string $title = '')
    {
        $this->title = $title;
    }
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setScripts(array $scripts = [])
    {
        if (is_array($scripts))
            $this->scripts = $scripts;
    }
    public function getScripts(): array
    {
        return $this->scripts;
    }


    public function setStyles(array $styles = [])
    {
        if (is_array($styles))
            $this->styles = $styles;
    }
    public function getStyles(): array
    {
        return $this->styles;
    }


    public function setContentLanguages(string $content_languages = '')
    {
        $this->content_Languages = $content_languages;
    }
    public function getContentLanguages(): string
    {
        return $this->content_Languages;
    }

    public function setKeywords(array $keywords = [])
    {
        $this->keywords = $keywords;
    }
    public function getKeywords(): string
    {
        return implode(',', $this->keywords);
    }

    public function setDescription(array $description = [])
    {
        $this->description = $description;
    }
    public function getDescription(): string
    {
        return implode(',', $this->description);
    }

    public function setContent(string $content_stream = '')
    {
        $this->content_stream = $content_stream;
    }
    public function getContent(): string
    {
        return $this->content_stream;
    }

}