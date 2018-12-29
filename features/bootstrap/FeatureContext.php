<?php

use App\DataFixtures\AppFixtures;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class FeatureContext extends RestContext
{
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

    public function __construct(Request $request, AppFixtures $fixtures, EntityManagerInterface $em)
    {
        $this->fixtures = $fixtures;
        $this->em = $em;
        $this->matcher = (new SimpleFactory())->createMatcher();

        parent::__construct($request);
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
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
}
