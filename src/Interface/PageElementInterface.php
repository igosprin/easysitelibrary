<?php
namespace Easysite\Library\Interface;

interface PageElementInterface
{
    
    public function getTitle(): string;

    
    public function getScripts(): array;

    
    public function getStyles(): array;

    
    public function getContentLanguages(): string;

    
    public function getKeywords(): string;

    
    public function getDescription(): string;

    
    public function getContent(): string;



}