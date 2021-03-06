<?php

namespace Magenta\Bundle\CBookModelBundle\Entity\Messaging;

use Bean\Component\Organization\IoC\OrganizationAwareInterface;
use Bean\Component\Organization\Model\OrganizationInterface;
use Bean\Component\Thing\Model\ThingInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Magenta\Bundle\CBookModelBundle\Entity\Organisation\IndividualMember;
use Magenta\Bundle\CBookModelBundle\Entity\Organisation\Organisation;

/**
 * @ORM\Entity()
 * @ORM\Table(name="messaging__message")
 */
class Message extends \Bean\Component\Messaging\Model\Message implements OrganizationAwareInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->deliveries = new ArrayCollection();
    }

    public function commitDeliveries()
    {
        $deliveries = [];
        if (in_array($this->status, [self::STATUS_NEW, self::STATUS_DELIVERY_IN_PROGRESS])) {
            $members = $this->organisation->getIndividualMembers();
            /** @var IndividualMember $member */
            foreach ($members as $member) {
                $recipient = $member;
                $delivery = MessageDelivery::createInstance($this, $recipient);
                $deliveries[] = $delivery;
            }
        }

        return $deliveries;
    }

    public function markStatusAsDeliveryInProgress()
    {
        if (self::STATUS_NEW === $this->status) {
            $this->status = self::STATUS_DELIVERY_IN_PROGRESS;
        }
    }

    /**
     * @return IndividualMember|null
     */
    public function getSender(): ?ThingInterface
    {
        return parent::getSender();
    }

    /**
     * @var Organisation|null
     * @ORM\ManyToOne(targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Organisation\Organisation")
     * @ORM\JoinColumn(name="id_organisation", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $organisation;

    /**
     * @var Conversation|null
     * @ORM\ManyToOne(targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Messaging\Conversation", inversedBy="messages")
     * @ORM\JoinColumn(name="id_conversation", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $conversation;

    /**
     * @var IndividualMember|null
     * @ORM\ManyToOne(targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Organisation\IndividualMember", inversedBy="messages")
     * @ORM\JoinColumn(name="id_sender", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $sender;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Magenta\Bundle\CBookModelBundle\Entity\Messaging\MessageDelivery", mappedBy="message")
     */
    protected $deliveries;

    public function getOrganization(): ?OrganizationInterface
    {
        return $this->organisation;
    }

    public function setOrganization(?OrganizationInterface $organization)
    {
        $this->organisation = $organization;
    }

    /**
     * @return Organisation|null
     */
    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    /**
     * @param Organisation|null $organisation
     */
    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }
}
