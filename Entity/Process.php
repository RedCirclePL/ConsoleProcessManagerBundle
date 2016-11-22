<?php

namespace RedCircle\ConsoleProcessManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * ConsoleManagerProcess
 *
 * @ORM\Table(name="console_manager_process")
 * @ORM\Entity(repositoryClass="RedCircle\ConsoleProcessManagerBundle\Repository\ProcessRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Mateusz Krysztofiak <mateusz@krysztofiak.net>
 */
class Process
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="command_name", type="string", length=255)
     */
    private $commandName;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=255)
     */
    private $command;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_counter", type="integer")
     */
    private $executionCounter;

    /**
     * @var string
     *
     * @ORM\Column(name="avg_execution_time", type="integer")
     */
    private $avgExecutionTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="call_error_count", type="integer", nullable = true)
     */
    private $callErrorCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="call_last_error_time", type="datetime", nullable = true)
     */
    private $callLastErrorTime;

    /**
     * @ORM\OneToMany(targetEntity="RedCircle\ConsoleProcessManagerBundle\Entity\Call", mappedBy="process")
     * @ORM\OrderBy({"id" = "asc"})
     */
    private $calls;

    /**
     * @ORM\ManyToOne(targetEntity="RedCircle\ConsoleProcessManagerBundle\Entity\Call", inversedBy="process")
     * @ORM\JoinColumn(name="last_call_id", referencedColumnName="id")
     */
    private $lastCall;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set command name
     *
     * @param string $commandName
     * @return ConsoleManagerProcess
     */
    public function setCommandName($commandName)
    {
        $this->commandName = $commandName;

        return $this;
    }

    /**
     * Get command name
     *
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return ConsoleManagerProcess
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $executionCounter
     * @return Process
     */
    public function setExecutionCounter($executionCounter)
    {
        $this->executionCounter = $executionCounter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExecutionCounter()
    {
        return $this->executionCounter;
    }

    /**
     * @param string $avgExecutionTime
     * @return Process
     */
    public function setAvgExecutionTime($avgExecutionTime)
    {
        $this->avgExecutionTime = $avgExecutionTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvgExecutionTime()
    {
        return $this->avgExecutionTime;
    }

    /**
     * Get $avgExecutionTime in format H:i:s
     *
     * @return string
     */
    public function getAvgExecutionTimeToString()
    {
        return gmdate('H:i:s', $this->avgExecutionTime);
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Call
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Process
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getCallErrorCount()
    {
        return $this->callErrorCount;
    }

    /**
     * @param mixed $callErrorCount
     * @return Process
     */
    public function setCallErrorCount($callErrorCount)
    {
        $this->callErrorCount = $callErrorCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallLastErrorTime()
    {
        return $this->callLastErrorTime;
    }

    /**
     * @param mixed $callLastErrorTime
     * @return Process
     */
    public function setCallLastErrorTime($callLastErrorTime)
    {
        $this->callLastErrorTime = $callLastErrorTime;
        return $this;
    }

    /**
     * @param Call $call
     * @return $this
     */
    public function addCall(Call $call) {
        $this->calls[] = $call;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCalls() {
        return $this->calls;
    }

    /**
     * @return mixed
     */
    public function getLastCall()
    {
        return $this->lastCall;
    }

    /**
     * @param mixed $lastCall
     * @return Process
     */
    public function setLastCall(Call $lastCall)
    {
        $this->lastCall = $lastCall;
        return $this;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getCommand();
    }
}
