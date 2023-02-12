<?php

namespace App\Entity;

use App\Repository\CurrencyDynamicRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyDynamicRepository::class)]
#[ORM\Table(name: '`currency_dynamic`')]
class CurrencyDynamic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'currency_id', type: 'string')]
    private ?string $CurrencyID = null;

    #[ORM\Column(name: 'date', type: 'date')]
    private DateTime|string|null $Date = null;

    #[ORM\Column(name: 'nominal', type: 'integer')]
    private ?string $Nominal = null;

    #[ORM\Column(name: 'value', type: 'float')]
    private ?string $Value = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCurrencyID(): ?string
    {
        return $this->CurrencyID;
    }

    /**
     * @param string|null $CurrencyID
     */
    public function setCurrencyID(?string $CurrencyID): void
    {
        $this->CurrencyID = $CurrencyID;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->Date;
    }

    /**
     * @param DateTime|string|null $Date
     */
    public function setDate(DateTime|string|null $Date): void
    {
        $this->Date = $Date;
    }

    /**
     * @return string|null
     */
    public function getNominal(): ?string
    {
        return $this->Nominal;
    }

    /**
     * @param string|null $Nominal
     */
    public function setNominal(?string $Nominal): void
    {
        $this->Nominal = $Nominal;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->Value;
    }

    /**
     * @param string|null $Value
     */
    public function setValue(?string $Value): void
    {
        $this->Value = $Value;
    }
}
