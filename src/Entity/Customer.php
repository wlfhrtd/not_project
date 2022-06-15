<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Uploadable]
class Customer
{
    // createdAt, updatedAt
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $middleName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $documentFilename;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     */
    #[UploadableField(mapping: "customer_document", fileNameProperty: "documentFilename")]
    private $documentFile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $apartment;

    #[ORM\Column(type: 'string', length: 255)]
    private $buildingNumber;

    #[ORM\ManyToOne(targetEntity: Street::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $info;

    const STATUS_CUSTOMER_NEW = 'new_customer';
    const STATUS_CUSTOMER_ACTIVE = 'active';
    const STATUS_CUSTOMER_DISABLED = 'disabled';

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
    private $orders;

    public function __construct()
    {
        $this->status = self::STATUS_CUSTOMER_NEW;
        $this->documentFile = null;
        $this->orders = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
    }

    public function toLongString(): string
    {
        return $this->lastName . ' ' . $this->firstName . ' ' . $this->middleName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // php ucfirst() doesn't work with RU(and other many-bytes) encoding; otherwise use ucfirst(), lcfirst() etc
    private function mb_ucfirst($text) {
        mb_internal_encoding("UTF-8");
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = trim(self::mb_ucfirst($lastName));

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = trim(self::mb_ucfirst($firstName));

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = trim(self::mb_ucfirst($middleName));

        return $this;
    }

    public function getDocumentFilename(): ?string
    {
        return $this->documentFilename;
    }

    public function setDocumentFilename(?string $documentFilename): self
    {
        $this->documentFilename = $documentFilename;

        return $this;
    }

    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $documentFile
     */
    public function setDocumentFile(File|UploadedFile $documentFile = null): void
    {
        $this->documentFile = $documentFile;

        if (null !== $documentFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    public function setApartment(string $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(string $buildingNumber): self
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    public function getStreet(): ?Street
    {
        return $this->street;
    }

    public function setStreet(?Street $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }
}
