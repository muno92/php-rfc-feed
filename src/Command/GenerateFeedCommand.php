<?php

namespace App\Command;

use App\Service\FeedGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rfc:generate-feed',
    description: 'Generate Atom feed from RFC activities in database',
)]
class GenerateFeedCommand extends Command
{
    public function __construct(
        private readonly FeedGenerator $feedGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file path', 'feed.xml')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of activities to include', 10)
            ->setHelp('This command generates an Atom feed from the latest RFC activities stored in the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $outputPath = $input->getOption('output');
        $limit = (int) $input->getOption('limit');
        
        $io->title('Generating Atom Feed');
        
        try {
            $feedContent = $this->feedGenerator->generateFeed($limit);
            
            if (file_put_contents($outputPath, $feedContent) === false) {
                $io->error(sprintf('Failed to write feed to file: %s', $outputPath));
                return Command::FAILURE;
            }
            
            $io->success(sprintf('Atom feed generated successfully: %s', $outputPath));
            $io->note(sprintf('Included %d latest activities', $limit));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error(sprintf('Error generating feed: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
