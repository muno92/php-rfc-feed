<?php

namespace App\Command;

use App\RfcFetcher\LinkExtractor;
use App\RfcFetcher\RfcDetailExtractor;
use App\Service\RfcPersister;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:fetch-rfcs',
    description: 'Fetch and store PHP RFCs',
)]
class FetchRfcsCommand extends Command
{
    private const RFC_HOST = 'https://wiki.php.net';
    private const RFC_PATH = '/rfc';

    public function __construct(
        private readonly LinkExtractor $linkExtractor,
        private readonly RfcDetailExtractor $rfcDetailExtractor,
        private readonly RfcPersister $rfcPersister,
        private readonly HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Fetching PHP RFCs');

        try {
            // Fetch main RFC page
            $io->section('Fetching RFC list');
            $rfcListUrl = self::RFC_HOST . self::RFC_PATH;
            $response = $this->httpClient->request('GET', $rfcListUrl);
            $html = $response->getContent();
            $links = $this->linkExtractor->extract($html, self::RFC_HOST);
            
            $io->info(sprintf('Found %d RFCs', count($links)));
            
            // Fetch details for each RFC
            $io->section('Fetching RFC details');
            $io->progressStart(count($links));
            
            $newOrUpdatedRfcs = 0;
            
            foreach ($links as $link) {
                try {
                    $response = $this->httpClient->request('GET', $link->url);
                    $html = $response->getContent();
                    $rfcDetail = $this->rfcDetailExtractor->extract($html);
                    
                    // Save to database
                    $activity = $this->rfcPersister->saveRfc($link->url, $rfcDetail);
                    
                    if ($activity !== null) {
                        $newOrUpdatedRfcs++;
                    }
                    
                    $io->progressAdvance();
                } catch (\Exception $e) {
                    $io->warning(sprintf(
                        'Error processing RFC "%s": %s (%s)',
                        $link->title,
                        $e->getMessage(),
                        $e->getTraceAsString(),
                    ));
                }
            }
            
            $io->progressFinish();
            
            $io->success(sprintf('Successfully processed %d RFCs, %d new or updated', count($links), $newOrUpdatedRfcs));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
