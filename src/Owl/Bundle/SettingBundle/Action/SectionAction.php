<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Action;

use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Owl\Bridge\SyliusResource\Controller\AbstractResourceAction;
use Owl\Bridge\SyliusResource\Controller\RedirectHandlerInterface;
use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Owl\Bridge\SyliusResource\Exception\InvalidResponseException;
use Owl\Bundle\SettingBundle\Factory\SettingFormFactoryInterface;
use Owl\Component\Setting\Storage\SettingStorageInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SectionAction extends AbstractResourceAction
{
    public const AJAX_VALIDATION_EVENT = 'owl_setting.ajax_validation';

    public function __construct(
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private SettingFormFactoryInterface $settingFormFactory,
        private SettingStorageInterface $storage,
        private ViewHandlerInterface $viewHandler,
        private RedirectHandlerInterface $redirectHandler,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var RequestConfiguration $configuration */
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $settingSection = $this->getSection($configuration);
        $settings = $this->storage->loadBySection($settingSection);

        $form = $this->settingFormFactory->create($configuration, $this->mapToArray($settings));
        $form->handleRequest($request);

        if ($configuration->isAjaxRequest() && $form->isSubmitted() && !$form->isValid()) {
            try {
                return $this->eventAjaxValidation($configuration, $form);
            } catch (InvalidResponseException $e) {
                throw $e;
            }
        }

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $this->storage->saveValues($settingSection, $form->getData(), $settings);

            return $this->createRedirect($configuration);
        }

        return $this->render($configuration->getTemplate(''), [
            'configuration' => $configuration,
            'form' => $form->createView(),
        ]);
    }

    private function getSection(RequestConfiguration $configuration): string
    {
        $vars = $configuration->getVars();

        if (!isset($vars['setting_section'])) {
            throw new InvalidArgumentException('Section param not exist in vars configuration route');
        }

        return $vars['setting_section'];
    }

    private function mapToArray(array $settings): array
    {
        $mappedValues = [];

        if ($settings) {
            foreach ($settings as $setting) {
                $mappedValues[$setting->getName()] = $setting->getValue();
            }
        }

        return $mappedValues;
    }

    private function createRedirect(RequestConfiguration $configuration): Response
    {
        $view = new View();
        $view->setData([]);
        $view->setStatusCode(Response::HTTP_OK);
        $view->setFormat('json');
        $view->setHeaders($this->redirectHandler->getRedirectHeaders($configuration, null));

        return $this->viewHandler->handle($configuration, $view);
    }
}
