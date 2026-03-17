# Named Entity Recognition

## Task Definition

In named entity recognition, one tries to find the strings within a text that correspond to proper names (excluding TIME and MONEY) and classify the type of entity denoted by these strings. The problem is difficult partly due to the ambiguity in sentence segmentation; one needs to extract which words belong to a named entity, and which not. Another difficulty occurs when some word may be used as a name of either a person, an organization or a location. For example, Deniz may be used as the name of a person, or - within a compound - it can refer to a location Marmara Denizi 'Marmara Sea', or an organization Deniz Taşımacılık 'Deniz Transportation'.

The standard approach for NER is a word-by-word classification, where the classifier is trained to label the words in the text with tags that indicate the presence of particular kinds of named entities. After giving the class labels (named entity tags) to our training data, the next step is to select a group of features to discriminate different named entities for each input word.

[<sub>ORG</sub> Türk Hava Yolları] bu [<sub>TIME</sub> Pazartesi'den] itibaren [<sub>LOC</sub> İstanbul] [<sub>LOC</sub> Ankara] hattı için indirimli satışlarını [<sub>MONEY</sub> 90 TL'den] başlatacağını açıkladı.

[<sub>ORG</sub> Turkish Airlines] announced that from this [<sub>TIME</sub> Monday] on it will start its discounted fares of [<sub>MONEY</sub> 90TL] for [<sub>LOC</sub> İstanbul] [<sub>LOC</sub> Ankara] route.

See the Table below for typical generic named entity types.

|Tag|Sample Categories|
|---|---|
|PERSON|people, characters|
|ORGANIZATION|companies, teams|
|LOCATION|regions, mountains, seas|
|TIME|time expressions|
|MONEY|monetarial expressions|

## Data Annotation

### Preparation

1. Collect a set of sentences to annotate. 
2. Each sentence in the collection must be named as xxxx.yyyyy in increasing order. For example, the first sentence to be annotated will be 0001.train, the second 0002.train, etc.
3. Put the sentences in the same folder such as *Turkish-Phrase*.
4. Build the [Java](https://github.com/starlangsoftware/NER) project and put the generated sentence-ner.jar file into another folder such as *Program*.
5. Put *Turkish-Phrase* and *Program* folders into a parent folder.

### Annotation

1. Open sentence-ner.jar file.
2. Wait until the data load message is displayed.
3. Click Open button in the Project menu.
4. Choose a file for annotation from the folder *Turkish-Phrase*.  
5. For each word in the sentence, click the word, and choose approprite entity tag from PERSON, ORGANIZATION, LOCATION, TIME, or MONEY tags.
6. Click one of the next buttons to go to other files.

## Classification DataSet Generation

After annotating sentences, you can use [DataGenerator](https://github.com/starlangsoftware/DataGenerator-CS) package to generate classification dataset for the Named Entity Recognition task.

## Generation of ML Models

After generating the classification dataset as above, one can use the [Classification](https://github.com/starlangsoftware/Classification-CS) package to generate machine learning models for the Named Entity Recognition task.

Simple Web Interface
============
[Link 1](http://104.247.163.162/nlptoolkit/turkish-named-entity-recognition.html) [Link 2](https://starlangsoftware.github.io/nlptoolkit-web-simple/turkish-named-entity-recognition.html)

Annotated Datasets
============
[Penn-Treebank](http://104.247.163.162/nlptoolkit/turkish-ner1.html)

Video Lectures
============

[<img src=https://github.com/StarlangSoftware/NER/blob/master/video.jpg width="50%">](https://youtu.be/4pxdvP_Rfd8)

For Developers
============

You can also see [Java](https://github.com/starlangsoftware/NER), [Python](https://github.com/starlangsoftware/NER-Py), [Cython](https://github.com/starlangsoftware/NER-Cy), [Swift](https://github.com/starlangsoftware/NER-Swift), [Js](https://github.com/starlangsoftware/NER-Js), [C#](https://github.com/starlangsoftware/NER-CS), or [C++](https://github.com/starlangsoftware/NER-CPP) repository.

For Contibutors
============

### composer.json file

1. autoload is important when this package will be imported.
```
  "autoload": {
    "psr-4": {
      "olcaytaner\\WordNet\\": "src/"
    }
  },
```
2. Dependencies should be maximum (not only direct but also indirect references should also be given), everything directly in the code should be given here.
```
  "require-dev": {
    "phpunit/phpunit": "11.4.0",
    "olcaytaner/dictionary": "1.0.0",
    "olcaytaner/xmlparser": "1.0.1",
    "olcaytaner/morphologicalanalysis": "1.0.0"
  }
```

### Data files
1. Add data files to the project folder. Subprojects should include all data files of the parent projects.

### Php files

1. Do not forget to comment each function.
```
    /**
     * Returns true if specified semantic relation type presents in the relations list.
     *
     * @param SemanticRelationType $relationType element whose presence in the list is to be tested
     * @return bool true if specified semantic relation type presents in the relations list
     */
    public function containsRelationType(SemanticRelationType $relationType): bool{
        foreach ($this->relations as $relation){
            if ($relation instanceof SematicRelation && $relation->getRelationType() == $relationType){
                return true;
            }
        }
        return false;
    }
```
2. Function names should follow caml case.
```
    public function getRelation(int $index): Relation{
```
3. Write getter and setter methods.
```
    public function getOrigin(): ?string
    public function setName(string $name): void
```
4. Use standard javascript test style by extending the TestCase class. Use setup when necessary.
```
class WordNetTest extends TestCase
{
    private WordNet $turkish;

    protected function setUp(): void
    {
        ini_set('memory_limit', '450M');
        $this->turkish = new WordNet();
    }

    public function testSize()
    {
        $this->assertEquals(78327, $this->turkish->size());
    }
```
5. Enumerated types should be declared with enum.
```
enum CategoryType
{
    case MATHEMATICS;
    case SPORT;
    case MUSIC;
    case SLANG;
    case BOTANIC;
```
6. If there are multiple constructors for a class, define them as constructor1, constructor2, ..., then from the original constructor call these methods.
```
    public function constructor1(string $path, string $fileName): void
    public function constructor2(string $path, string $extension, int $index): void
    public function __construct(string $path, string $extension, ?int $index = null)
```
7. Use __toString method if necessary to create strings from objects.
```
    public function __toString(): string
```
8. Use xmlparser package for parsing xml files.
```
  $doc = new XmlDocument("../test.xml");
  $doc->parse();
  $root = $doc->getFirstChild();
  $firstChild = $root->getFirstChild();
```
