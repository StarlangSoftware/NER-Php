<?php

namespace olcaytaner\Ner\AutoProcessor\ParseTree;

use olcaytaner\AnnotatedSentence\ViewLayerType;
use olcaytaner\AnnotatedTree\ParseTreeDrawable;

class PersianAutoNER extends TreeAutoNER
{

    public function __construct(){
        parent::__construct(ViewLayerType::PERSIAN_WORD);
    }

    /**
     * @inheritDoc
     */
    protected function autoDetectPerson(ParseTreeDrawable $parseTree): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function autoDetectLocation(ParseTreeDrawable $parseTree): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function autoDetectOrganization(ParseTreeDrawable $parseTree): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function autoDetectMoney(ParseTreeDrawable $parseTree): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function autoDetectTime(ParseTreeDrawable $parseTree): void
    {
    }
}