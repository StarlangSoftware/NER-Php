<?php

namespace olcaytaner\Ner\AutoProcessor\Sentence;

use olcaytaner\AnnotatedSentence\AnnotatedSentence;
use olcaytaner\AnnotatedSentence\AnnotatedWord;
use olcaytaner\Dictionary\Dictionary\Word;
use olcaytaner\MorphologicalAnalysis\MorphologicalAnalysis\MorphologicalTag;
use olcaytaner\Ner\AutoProcessor\Sentence\SentenceAutoNER;
use Transliterator;

class TurkishSentenceAutoNER extends SentenceAutoNER
{

    /**
     * The method assigns the words "bay" and "bayan" PERSON tag. The method also checks the PERSON gazetteer, and if
     * the word exists in the gazetteer, it assigns PERSON tag.
     * @param AnnotatedSentence $sentence The sentence for which PERSON named entities checked.
     */
    protected function autoDetectPerson(AnnotatedSentence $sentence): void
    {
        for ($i = 0; $i < $sentence->wordCount(); $i++) {
            $word = $sentence->getWord($i);
            if ($word instanceof AnnotatedWord && $word->getNamedEntityType() == null && $word->getParse() != null) {
                if (Word::isHonorific($word->getName())) {
                    $word->setNamedEntityType("PERSON");
                }
                $word->checkGazetteer($this->personGazetteer);
            }
        }
    }

    /**
     * The method checks the LOCATION gazetteer, and if the word exists in the gazetteer, it assigns the LOCATION tag.
     * @param AnnotatedSentence $sentence The sentence for which LOCATION named entities checked.
     */
    protected function autoDetectLocation(AnnotatedSentence $sentence): void
    {
        for ($i = 0; $i < $sentence->wordCount(); $i++) {
            $word = $sentence->getWord($i);
            if ($word instanceof AnnotatedWord && $word->getNamedEntityType() == null && $word->getParse() != null) {
                $word->checkGazetteer($this->locationGazetteer);
            }
        }
    }

    /**
     * The method assigns the words "corp.", "inc.", and "co" ORGANIZATION tag. The method also checks the
     * ORGANIZATION gazetteer, and if the word exists in the gazetteer, it assigns ORGANIZATION tag.
     * @param AnnotatedSentence $sentence The sentence for which ORGANIZATION named entities checked.
     */
    protected function autoDetectOrganization(AnnotatedSentence $sentence): void
    {
        for ($i = 0; $i < $sentence->wordCount(); $i++) {
            $word = $sentence->getWord($i);
            if ($word instanceof AnnotatedWord && $word->getNamedEntityType() == null && $word->getParse() != null) {
                if (Word::isOrganization($word->getName())) {
                    $word->setNamedEntityType("ORGANIZATION");
                }
                $word->checkGazetteer($this->organizationGazetteer);
            }
        }
    }

    /**
     * The method checks for the MONEY entities using regular expressions. After that, if the expression is a MONEY
     * expression, it also assigns the previous text, which may included numbers or some monetarial texts, MONEY tag.
     * @param AnnotatedSentence $sentence The sentence for which MONEY named entities checked.
     */
    protected function autoDetectMoney(AnnotatedSentence $sentence): void
    {
        for ($i = 0; $i < $sentence->wordCount(); $i++) {
            $word = $sentence->getWord($i);
            $lowerCase = Transliterator::create("tr-Lower")->transliterate($word->getName());
            if ($word instanceof AnnotatedWord && $word->getNamedEntityType() == null && $word->getParse() != null) {
                if (Word::isMoney($lowerCase)) {
                    $word->setNamedEntityType("MONEY");
                    $j = $i - 1;
                    while ($j >= 0) {
                        $previous = $sentence->getWord($j);
                        if ($previous instanceof AnnotatedWord && $previous->getParse() != null &&
                            ($previous->getName() == "amerikan" || $previous->getParse()->containsTag(MorphologicalTag::REAL) ||
                                $previous->getParse()->containsTag(MorphologicalTag::CARDINAL) || $previous->getParse()->containsTag(MorphologicalTag::NUMBER))) {
                            $previous->setNamedEntityType("MONEY");
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
     * @inheritDoc
     */
    protected function autoDetectTime(AnnotatedSentence $sentence): void
    {
    }
}