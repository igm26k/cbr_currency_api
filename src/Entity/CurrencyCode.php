<?php

namespace App\Entity;

use App\Repository\CurrencyCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CurrencyCodeRepository::class)]
#[ORM\Table(name: '`currency_code`')]
#[UniqueEntity(
    fields: ['ID'],
    message: 'Такой ID уже используется',
    errorPath: 'ID'
)]
class CurrencyCode
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $ID = null;

    #[ORM\Column(type: 'string')]
    private ?string $ParentCode = null;

    #[ORM\Column(type: 'string')]
    private ?string $Name = null;

    #[ORM\Column(type: 'string')]
    private ?string $EngName = null;

    #[ORM\Column(type: 'integer')]
    private ?string $Nominal = null;

    /**
     * @return string|null
     */
    public function getID(): ?string
    {
        return $this->ID;
    }

    /**
     * @param string|null $ID
     */
    public function setID(?string $ID): void
    {
        $this->ID = $ID;
    }

    /**
     * @return string|null
     */
    public function getParentCode(): ?string
    {
        return $this->ParentCode;
    }

    /**
     * @param string|null $ParentCode
     */
    public function setParentCode(?string $ParentCode): void
    {
        $this->ParentCode = $ParentCode;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->Name;
    }

    /**
     * @param string|null $Name
     */
    public function setName(?string $Name): void
    {
        $this->Name = $Name;
    }

    /**
     * @return string|null
     */
    public function getEngName(): ?string
    {
        return $this->EngName;
    }

    /**
     * @param string|null $EngName
     */
    public function setEngName(?string $EngName): void
    {
        $this->EngName = $EngName;
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
}
