<?php
namespace Abmundi\DatabaseCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDumpCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('db:dump')
            ->setDescription('This task dump the database in a file')
        ;
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
        $dump_dir = "app/tmp/dump";
        $link = "app/tmp/dump/current";
        if (!is_dir($dump_dir)) {
            mkdir($dump_dir);
        }
        $filename = date('YmdHis').'.sql.bz2';
        $tofile = "$dump_dir/$filename";
        $db_name = $this->getContainer()->getParameter('database_name');
        $db_user = $this->getContainer()->getParameter('database_user');
        $db_pwd = $this->getContainer()->getParameter('database_password');
        //exec("mysqldump -u $db_user --password=$db_pwd $db_name | bzip2 -c > #{file}");
        $time = new \DateTime();
        if (file_exists($link)) {
            unlink($link);
        }
        exec("mysqldump -u $db_user --password=$db_pwd $db_name | bzip2 -c > $tofile");
        exec("ln -s $tofile $link");
        $output->writeln("Dumped $db_name in $tofile in ". $time->diff($time = new \DateTime())->format('%s seconds'));
    }
}