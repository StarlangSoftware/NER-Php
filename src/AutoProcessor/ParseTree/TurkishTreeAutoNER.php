<?php

namespace olcaytaner\Ner\AutoProcessor\ParseTree;

use olcaytaner\AnnotatedSentence\ViewLayerType;
use olcaytaner\AnnotatedTree\ParseNodeDrawable;
use olcaytaner\AnnotatedTree\ParseTreeDrawable;
use olcaytaner\AnnotatedTree\Processor\Condition\IsTurkishLeafNode;
use olcaytaner\AnnotatedTree\Processor\NodeDrawableCollector;
use olcaytaner\Dictionary\Dictionary\Word;
use Transliterator;

class TurkishTreeAutoNER extends TreeAutoNER
{
    public function __construct()
    {
        parent::__construct(ViewLayerType::TURKISH_WORD);
    }

    /**
     * The method assigns the words "bay" and "bayan" PERSON tag. The method also checks the PERSON gazetteer, and if
     * the word exists in the gazetteer, it assigns PERSON tag. The parent node should have the proper noun tag NNP.
     * @param ParseTreeDrawable $parseTree The tree for which PERSON named entities checked.
     */
    protected function autoDetectPerson(ParseTreeDrawable $parseTree): void
    {
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTurkishLeafNode());
        $leafList = $nodeDrawableCollector->collect();
        foreach ($leafList as $leaf) {
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)) {
                $word = Transliterator::create("tr-Lower")->transliterate($leaf->getLayerData(ViewLayerType::TURKISH_WORD));
                if (Word::isHonorific($word) && $leaf->getParent()->getData()->getName() == "NNP") {
                    $leaf->getLayerInfo()->setLayerData(ViewLayerType::NER, "PERSON");
                }
                $leaf->checkGazetteer($this->personGazetteer, $word);
            }
        }
    }

    /**
     * The method checks the LOCATION gazetteer, and if the word exists in the gazetteer, it assigns the LOCATION tag.
     * @param ParseTreeDrawable $parseTree The tree for which LOCATION named entities checked.
     */
    protected function autoDetectLocation(ParseTreeDrawable $parseTree): void
    {
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTurkishLeafNode());
        $leafList = $nodeDrawableCollector->collect();
        foreach ($leafList as $leaf) {
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)) {
                $word = Transliterator::create("tr-Lower")->transliterate($leaf->getLayerData(ViewLayerType::TURKISH_WORD));
                $leaf->checkGazetteer($this->locationGazetteer, $word);
            }
        }
    }

    /**
     * The method assigns the words "corp.", "inc.", and "co" ORGANIZATION tag. The method also checks the
     * ORGANIZATION gazetteer, and if the word exists in the gazetteer, it assigns ORGANIZATION tag.
     * @param ParseTreeDrawable $parseTree The tree for which ORGANIZATION named entities checked.
     */
    protected function autoDetectOrganization(ParseTreeDrawable $parseTree): void
    {
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTurkishLeafNode());
        $leafList = $nodeDrawableCollector->collect();
        foreach ($leafList as $leaf) {
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)) {
                $word = Transliterator::create("tr-Lower")->transliterate($leaf->getLayerData(ViewLayerType::TURKISH_WORD));
                if (Word::isOrganization($word)) {
                    $leaf->getLayerInfo()->setLayerData(ViewLayerType::NER, "ORGANIZATION");
                }
                $leaf->checkGazetteer($this->organizationGazetteer, $word);
            }
        }
    }

    /**
     * The method checks for the MONEY entities using regular expressions. After that, if the expression is a MONEY
     * expression, it also assigns the previous nodes, which may included numbers or some monetarial texts, MONEY tag.
     * @param ParseTreeDrawable $parseTree The tree for which MONEY named entities checked.
     */
    protected function autoDetectMoney(ParseTreeDrawable $parseTree): void
    {
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTurkishLeafNode());
        $leafList = $nodeDrawableCollector->collect();
        for ($i = 0; $i < count($leafList); $i++) {
            $leaf = $leafList[$i];
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)) {
                $word = Transliterator::create("tr-Lower")->transliterate($leaf->getLayerData(ViewLayerType::TURKISH_WORD));
                $leaf->checkGazetteer($this->locationGazetteer, $word);
                if (Word::isMoney($word)){
                    $leaf->getLayerInfo()->setLayerData(ViewLayerType::NER, "MONEY");
                    $j = $i - 1;
                    while ($j >= 0) {
                        $previous = $leafList[$j];
                        if ($previous instanceof ParseNodeDrawable && $previous->getParent()->getData()->getName() === "CD") {
                            $previous->getLayerInfo()->setLayerData(ViewLayerType::NER, "MONEY");
                        } else {
                            break;
                        }
                        $j--;
                    }
                }
            }
        }
    }

    /**
     * The method checks for the TIME entities using regular expressions. After that, if the expression is a TIME
     * expression, it also assigns the previous texts, which are numbers, TIME tag.
     * @param ParseTreeDrawable $parseTree The tree for which TIME named entities checked.
     */
    protected function autoDetectTime(ParseTreeDrawable $parseTree): void
    {
        $nodeDrawableCollector = new NodeDrawableCollector($parseTree->getRoot(), new IsTurkishLeafNode());
        $leafList = $nodeDrawableCollector->collect();
        for ($i = 0; $i < count($leafList); $i++) {
            $leaf = $leafList[$i];
            if ($leaf instanceof ParseNodeDrawable && !$leaf->layerExists(ViewLayerType::NER)) {
                $word = Transliterator::create("tr-Lower")->transliterate($leaf->getLayerData(ViewLayerType::TURKISH_WORD));
                $leaf->checkGazetteer($this->locationGazetteer, $word);
                if (Word::isDateTime($word)){
                    $leaf->getLayerInfo()->setLayerData(ViewLayerType::NER, "TIME");
                    if ($i > 0) {
                        $previous = $leafList[$i - 1];
                        if ($previous instanceof ParseNodeDrawable && $previous->getParent()->getData()->getName() === "CD") {
                            $previous->getLayerInfo()->setLayerData(ViewLayerType::NER, "TIME");
                        }
                    }
                }
            }
        }
    }
}