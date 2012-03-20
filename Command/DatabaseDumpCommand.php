<?php
namespace Abmundi\DatabaseCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that dumps
 */
class DatabaseDumpCommand extends ContainerAwareCommand
{
    /**
     * This method set name and description 
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('db:dump')
            ->setDescription('This task dump the database in a file');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return integer 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract class is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = "app/tmp/dump";
        $link = "app/tmp/dump/current.sql.bz2";
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        $filename = date('YmdHis').'.sql.bz2';
        $toFile = "$directory/$filename";
        $dbName = $this->getContainer()->getParameter('database_name');
        $dbUser = $this->getContainer()->getParameter('database_user');
        $dbPwd = $this->getContainer()->getParameter('database_password');

        $time = new \DateTime();
        if (file_exists($link)) {
            unlink($link);
        }

        exec("mysqldump -u $dbUser --password=$dbPwd $dbName | bzip2 -c > $toFile");
        exec("ln -f $toFile $link");
        $output->writeln("Dumped $dbName in $toFile in ". $time->diff($time = new \DateTime())->format('%s seconds'));
    }
}