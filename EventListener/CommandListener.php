<?php
namespace RedCircle\ConsoleProcessManagerBundle\EventListener;

use Exception;
use RedCircle\ConsoleProcessManagerBundle\Entity\Call;
use RedCircle\ConsoleProcessManagerBundle\Repository\CallRepository;
use RedCircle\ConsoleProcessManagerBundle\Repository\ProcessRepository;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

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

        $event->stopPropagation();
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

        $this->processRepository->addCallToProcess($process, $call);

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

        $call->setStatus(Call::STATUS_FAIL)
            ->setOutput($event->getException());

        $this->callRepository->update($call);
    }

    /**
     * Run on console command ends
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $call = $this->callRepository->find($_REQUEST['call_id']);

        if(!$call->getStatus()) {
            $call->setStatus(Call::STATUS_SUCCESS);
        }

        if($event->getExitCode()) {
            $call->setStatus(Call::STATUS_FAIL);
            $call->setOutput(sprintf('Command finished with exit code: %s', $event->getExitCode()));
        }

        $call->setFinishedAt(new \DateTime());
        $newExecutionTime = $call->getFinishedAt()->getTimestamp() - $call->getCreatedAt()->getTimestamp();

        $call->setExecutionTime($newExecutionTime);

        $this->processRepository->countAvgExecutionTime($call->getProcess(), $newExecutionTime);
        $this->processRepository->update($call->getProcess());

        $this->callRepository->update($call);
    }
}
