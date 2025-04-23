<?php

namespace App\Factory;

use App\Entity\Review;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Review>
 */
final class ReviewFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Review::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'author' => self::faker()->text(),
            'body' => self::faker()->text(),
            'publicationDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'rating' => self::faker()->numberBetween(1, 32767),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Review $review): void {})
        ;
    }

    protected function getDefaults(): array
    {
        return [
            'author' => self::faker()->name(),
            'body' => self::faker()->text(),
            'book' => lazy(fn() => BookFactory::randomOrCreate()),
            'publicationDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'rating' => self::faker()->numberBetween(0, 5),
        ];
    }
}
