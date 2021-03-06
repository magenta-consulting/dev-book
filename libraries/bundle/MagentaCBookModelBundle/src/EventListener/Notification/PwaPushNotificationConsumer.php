<?php

namespace Magenta\Bundle\CBookModelBundle\EventListener\Notification;

use Doctrine\ORM\EntityManager;
use Magenta\Bundle\CBookModelBundle\Entity\System\DataProcessing\DPJob;
use Magenta\Bundle\CBookModelBundle\Entity\System\DataProcessing\DPLog;
use Magenta\Bundle\CBookModelBundle\Service\Organisation\IndividualMemberService;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PwaPushNotificationConsumer implements ConsumerInterface
{
    protected $memberService;
    protected $manager;
    protected $registry;

    public function __construct(EntityManager $manager, RegistryInterface $registry, IndividualMemberService $ms)
    {
        $this->manager = $manager;
        $this->registry = $registry;
        $this->memberService = $ms;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $message = $event->getMessage();

        $dp = $this->registry->getRepository(DPJob::class)->find($message->getValue('job-id'));

        if (!empty($dp) && DPJob::STATUS_PENDING === $dp->getStatus()) {
            try {
                $this->memberService->notifyOneOrganisationIndividualMembers($dp);
            } catch (\Exception $e) {
                $error = new DPLog();
                $error->setName('Exception: '.$e->getFile());
                $error->setLevel(DPLog::LEVEL_ERROR);
                $error->setIndex(null);
                $error->setJob($dp);
                $error->setCode($e->getCode());
                $error->setTrace($e->getTrace());
                $error->setMessage($e->getMessage());
                $dp->setStatus(DPJob::STATUS_PENDING);

                if (!$this->manager->isOpen()) {
                    $this->manager = $this->manager->create(
                        $this->manager->getConnection(),
                        $this->manager->getConfiguration(),
                        $this->manager->getEventManager()
                    );
                }
                $this->manager->persist($dp);
                $this->manager->persist($error);
                $this->manager->flush();
                throw $e;
            }
        }
    }
}
