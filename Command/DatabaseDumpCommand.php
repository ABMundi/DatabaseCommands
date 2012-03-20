<?php
namespace Abmundi\DatabaseCommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Command that dumps
 */
class DatabaseDumpCommand extends ContainerAwareCommand
{
    protected $directory;
    protected $filename;
    protected $link;
    protected $toFile;

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
        $this->directory = "app/tmp/dump";
        $this->link = "app/tmp/dump/current.sql.bz2";

        $this->filename = date('YmdHis').'.sql.bz2';
        $this->toFile = $this->directory . '/' . $this->filename;

        $time = new \DateTime();

        if ($this->prepareEnviroment($output)
            && $this->mysqldump($output)
            && $this->createLink($output)
        ) {
            $output->writeln("<info>Dumped in $this->toFile in ". $time->diff($time = new \DateTime())->format('%s seconds').'</info>');
            $output->writeln('<info>MISSION ACCOMPLISHED</info>');
        } else {
            $output->writeln('<error>Nasty error happened :\'-(</error>');
            if ($this->failingProcess instanceOf Process) {
                $output->writeln('<error>%s</error>', $this->failingProcess->getErrorOutput());
            }
        }
    }

    /**
     * Create folder for dump
     * 
     * @param OutputInterface $output
     * 
     * @return boolean 
     */
    protected function prepareEnviroment(OutputInterface $output)
    {
        if (!is_dir($this->directory)) {
            $mkdir = new Process(sprintf('mkdir -p %s', $this->directory));
            $mkdir->run();

            if ($mkdir->isSuccessful()) {
                $output->writeln(sprintf('<info>Directory %s succesfully  created</info>', $this->directory));
                return true;
            }
            $this->failingProcess = $mkdir;
            return false;
        }
        $output->writeln(sprintf('<info>Directory %s already exists</info>', $this->directory));
        return true;
    }

    /**
     * Run MysqlDump
     * 
     * @param OutputInterface $output
     * 
     * @return boolean 
     */
    protected function mysqldump(OutputInterface $output)
    {
        $dbName = $this->getContainer()->getParameter('database_name');
        $dbUser = $this->getContainer()->getParameter('database_user');
        $dbPwd = $this->getContainer()->getParameter('database_password');
        $mysqldump=  new Process(sprintf('mysqldump -u %s --password=%s %s | bzip2 -c > %s', $dbUser, $dbPwd, $dbName, $this->toFile));
        $mysqldump->run();
        if ($mysqldump->isSuccessful()) {
            $output->writeln(sprintf('<info>Database %s dumped succesfully</info>', $dbName));
            return true;
        }
        $this->failingProcess = $mysqldump;
        return false;
    }

    /**
     * Create link to last dump
     * 
     * @param type $output
     * 
     * @return boolean 
     */
    protected function createLink($output)
    {
        $link = new Process(sprintf('ln -f %s %s', $this->toFile, $this->link));
        $link->run();
        if ($link->isSuccessful()) {
            $output->writeln(sprintf('<info>Link %s created succesfully</info>', $this->link));
            return true;
        }
        $this->failingProcess = $link;
        return false;
    }
}