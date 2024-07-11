<?php
namespace Easysite\Library\View;

use Easysite\Library\View\PageElement;
use Easysite\Library\Interface\ViewInterface;
use Exception;

class View implements ViewInterface
{
    public mixed $pageElement;
    private string $layoutDefault;
    private string $view_path;
    function __construct()
    {
        $this->pageElement = new PageElement;
    }
    public function render(string $template, mixed $data = [], bool $layout = true)
    {
        $templatePath = $this->templatePath($template);
        if (!$layout)
            return $this->renderPage($templatePath, $data);
        else {
            $this->pageElement->setContent($this->renderPage($templatePath, $data));
            echo $this->renderPage($this->layoutPath());
        }
    }
    public function renderBlock($template, $data = [])
    {

    }

    private function renderPage($templatePath, $data = []): string
    {
        ob_start();
        include $templatePath;
        $strims = ob_get_contents();
        ob_end_clean();
        return $strims;
    }
    public function setTitle(string $title = '')
    {
        $this->pageElement->setTitle($title);
    }

    public function setScripts(array $scripts = [])
    {
        $this->pageElement->setScripts($scripts);
    }

    public function setStyles(array $styles = [])
    {
        $this->pageElement->setStyles($styles);
    }
    public function setContentLanguages(string $content_languages = '')
    {
        $this->pageElement->setContentLanguages($content_languages);
    }

    public function setKeywords(array $keywords = [])
    {
        $this->pageElement->setKeywords($keywords);
    }

    public function setDescription(array $description = [])
    {
        $this->pageElement->setDescription($description);
    }

    public function setContent(string $content_stream = '')
    {
        $this->pageElement->setContent($content_stream);
    }
    public function setLayout(string $layout = '')
    {
        $this->layoutDefault = $layout;
    }

    public function setViewPath(string $path = '')
    {
        $this->view_path = $path;
    }
    protected function templatePath(string $template): string
    {
        $templatePath = $this->view_path . $template;
        if (!file_exists($templatePath))
            throw new Exception("None template:{$template} ");
        return $templatePath;
    }
    protected function layoutPath(): string
    {
        if (empty($this->layoutDefault))
            throw new Exception("Layout is empty");
        return $this->templatePath($this->layoutDefault);

    }

}