<?php

namespace olcaytaner\Ner\AutoProcessor\ParseTree;

use olcaytaner\AnnotatedSentence\ViewLayerType;
use olcaytaner\AnnotatedTree\ParseNodeDrawable;
use olcaytaner\AnnotatedTree\ParseTreeDrawable;
use olcaytaner\AnnotatedTree\Processor\Condition\IsTransferable;
use olcaytaner\AnnotatedTree\Processor\NodeDrawableCollector;
use olcaytaner\NamedEntityRecognition\AutoNER;
use olcaytaner\ParseTree\ParseTree;

/**
 * Abstract class to detect named entities in a tree automatically. By implementing 5 abstract methods,
 * the class can detect (i) Person, (ii) Location, (iii) Organization, (iv) Money, (v) Time.
 * Each method tries to detect those named entities and if successful, sets the correct named entity for the word.
 * Anything that is denoted by a proper name, i. e., for instance, a person, a location, or an organization,
 * is considered to be a named entity. In addition, named entities also include things like dates, times,
 * or money. Here is a sample text with named entities marked:
 * [$_{ORG}$ Türk Hava Yolları] bu [$_{TIME}$ Pazartesi'den] itibaren [$_{LOC}$ İstanbul] [$_{LOC}$ Ankara]
 * güzergahı için indirimli satışlarını [$_{MONEY}$ 90 TL'den] başlatacağını açıkladı.
 * This sentence contains 5 named entities including 3 words labeled as ORGANIZATION, 2 words labeled as
 * LOCATION, 1 word labeled as TIME, and 1 word labeled as MONEY.
 * */
abstract class TreeAutoNER extends AutoNER
{
    protected ViewLayerType $secondLanguage;

    /**
     * The method should detect PERSON named entities. PERSON corresponds to people or
     * characters. Example: {\bf Atatürk} yurdu düşmanlardan kurtardı.
     * @param ParseTreeDrawable $parseTree The tree for which PERSON named entities checked.
     */
    protected abstract function autoDetectPerson(ParseTreeDrawable $parseTree): void;

    /**
     * The method should detect LOCATION named entities. LOCATION corresponds to regions,
     * mountains, seas. Example: Ülkemizin başkenti {\bf Ankara'dır}.
     * @param ParseTreeDrawable $parseTree The tree for which LOCATION named entities checked.
     */
    protected abstract function autoDetectLocation(ParseTreeDrawable $parseTree): void;

    /**
     * The method should detect ORGANIZATION named entities. ORGANIZATION corresponds to companies,
     * teams etc. Example:  {\bf IMKB} günü 60 puan yükselerek kapattı.
     * @param ParseTreeDrawable $parseTree The tree for which ORGANIZATION named entities checked.
     */
    protected abstract function autoDetectOrganization(ParseTreeDrawable $parseTree): void;

    /**
     * The method should detect MONEY named entities. MONEY corresponds to monetarial
     * expressions. Example: Geçen gün {\bf 3000 TL} kazandık.
     * @param ParseTreeDrawable $parseTree The tree for which MONEY named entities checked.
     */
    protected abstract function autoDetectMoney(ParseTreeDrawable $parseTree): void;

    /**
     * The method should detect TIME named entities. TIME corresponds to time
     * expressions. Example: {\bf Cuma günü} tatil yapacağım.
     * @param ParseTreeDrawable $parseTree The tree for which TIME named entities checked.
     */
    protected abstract function autoDetectTime(ParseTreeDrawable $parseTree): void;

    /**
     * Constructor for the TreeAutoNER. Sets the language for the NER annotation. Currently, the system supports Turkish
     * and Persian.
     * @param ViewLayerType $secondLanguage Language for NER annotation.
     */
    public function __construct(ViewLayerType $secondLanguage){
        $this->secondLanguage = $secondLanguage;
    }

    /**
     * The main method to automatically detect named entities in a tree. The algorithm
     * 1. Detects PERSON(s).
     * 2. Detects LOCATION(s).
     * 3. Detects ORGANIZATION(s).
     * 4. Detects MONEY.
     * 5. Detects TIME.
     * For not detected nodes, the algorithm sets the named entity "NONE".
     * @param ParseTreeDrawable $parseTree The tree for which named entities checked.
     */
    public function autoNER(ParseTreeDrawable $parseTree): void{
        $this->autoDetectPerson($parseTree);
        $this->autoDetectLocation($parseTree);
        $this->autoDetectOrganization($parseTree);
        $this->autoDetectMoney($parseTree);
        $this->autoDetectTime($parseTree);
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTransferable($this->secondLanguage));
        $leafList = $nodeDrawableCollector->collect();
        foreach ($leafList as $leaf){
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)){
                $leaf->getLayerInfo()->setLayerData(ViewLayerType::NER, "NONE");
            }
        }
    }
}