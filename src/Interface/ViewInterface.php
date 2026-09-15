<?php
namespace Easysite\Library\Interface;

interface ViewInterface
{
    /**
     * Render html page
     * @param string $template
     * @param mixed $data
     * @param bool $layout
     * @return void
     */
    public function render(string $template, mixed $data = [], bool $layout = true);

    /**
     * Render html block
     * @param string $template
     * @param mixed $data
     * @return void
     */
    public function renderBlock(string $template, mixed $data = []);

    /**
     * Set title for page
     * @param string $title
     * @return void
     */
    public function setTitle(string $title = '');

    /**
     * Set url for js scripts
     * @param array $scripts
     * @return void
     */
    public function setScripts(array $scripts = []);

    /**
     * Set url for  styles
     * @param array $styles
     * @return void
     */
    public function setStyles(array $styles = []);

    /**
     * Set content languages
     * @param string $content_languages
     * @return void
     */
    public function setContentLanguages(string $content_languages = '');

    /**
     * Set keywords
     * @param array $keywords
     * @return void
     */
    public function setKeywords(array $keywords = []);

    /**
     * Set description
     * @param array $description
     * @return void
     */
    public function setDescription(array $description = []);

    /**
     * Set page content
     * @param string $content_stream
     * @return void
     */
    public function setContent(string $content_stream = '');

    /**
     * Set layout
     * @param string $layout
     * @return void
     */
    public function setLayout(string $layout = '');

    /**
     * Set view path
     * @param string $path
     * @return void
     */
    public function setViewPath(string $path = '');

    /**
     * get config
     * @return void
     */
    public function getConfig();


}