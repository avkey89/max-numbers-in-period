<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\MaxNumberOfSurfersInPeriodService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MaxNumberOfSurfersInPeriodCommand extends Command
{
    protected static $defaultName = 'app:max-surfers-period:show';
    private MaxNumberOfSurfersInPeriodService $maxNumberOfSurfersInPeriodService;

    public function __construct(MaxNumberOfSurfersInPeriodService $maxNumberOfSurfersInPeriodService)
    {
        $this->maxNumberOfSurfersInPeriodService = $maxNumberOfSurfersInPeriodService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Определяет максимальное количество сёрферов в момент времени.')
            ->setHelp('Позволяет определить максимальное количество сёрферов, одновременно находящихся на сайте за определённый промежуток времени.')
            ->addArgument('minDate', InputArgument::REQUIRED, 'Укажите начальную дату периода. В формате "YYYY-MM-DD H:i:s"')
            ->addArgument('maxDate', InputArgument::REQUIRED, 'Укажите конечную дату периода. В формате "YYYY-MM-DD H:i:s"')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->maxNumberOfSurfersInPeriodService->handler($input->getArgument('minDate'), $input->getArgument('maxDate'));

        if ($response["status"] == "error") {
            $io->warning($response["message"]);
            return 1;
        }

        $io->success('Одновременно посетителей: ' . $response["value"]);
        $io->success('Command executed!');

        return 0;
    }
}