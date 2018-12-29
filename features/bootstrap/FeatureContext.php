<?php

use App\DataFixtures\AppFixtures;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class FeatureContext extends RestContext
{
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

    /**
     * @var AppFixtures
     */
    private $fixtures;

    /**
     * @var \Coduo\PHPMatcher\Matcher
     */
    private $matcher;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FeatureContext constructor.
     * @param Request $request
     * @param AppFixtures $fixtures
     * @param EntityManagerInterface $em
     */
    public function __construct(Request $request, AppFixtures $fixtures, EntityManagerInterface $em)
    {
        $this->fixtures = $fixtures;
        $this->em = $em;
        $this->matcher = (new SimpleFactory())->createMatcher();

        parent::__construct($request);
    }

    /**
     * @Given I am authenticated as :user
     * @param string $user
     */
    public function iAmAuthenticatedAs(string $user): void
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            'POST',
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, AppFixtures::PASSWORD)
        );

        $json = json_decode($this->request->getContent(), true);

        $this->assertTrue( isset($json['token']) );
        $token = $json['token'];
        $this->request->setHttpHeader('Authorization', 'Bearer '.$token);
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema(): void
    {
        // Get entity metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        // Drop & Create Schema
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load fixtures + Execute
        $purger = new ORMPurger($this->em);
        $fixturesExecutor = new ORMExecutor(
            $this->em,
            $purger
        );

        $fixturesExecutor->execute([$this->fixtures]);
    }

    /**
     * @Then the JSON matches expected template:
     * @param PyStringNode $json
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();

        $matcherResult = $this->matcher->match($actual, $json->getRaw());
        if(!$matcherResult) {
            dump($actual);
        }

        $this->assertTrue($matcherResult);
    }
}
