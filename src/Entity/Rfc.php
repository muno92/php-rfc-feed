<?php

namespace App\Entity;

use App\Repository\RfcRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: RfcRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_TITLE_VERSION', columns: ['title', 'version'])]
class Rfc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $url = null;

    #[ORM\Column(length: 50)]
    private ?string $version = null;

    #[ORM\OneToMany(mappedBy: 'rfc', targetEntity: Activity::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|PersistentCollection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setRfc($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getRfc() === $this) {
                $activity->setRfc(null);
            }
        }

        return $this;
    }

    /**
     * Get the most recent activity
     */
    public function getLatestActivity(): ?Activity
    {
        $criteria = Criteria::create()
            ->orderBy(['createdAt' => Order::Descending])
            ->setMaxResults(1);

        $result = $this->activities->matching($criteria);

        return $result->isEmpty() ? null : $result->first();
    }

    public function statusIsConfirmed(): bool
    {
        return $this->isImplemented();
    }

    public function isImplemented(): bool
    {
        $latestActivity = $this->getLatestActivity();
        return $latestActivity !== null && str_contains(strtolower($latestActivity->getStatus()), 'implemented');
    }
}
