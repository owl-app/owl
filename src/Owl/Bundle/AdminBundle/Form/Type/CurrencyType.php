<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyType as BaseCurrencyType;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType as SymfonyCurrencyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Currencies;

final class CurrencyType extends AbstractType
{
    /** @param CurrencyRepositoryInterface<CurrencyInterface> $currencyRepository */
    public function __construct(private readonly CurrencyRepositoryInterface $currencyRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $options = [
                'label' => 'sylius.form.currency.code',
                'choice_loader' => null,
                'autocomplete' => true,
                'placeholder' => 'owl.form.currency.select',
            ];

            $currency = $event->getData();

            if ($currency instanceof CurrencyInterface && null !== $currency->getCode()) {
                $options['disabled'] = true;
                $options['choices'] = [Currencies::getName($currency->getCode()) => $currency->getCode()];
            } else {
                $options['choices'] = array_flip($this->getAvailableCurrencies());
            }

            $form = $event->getForm();
            $form->add('code', SymfonyCurrencyType::class, $options);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_currency';
    }

    public function getParent(): string
    {
        return BaseCurrencyType::class;
    }

    /**
     * @return array<string, string>
     */
    private function getAvailableCurrencies(): array
    {
        $availableCurrencies = Currencies::getNames();

        /** @var CurrencyInterface[] $definedCurrencies */
        $definedCurrencies = $this->currencyRepository->findAll();

        foreach ($definedCurrencies as $currency) {
            unset($availableCurrencies[$currency->getCode()]);
        }

        return $availableCurrencies;
    }
}
