<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoriesRepository;
use DateTimeImmutable;


#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Articles>
     */
    #[ORM\ManyToMany(targetEntity: Articles::class, mappedBy: 'category')]
    private Collection $article;

    public function __construct()
    {
        $this->article = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }

    // public function __toString(): string
    // {
    //     return $this->name;
    // }


    #[ORM\PreUpdate] // signifie que la méthode annotée sera appelée juste avant que l'entité soit mise à jour dans la base de données, c'est-à-dire juste avant l'exécution de la requête SQL de mise à jour.
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->article->contains($article)) {
            $this->article->add($article);
            $article->addCategory($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->article->removeElement($article)) {
            $article->removeCategory($this);
        }

        return $this;
    }
}
