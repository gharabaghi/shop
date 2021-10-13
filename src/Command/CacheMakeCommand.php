<?php
namespace App\Command;

use App\Service\AppCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheMakeCommand extends Command
{
    protected static $defaultName = 'app:cache:make';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     * @var AppCache
     */
    protected $appCache;

    public function __construct(AppCache $appCache)
    {
        parent::__construct();
        $this->appCache = $appCache;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->appCache->cacheAllItems();

        $io->success('All items are cached');

        return Command::SUCCESS;
    }
}
