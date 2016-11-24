<?php
namespace RedCircle\ConsoleProcessManagerBundle\EventListener;

use Exception;
use RedCircle\ConsoleProcessManagerBundle\Entity\Call;
use RedCircle\ConsoleProcessManagerBundle\Repository\CallRepository;
use RedCircle\ConsoleProcessManagerBundle\Repository\ProcessRepository;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * @author Mateusz Krysztofiak <mateusz@krysztofiak.net>
 */
class CommandListener
{
    private $processRepository;

    private $callRepository;

    /**
     * @param mixed $processRepository
     * @return CommandListener
     */
    public function setProcessRepository(ProcessRepository $processRepository)
    {
        $this->processRepository = $processRepository;
    }

    /**
     * @param mixed $callRepository
     * @return CommandListener
     */
    public function setCallRepository(CallRepository $callRepository)
    {
        $this->callRepository = $callRepository;
    }


    /**
     * Run on console command start
     *
     * @param ConsoleCommandEvent $event
     * @throws Exception
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        if (!$this->processRepository->createSchemaIfNotExists()) {
            throw new Exception('Cannot create schema for ConsoleProcessManagerBundle');
        }

        $argv = $_SERVER['argv'];
        unset($argv[0]);
        $command = implode(' ', $argv);

        if (!$process = $this->processRepository->findOneByCommand($command)) {
            $commandName = $event->getCommand()->getName();
            $process = $this->processRepository->createProcess($commandName, $command);
        }

        $call = new Call();
        $call->setStatus(Call::STATUS_STARTED);
        $call->setConsolePid($this->getPid());

        $this->processRepository->addCallToProcess($process, $call);

        $process->setLastCall($call);
        $this->processRepository->update($process);

        $_REQUEST['call_id'] = $call->getId();
    }

    /**
     * Run on console command exception
     *
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $call = $this->callRepository->find($_REQUEST['call_id']);

        $call->setStatus(Call::STATUS_FAILED)
            ->setOutput($event->getException());

        $this->callRepository->update($call);

        $this->registerError($call);
    }

    /**
     * Run on console command ends
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $call = $this->callRepository->find($_REQUEST['call_id']);

        if (!$call->getStatus()) {
            $call->setStatus(Call::STATUS_SUCCESS);
        }

        if ($event->getExitCode()) {
            $call->setStatus(Call::STATUS_ABORTED);
            $call->setOutput(sprintf('Command finished with exit code: %s', $event->getExitCode()));

            $this->registerError($call);
        }

        $call->setFinishedAt(new \DateTime());
        $call->setExecutionTime($call->getExecutionTime());
        $this->callRepository->update($call);

        $this->processRepository->countAvgExecutionTime($call->getProcess(), $call->getExecutionTime());
        $this->processRepository->update($call->getProcess());
    }

    /**
     * Returns console command line process ID
     *
     * @return int
     */
    private function getPid()
    {
        return posix_getpid();
    }

    /**
     * @param Call $call
     */
    private function registerError(Call $call)
    {
        $process = $call->getProcess();
        $process->setCallLastErrorTime(new \DateTime());

        $this->processRepository->update($process);
    }
}
