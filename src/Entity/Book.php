<?php
// api/src/Entity/Book.php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** A book. */
#[ORM\Entity]
#[ApiResource]
class Book
{
    /** The ID of this book. */
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    /** The ISBN of this book (or null  doesn't have one). */
    #[ORM\Column(nullable: true)]
    public ?string $isbn = null;

    /** The title of this book. */
    #[ORM\Column]
    public string $title = '';

    /** The description of this book. */
    #[ORM\Column(type: 'text')]
    public string $description = '';

    /** The publication date of this book. */
    #[ORM\Column]
    public ?\DateTimeImmutable $publicationDate = null;

    /** @var Review[] Available reviews for this book. */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'book', cascade: ['persist', 'remove'])]
    public iterable $reviews;


    // Mối quan hệ ManyToOne với Author
    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)] // khóa ngoaị khoong thể null
    public ?Author $author = null;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}