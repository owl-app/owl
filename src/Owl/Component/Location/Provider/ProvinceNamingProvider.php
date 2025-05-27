<?php

declare(strict_types=1);

namespace Owl\Component\Location\Provider;

use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Webmozart\Assert\Assert;

class ProvinceNamingProvider implements ProvinceNamingProviderInterface
{
    /** @param RepositoryInterface<ProvinceInterface> $provinceRepository */
    public function __construct(private RepositoryInterface $provinceRepository)
    {
    }

    public function getName(ProvinceCodeAwareInterface $address): string
    {
        if (null === $address->getProvinceCode()) {
            return '';
        }

        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getName();
    }

    public function getAbbreviation(ProvinceCodeAwareInterface $address): string
    {
        if (null === $address->getProvinceCode()) {
            return '';
        }

        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getAbbreviation() ?: $province->getName();
    }
}
