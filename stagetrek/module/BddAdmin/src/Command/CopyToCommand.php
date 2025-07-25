<?php

namespace BddAdmin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Unicaen\BddAdmin\Command\CopyToCommand as BddAdminCopyTo;

/**
 * Pour ne pas pouvoir faire de copyToParErreur
 * TODO : mettre un parametre dans la config permettant de le réactiver temporairement ou un paramétre -f pour le forcer
 */
class CopyToCommand extends BddAdminCopyTo
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io  = new SymfonyStyle($input, $output);
        $io->error("La commande copy-to a été désactivée pour des raison de sécurité");
        return Command::INVALID;
    }
}