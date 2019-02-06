<?php
namespace App\Command;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BlogPostListCommand extends Command
{
    protected static $defaultName = 'blogpost:list';

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
        $this->setDescription('Blog Post List content.')
            ->setHelp('This command allows you to list blog post items.')
        ;

        $this
            ->addOption('number', 'nb', InputOption::VALUE_REQUIRED, 'How many blog post appear ?', 10)
            ->addOption('order',  null, InputOption::VALUE_REQUIRED, 'Which colors do you like?', 'DESC')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('BlogPost List');

        $limit = ( (is_numeric($input->getOption('number')) ? $input->getOption('number') : 10) );
        $order = ( ($input->getOption('order') == 'ASC') ? 'ASC' : 'DESC' );

        $blogPostRepository = $this->entityManager->getRepository(BlogPost::class);
        $blogPostList = $blogPostRepository->findBy([], ['published' => $order], $limit);

        if(empty($blogPostList)) {
            die($io->caution('No BlogPost'));
        }

        $titles = ['ID', 'Title', 'Author', 'Published', 'Content'];
        $rows = [];

        foreach ($blogPostList as $blogPost) {
            $content = $blogPost->getContent();
            if(strlen($content) > 50)
                $content = trim(substr($content, 0, 50)).'...';

            $rows[] = [
                $blogPost->getId(),
                $blogPost->getTitle(),
                $blogPost->getAuthor()->getUsername(),
                $blogPost->getPublished()->format('Y-m-d'),
                $content,
            ];
        }

        $io->table($titles, $rows);
    }
}
