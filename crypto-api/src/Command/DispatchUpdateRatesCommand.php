<?php

namespace App\Command;

use App\Message\UpdateExchangeRatesMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:dispatch-update-rates',
    description: 'Dispatches UpdateExchangeRatesMessage to Messenger',
)]
class DispatchUpdateRatesCommand extends Command
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new UpdateExchangeRatesMessage());
        $output->writeln('UpdateExchangeRatesMessage dispatched!');

        return Command::SUCCESS;
    }
}
