<?php
namespace App\Command;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BlogPostViewCommand extends Command
{
    protected static $defaultName = 'blogpost:view';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ?string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Blog Post View item.')
            ->setHelp('This command allows you to look post items.')
        ;

        $this->addArgument('id', InputArgument::REQUIRED, 'View BlogPost by ID');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $blogPostId = $input->getArgument('id');

        $blogPostRepository = $this->entityManager->getRepository(BlogPost::class);
        $blogPost = $blogPostRepository->find($blogPostId);

        if(empty($blogPost)) {
            die($output->writeln('<error>BlogPost not found.</error>'));
        }

        $attach = [];
        if(!empty($blogPost->getImages()->count())) {
            $attach[] = $blogPost->getImages()->count().' images';
        }
        if(!empty($blogPost->getComments()->count())) {
            $attach[] = $blogPost->getComments()->count().' comments';
        }

        $output->writeln([
            $blogPost->getTitle(),
                'By: '. $blogPost->getAuthor()->getUsername().
                ' on '.$blogPost->getPublished()->format('Y-m-d H:i'),
            'Attach: '. implode(', ', $attach),
            '',
            ''. $blogPost->getContent(),
        ]);
    }
}
