<?php

namespace RedCircle\ConsoleProcessManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use RedCircle\ConsoleProcessManagerBundle\Entity\Call;
use RedCircle\ConsoleProcessManagerBundle\Entity\Process;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * ProcessRepository
 */
class ProcessRepository extends EntityRepository
{

    /**
     * @param $commandName
     * @param $command
     * @return Process
     */
    public function createProcess($commandName, $command)
    {
        $process = new Process();
        $process->setCommandName($commandName)
            ->setCommand($command)
            ->setExecutionCounter(0)
            ->setAvgExecutionTime(0);

        $em = $this->getEntityManager();
        $em->persist($process);
        $em->flush();

        return $process;
    }

    /**
     * @param Process $process
     * @return Process
     */
    public function update(Process $process)
    {
        $em = $this->getEntityManager();
        $em->persist($process);
        $em->flush($process);

        return $process;
    }

    /**
     * @param Process $process
     * @param Call $call
     * @return Process
     */
    public function addCallToProcess(Process $process, Call $call)
    {
        $call->setProcess($process);

        $process = $this->increaseExecutionCounter($process);

        $em = $this->getEntitymanager();
        $em->persist($call);
        $em->flush();

        $process->addCall($call);

        return $process;
    }

    /**
     * @param Process $process
     * @return Process
     */
    public function increaseExecutionCounter(Process $process)
    {
        return $process->setExecutionCounter($process->getExecutionCounter() + 1);
    }

    /**
     * @param Process $process
     * @param $newExecutionTime
     * @return Process
     */
    public function countAvgExecutionTime(Process $process, $newExecutionTime)
    {
        if (!$process->getAvgExecutionTime()) {
            return $process->setAvgExecutionTime($newExecutionTime);
        } else {
            return $process->setAvgExecutionTime(
                (($process->getExecutionCounter() - 1) * $process->getAvgExecutionTime() + $newExecutionTime)
                / $process->getExecutionCounter()
            );
        }
    }

    /**
     * @return bool
     */
    public function createSchemaIfNotExists()
    {
        $em = $this->getEntityManager();

        $sm = $em->getConnection()->getSchemaManager();
        if ($sm->tablesExist(['console_manager_process', 'console_manager_call'])) {
            return true;
        }
        $connection = $em->getConnection();

        $createProcess = $connection->prepare("
            CREATE TABLE IF NOT EXISTS console_manager_process (
                id INT AUTO_INCREMENT NOT NULL,
                command_name VARCHAR(255) NOT NULL,
                command VARCHAR(255) NULL,
                execution_counter INT NOT NULL,
                avg_execution_time INT NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NULL,
                PRIMARY KEY (id)
            )  DEFAULT CHARACTER SET UTF8 COLLATE UTF8_UNICODE_CI ENGINE=INNODB;
        ");

        $createCall = $connection->prepare("
            CREATE TABLE IF NOT EXISTS console_manager_call (
                id INT AUTO_INCREMENT NOT NULL,
                process_id INT NOT NULL,
                created_at DATETIME NOT NULL,
                finished_at DATETIME NULL,
                execution_time INT NULL,
                status INT NOT NULL,
                output LONGTEXT NULL,
                PRIMARY KEY (id),
                INDEX fk_console_manager_call_1_idx (process_id ASC),
                CONSTRAINT fk_console_manager_call_1 FOREIGN KEY (process_id)
                    REFERENCES console_manager_process (id)
                    ON DELETE RESTRICT ON UPDATE RESTRICT
            );
        ");

        return $createProcess->execute() && $createCall->execute();

//        $tool = new SchemaTool($em);
//        $classes = array(
//            $em->getClassMetadata('RedCircle\ConsoleProcessManagerBundle\Entity\Process'),
//            $em->getClassMetadata('RedCircle\ConsoleProcessManagerBundle\Entity\Call'),
//        );
//        $schemaSql = $tool->getCreateSchemaSql($classes);
//        $tool->createSchema($classes);
    }
}
