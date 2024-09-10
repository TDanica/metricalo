<?php

namespace App\Command;

use App\Service\PaymentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentCommand extends Command
{
    protected static $defaultName = 'app:example';
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process a payment')
            ->addArgument('provider', InputArgument::REQUIRED, 'The payment provider (aci|shift4)')
            ->addArgument('amount', InputArgument::REQUIRED, 'The amount to charge')
            ->addArgument('currency', InputArgument::REQUIRED, 'The currency')
            ->addArgument('card_number', InputArgument::REQUIRED, 'The card number')
            ->addArgument('exp_year', InputArgument::OPTIONAL, 'The card expiry year')
            ->addArgument('exp_month', InputArgument::OPTIONAL, 'The card expiry month')
            ->addArgument('cvv', InputArgument::OPTIONAL, 'The card CVV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $input->getArgument('provider');
        $amount = $input->getArgument('amount');
        $currency = $input->getArgument('currency');
        $cardNumber = $input->getArgument('card_number');
        $expYear = $input->getArgument('exp_year') ?: null;
        $expMonth = $input->getArgument('exp_month') ?: null;
        $cvv = $input->getArgument('cvv') ?: null;

        if (!in_array($provider, ['aci', 'shift4'])) {
            $output->writeln('Error: Invalid payment provider.');
            return Command::FAILURE;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            $output->writeln('Error: Invalid amount.');
            return Command::FAILURE;
        }

        try {
            $response = $this->paymentService->processPayment($provider, [
                'amount' => $amount,
                'currency' => $currency,
                'card_number' => $cardNumber,
                'exp_year' => $expYear,
                'exp_month' => $expMonth,
                'cvv' => $cvv,
            ]);

            $output->writeln(json_encode($response));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
