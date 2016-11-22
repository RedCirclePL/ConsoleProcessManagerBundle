<?php

namespace RedCircle\ConsoleProcessManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Call
 *
 * @ORM\Table(name="console_manager_call")
 * @ORM\Entity(repositoryClass="RedCircle\ConsoleProcessManagerBundle\Repository\CallRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Mateusz Krysztofiak <mateusz@krysztofiak.net>
 */
class Call
{
    const STATUS_STARTED = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_ABORTED = 3;
    const STATUS_RESOLVED = 4;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="console_pid", type="integer")
     */
    private $consolePid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_time", type="integer", nullable=true)
     */
    private $executionTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    private $output;

    /**
     * @ORM\ManyToOne(targetEntity="RedCircle\ConsoleProcessManagerBundle\Entity\Process", inversedBy="calls")
     * @ORM\JoinColumn(name="process_id", referencedColumnName="id")
     */
    private $process;


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
     * @param int $consolePid
     * @return Call
     */
    public function setConsolePid($consolePid)
    {
        $this->consolePid = $consolePid;
        return $this;
    }

    /**
     * @return int
     */
    public function getConsolePid()
    {
        return $this->consolePid;
    }

    /**
     * @param Process $process
     * @return $this
     * @internal param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
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
     * Set finishedAt
     *
     * @param \DateTime $finishedAt
     * @return Call
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set executionTime
     *
     * @param $executionTime
     * @return Call
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = $executionTime;

        return $this;
    }

    /**
     * Get executionTime
     *
     * @return integer
     */
    public function getExecutionTime()
    {
        if(!$this->getFinishedAt()) {
            $now = new \DateTime();
            return $now->getTimestamp() - $this->getCreatedAt()->getTimestamp();
        } else {
            return $this->getFinishedAt()->getTimestamp() - $this->getCreatedAt()->getTimestamp();
        }
    }

    /**
     * Get executionTime in format H:i:s
     *
     * @return string
     */
    public function getExecutionTimeToString()
    {
        return gmdate('H:i:s', $this->getExecutionTime());
    }

    /**
     * Returns proportion between execution time and avg execution time
     *
     * @return float
     */
    public function getExecutionTimeProportion()
    {
        if ($this->getExecutionTime() && $this->getProcess()->getAvgExecutionTime()) {
            return round($this->getExecutionTime() / $this->getProcess()->getAvgExecutionTime(), 2);
        } elseif ($this->getExecutionTime()) {
            return $this->getExecutionTime();
        } elseif ($this->getProcess()->getAvgExecutionTime()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Call
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $params
     * @return Call
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $output
     * @return Call
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getProcess()->getCommand();
    }
}
