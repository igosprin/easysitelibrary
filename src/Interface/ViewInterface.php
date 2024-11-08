<?php
namespace Easysite\Library\Interface;

interface ViewInterface
{
    public function render(string $template, mixed $data = [], bool $layout = true);

    public function renderBlock(string $template, mixed $data = []);

    public function setTitle(string $title = '');

    public function setScripts(array $scripts = []);

    public function setStyles(array $styles = []);
    
    public function setContentLanguages(string $content_languages = '');

    public function setKeywords(array $keywords = []);

    public function setDescription(array $description = []);

    public function setContent(string $content_stream = '');

    public function setLayout(string $layout = '');

    public function setViewPath(string $path = '');
    public function getConfig();


}