<?php
namespace  Easysite\Library\Interface;
interface iPageElement
{
    public function setTitle(string $title = '');
    public function getTitle(): string;

    public function setScripts(array $scripts = []);
    public function getScripts(): array;

    public function setStyles(array $scripts = []);
    public function getStyles(): array;

    public function setContentLanguages(string $content_languages = '');
    public function getContentLanguages(): string;

    public function setKeywords(array $keywords = []);
    public function getKeywords(): string;

    public function setDescription(array $description = []);
    public function getDescription(): string;

    public function setContent(string $content_stream = '');
    public function getContent(): string;



}